<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Registration;
use App\Services\PaymentTypeResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class PaymentController extends Controller
{
    public function __construct(private PaymentTypeResolver $paymentTypes)
    {
    }

    public function show(Registration $registration): View
    {
        Gate::authorize('view', $registration);

        $registration->load('payment', 'workshop', 'ticket');

        return view('payments.show', [
            'registration' => $registration,
        ]);
    }

    public function pay(Registration $registration): RedirectResponse
    {
        Gate::authorize('view', $registration);

        $payment = $registration->payment;

        if ($payment->isPaid()) {
            return redirect()
                ->route('payments.show', $registration)
                ->with('status', 'Pembayaran sudah lunas.');
        }

        if ($payment->snap_token) {
            return redirect()->away($this->snapRedirectUrl($payment));
        }

        try {
            $this->configureMidtrans();

            $transaction = Snap::createTransaction([
                'transaction_details' => [
                    'order_id' => $payment->order_id,
                    'gross_amount' => $payment->amount,
                ],
            ]);
        } catch (\Exception $e) {
            report($e);

            return back()->with('error', 'Gagal membuat transaksi pembayaran. Silakan coba lagi.');
        }

        $payment->update([
            'snap_token' => $transaction->token,
        ]);

        return redirect()->away($this->snapRedirectUrl($payment));
    }

    public function check(Registration $registration): RedirectResponse
    {
        Gate::authorize('view', $registration);

        $payment = $registration->payment;

        if ($payment->isPaid()) {
            return redirect()
                ->route('payments.show', $registration)
                ->with('status', 'Pembayaran sudah lunas.');
        }

        try {
            $this->configureMidtrans();
            $result = Transaction::status($payment->order_id);
        } catch (\Exception $e) {
            report($e);

            return redirect()
                ->route('payments.show', $registration)
                ->with('error', 'Gagal memeriksa status pembayaran.');
        }

        $sameOrder = $result->order_id === $payment->order_id;
        $sameAmount = (int) round((float) $result->gross_amount) === (int) $payment->amount;

        if (! $sameOrder || ! $sameAmount) {
            return redirect()
                ->route('payments.show', $registration)
                ->with('error', 'Data transaksi tidak sesuai. Silakan hubungi panitia.');
        }

        $this->applyTransactionStatus($payment, $result->transaction_status ?? 'unknown');

        return redirect()
            ->route('payments.show', $registration)
            ->with('status', $payment->fresh()->isPaid() ? 'Pembayaran berhasil.' : 'Pembayaran belum berhasil.');
    }

    public function notification(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'order_id' => ['required', 'string'],
            'status_code' => ['required', 'string'],
            'gross_amount' => ['required', 'string'],
            'signature_key' => ['required', 'string'],
            'transaction_status' => ['required', 'string'],
            'payment_type' => ['nullable', 'string'],
        ]);

        if (! $this->isValidSignature($payload)) {
            abort(403, 'Invalid signature.');
        }

        $payment = Payment::where('order_id', $payload['order_id'])->first();

        if (! $payment) {
            abort(404, 'Payment not found.');
        }

        if ((int) round((float) $payload['gross_amount']) !== (int) $payment->amount) {
            abort(422, 'Amount mismatch.');
        }

        $this->applyTransactionStatus($payment, $payload['transaction_status'], $payload['payment_type'] ?? null);

        return response()->json(['message' => 'OK']);
    }

    private function applyTransactionStatus(Payment $payment, string $transactionStatus, ?string $paymentType = null): void
    {
        DB::transaction(function () use ($payment, $transactionStatus, $paymentType) {
            $isPaid = in_array($transactionStatus, [Payment::STATUS_SETTLEMENT, Payment::STATUS_CAPTURE], true);

            $payment->update([
                'payment_status' => $transactionStatus,
                'payment_type' => $paymentType ?? $payment->payment_type,
                'payment_type_id' => $this->paymentTypes->resolve($paymentType) ?? $payment->payment_type_id,
                'paid_at' => $isPaid ? ($payment->paid_at ?? now()) : $payment->paid_at,
            ]);

            if ($isPaid) {
                $registration = $payment->registration;

                $registration->ticket()->firstOrCreate([], [
                    'ticket_code' => 'TIX-'.strtoupper(Str::ulid()),
                    'status' => 'active',
                    'issued_at' => now(),
                ]);

                $registration->update(['status' => 'paid']);
            }
        });
    }

    private function isValidSignature(array $payload): bool
    {
        $expected = hash('sha512', $payload['order_id'].$payload['status_code'].$payload['gross_amount'].config('services.midtrans.server_key'));

        return hash_equals($expected, $payload['signature_key']);
    }

    private function snapRedirectUrl(Payment $payment): string
    {
        $isProduction = (bool) config('services.midtrans.is_production');
        $base = $isProduction
            ? 'https://app.midtrans.com'
            : 'https://app.sandbox.midtrans.com';

        return $base.'/snap/v2/vtweb/'.$payment->snap_token;
    }

    private function configureMidtrans(): void
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = (bool) config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }
}

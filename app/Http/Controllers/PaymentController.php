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
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class PaymentController extends Controller
{
    public function __construct(private PaymentTypeResolver $paymentTypes) {}

    public function show(Registration $registration): View
    {
        Gate::authorize('view', $registration);

        $registration->load('payment', 'workshop', 'tickets');

        return view('payments.show', [
            'registration' => $registration,
        ]);
    }

    public function pay(Registration $registration): RedirectResponse
    {
        Gate::authorize('view', $registration);

        return $this->startPayment($registration, route('payments.show', $registration));
    }

    public function check(Registration $registration): RedirectResponse
    {
        Gate::authorize('view', $registration);

        return $this->checkPayment($registration, route('payments.show', $registration));
    }

    public function showGuest(Registration $registration): View
    {
        abort_unless($registration->user_id === null, 404);

        $registration->load('payment', 'workshop', 'tickets');

        return view('payments.guest', [
            'registration' => $registration,
            'payUrl' => $this->guestPaymentUrl('guest.payments.pay', $registration),
            'checkUrl' => $this->guestPaymentUrl('guest.payments.check', $registration),
        ]);
    }

    public function payGuest(Registration $registration): RedirectResponse
    {
        abort_unless($registration->user_id === null, 404);

        return $this->startPayment(
            $registration,
            $this->guestPaymentUrl('guest.payments.show', $registration),
        );
    }

    public function checkGuest(Registration $registration): RedirectResponse
    {
        abort_unless($registration->user_id === null, 404);

        return $this->checkPayment(
            $registration,
            $this->guestPaymentUrl('guest.payments.show', $registration),
        );
    }

    private function startPayment(Registration $registration, string $finishUrl): RedirectResponse
    {
        $payment = $registration->payment;

        if ($payment->isPaid()) {
            return redirect()->to($finishUrl)->with('status', 'Pembayaran sudah lunas.');
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
                'callbacks' => [
                    'finish' => $finishUrl,
                ],
            ]);
        } catch (\Exception $e) {
            report($e);

            return redirect()->to($finishUrl)->with('error', 'Gagal membuat transaksi pembayaran. Silakan coba lagi.');
        }

        $payment->update(['snap_token' => $transaction->token]);

        return redirect()->away($this->snapRedirectUrl($payment));
    }

    private function checkPayment(Registration $registration, string $returnUrl): RedirectResponse
    {
        $payment = $registration->payment;

        if ($payment->isPaid()) {
            return redirect()->to($returnUrl)->with('status', 'Pembayaran sudah lunas.');
        }

        try {
            $this->configureMidtrans();
            $result = Transaction::status($payment->order_id);
        } catch (\Exception $e) {
            report($e);

            return redirect()->to($returnUrl)->with('error', 'Gagal memeriksa status pembayaran.');
        }

        $sameOrder = $result->order_id === $payment->order_id;
        $sameAmount = (int) round((float) $result->gross_amount) === (int) $payment->amount;

        if (! $sameOrder || ! $sameAmount) {
            return redirect()->to($returnUrl)->with('error', 'Data transaksi tidak sesuai. Silakan hubungi panitia.');
        }

        $this->applyTransactionStatus($payment, $result->transaction_status ?? 'unknown');

        return redirect()
            ->to($returnUrl)
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
                $registration = Registration::query()
                    ->lockForUpdate()
                    ->findOrFail($payment->registration_id);
                $attendees = $registration->attendees
                    ?? [$registration->guest_name ?? $registration->user?->name ?? 'Peserta'];
                $existingTickets = $registration->tickets()->count();

                foreach (array_slice($attendees, $existingTickets) as $attendeeName) {
                    $registration->tickets()->create([
                        'attendee_name' => $attendeeName,
                        'ticket_code' => 'TIX-'.strtoupper(Str::ulid()),
                        'status' => 'active',
                        'issued_at' => now(),
                    ]);
                }

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

    private function guestPaymentUrl(string $route, Registration $registration): string
    {
        return URL::temporarySignedRoute($route, now()->addDays(7), ['registration' => $registration]);
    }

    private function configureMidtrans(): void
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = (bool) config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }
}

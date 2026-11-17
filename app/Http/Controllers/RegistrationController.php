<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Workshop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class RegistrationController extends Controller
{
    public function store(Request $request, Workshop $workshop): RedirectResponse
    {
        abort_unless($workshop->status === 'published', 404);

        $user = $request->user();

        $existing = $user->registrations()
            ->whereBelongsTo($workshop)
            ->first();

        if ($existing) {
            return redirect()
                ->route('payments.show', $existing)
                ->with('status', 'Anda sudah terdaftar pada workshop ini.');
        }

        $registration = DB::transaction(function () use ($user, $workshop) {
            $registration = $workshop->registrations()->create([
                'user_id' => $user->id,
                'status' => 'pending',
            ]);

            $registration->payment()->create([
                'order_id' => 'WTX-'.strtoupper(Str::ulid()),
                'amount' => $workshop->price,
                'payment_status' => Payment::STATUS_PENDING,
            ]);

            return $registration;
        });

        return redirect()
            ->route('payments.show', $registration)
            ->with('status', 'Pendaftaran berhasil. Silakan selesaikan pembayaran.');
    }

    public function storeGuest(Request $request, Workshop $workshop): RedirectResponse
    {
        abort_unless($workshop->status === 'published', 404);

        $validated = $request->validate([
            'guest_name' => ['required', 'string', 'max:255'],
        ]);

        $registration = DB::transaction(function () use ($validated, $workshop) {
            $registration = $workshop->registrations()->create([
                'guest_name' => $validated['guest_name'],
                'status' => 'pending',
            ]);

            $registration->payment()->create([
                'order_id' => 'WTX-'.strtoupper(Str::ulid()),
                'amount' => $workshop->price,
                'payment_status' => Payment::STATUS_PENDING,
            ]);

            return $registration;
        });

        return redirect()->to(URL::temporarySignedRoute(
            'guest.payments.show',
            now()->addDays(7),
            ['registration' => $registration],
        ))->with('status', 'Pesanan dibuat. Simpan tautan privat ini untuk membuka tiket setelah pembayaran.');
    }
}

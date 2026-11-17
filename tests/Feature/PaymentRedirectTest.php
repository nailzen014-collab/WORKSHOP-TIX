<?php

use App\Models\Payment;
use App\Models\Registration;
use App\Models\User;
use App\Models\Workshop;

test('completed snap payments return to the registration payment page', function () {
    $user = User::factory()->create();
    $workshop = Workshop::factory()->create(['price' => 100000]);
    $registration = Registration::create([
        'user_id' => $user->id,
        'workshop_id' => $workshop->id,
        'status' => 'pending',
    ]);
    $payment = $registration->payment()->create([
        'order_id' => 'WTX-REDIRECT01',
        'amount' => 100000,
        'payment_status' => Payment::STATUS_PENDING,
    ]);

    Mockery::mock('alias:Midtrans\Snap')
        ->shouldReceive('createTransaction')
        ->once()
        ->with([
            'transaction_details' => [
                'order_id' => $payment->order_id,
                'gross_amount' => $payment->amount,
            ],
            'callbacks' => [
                'finish' => route('payments.show', $registration),
            ],
        ])
        ->andReturn((object) ['token' => 'snap-token']);

    $this->actingAs($user)
        ->post(route('payments.pay', $registration))
        ->assertRedirect('https://app.sandbox.midtrans.com/snap/v2/vtweb/snap-token');

    expect($payment->fresh()->snap_token)->toBe('snap-token');
});

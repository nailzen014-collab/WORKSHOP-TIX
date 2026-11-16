<?php

use App\Models\Payment;
use App\Models\Registration;
use App\Models\User;
use App\Models\Workshop;

function makePendingPayment(): Payment
{
    $user = User::factory()->create();
    $workshop = Workshop::factory()->create(['price' => 100000]);

    $registration = Registration::create([
        'user_id' => $user->id,
        'workshop_id' => $workshop->id,
        'status' => 'pending',
    ]);

    return $registration->payment()->create([
        'order_id' => 'WTX-TESTORDER01',
        'amount' => 100000,
        'payment_status' => Payment::STATUS_PENDING,
    ]);
}

function midtransPayload(Payment $payment, string $transactionStatus = 'settlement', array $overrides = []): array
{
    $statusCode = '200';
    $grossAmount = (string) $payment->amount;
    $serverKey = config('services.midtrans.server_key');

    return array_merge([
        'order_id' => $payment->order_id,
        'status_code' => $statusCode,
        'gross_amount' => $grossAmount,
        'transaction_status' => $transactionStatus,
        'payment_type' => 'bank_transfer',
        'signature_key' => hash('sha512', $payment->order_id.$statusCode.$grossAmount.$serverKey),
    ], $overrides);
}

beforeEach(function () {
    config(['services.midtrans.server_key' => 'test-server-key']);
});

test('a valid settlement notification issues a ticket', function () {
    $payment = makePendingPayment();

    $this->postJson(route('midtrans.notification'), midtransPayload($payment))
        ->assertOk();

    $payment->refresh();

    expect($payment->isPaid())->toBeTrue();
    expect($payment->paid_at)->not->toBeNull();
    expect($payment->registration->ticket)->not->toBeNull();
});

test('a pending notification does not issue a ticket', function () {
    $payment = makePendingPayment();

    $this->postJson(route('midtrans.notification'), midtransPayload($payment, 'pending'))
        ->assertOk();

    expect($payment->refresh()->registration->ticket)->toBeNull();
});

test('a notification with an invalid signature is rejected', function () {
    $payment = makePendingPayment();

    $this->postJson(route('midtrans.notification'), midtransPayload($payment, 'settlement', [
        'signature_key' => 'invalid-signature',
    ]))->assertForbidden();

    expect($payment->refresh()->isPaid())->toBeFalse();
});

test('a notification with a mismatched amount is rejected', function () {
    $payment = makePendingPayment();

    $payload = midtransPayload($payment, 'settlement', ['gross_amount' => '1.00']);
    $payload['signature_key'] = hash('sha512', $payment->order_id.$payload['status_code'].'1.00'.config('services.midtrans.server_key'));

    $this->postJson(route('midtrans.notification'), $payload)->assertStatus(422);

    expect($payment->refresh()->isPaid())->toBeFalse();
});

test('repeated settlement notifications do not create duplicate tickets', function () {
    $payment = makePendingPayment();

    $payload = midtransPayload($payment);

    $this->postJson(route('midtrans.notification'), $payload)->assertOk();
    $this->postJson(route('midtrans.notification'), $payload)->assertOk();

    expect($payment->registration->ticket()->count())->toBe(1);
});

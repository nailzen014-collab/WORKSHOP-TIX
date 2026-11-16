<?php

use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\Registration;
use App\Models\User;
use App\Models\Workshop;

function makePendingPaymentForTypeTest(): Payment
{
    $user = User::factory()->create();
    $workshop = Workshop::factory()->create(['price' => 100000]);

    $registration = Registration::create([
        'user_id' => $user->id,
        'workshop_id' => $workshop->id,
        'status' => 'pending',
    ]);

    return $registration->payment()->create([
        'order_id' => 'WTX-TYPETEST01',
        'amount' => 100000,
        'payment_status' => Payment::STATUS_PENDING,
    ]);
}

function midtransPayloadForTypeTest(Payment $payment, string $transactionStatus = 'settlement', array $overrides = []): array
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

test('a midtrans callback maps payment type to master data', function () {
    $payment = makePendingPaymentForTypeTest();

    $this->postJson(route('midtrans.notification'), midtransPayloadForTypeTest($payment, 'settlement', [
        'payment_type' => 'gopay',
    ]))->assertOk();

    $payment->refresh();
    $paymentType = PaymentType::query()->where('code', 'gopay')->first();

    expect($paymentType)->not->toBeNull();
    expect($payment->payment_type)->toBe('gopay');
    expect($payment->payment_type_id)->toBe($paymentType->id);
    expect($payment->isPaid())->toBeTrue();
});

test('an unknown midtrans payment type creates a master record and still issues a ticket', function () {
    $payment = makePendingPaymentForTypeTest();

    $this->postJson(route('midtrans.notification'), midtransPayloadForTypeTest($payment, 'settlement', [
        'payment_type' => 'custom_pay',
    ]))->assertOk();

    $payment->refresh();
    $paymentType = PaymentType::query()->where('code', 'custom_pay')->first();

    expect($paymentType)->not->toBeNull();
    expect($payment->payment_type_id)->toBe($paymentType->id);
    expect($payment->registration->ticket)->not->toBeNull();
});

test('repeated notifications do not duplicate payment types', function () {
    $payment = makePendingPaymentForTypeTest();
    $payload = midtransPayloadForTypeTest($payment, 'settlement', ['payment_type' => 'gopay']);

    $this->postJson(route('midtrans.notification'), $payload)->assertOk();
    $this->postJson(route('midtrans.notification'), $payload)->assertOk();

    expect(PaymentType::query()->where('code', 'gopay')->count())->toBe(1);
    expect($payment->refresh()->payment_type_id)->not->toBeNull();
});

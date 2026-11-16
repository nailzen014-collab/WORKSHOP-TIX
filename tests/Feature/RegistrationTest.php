<?php

use App\Models\Registration;
use App\Models\User;
use App\Models\Workshop;

test('visitors can see the workshop listing', function () {
    $workshops = Workshop::factory()->count(3)->create();

    $response = $this->get('/');

    $response->assertOk();
    foreach ($workshops as $workshop) {
        $response->assertSee($workshop->title);
    }
});

test('draft workshops are not listed publicly', function () {
    $draft = Workshop::factory()->draft()->create();

    $this->get('/')
        ->assertOk()
        ->assertDontSee($draft->title);
});

test('visitors can open a workshop detail page', function () {
    $workshop = Workshop::factory()->create();

    $this->get(route('workshops.show', $workshop))
        ->assertOk()
        ->assertSee($workshop->title);
});

test('an authenticated participant can register for a workshop', function () {
    $user = User::factory()->create();
    $workshop = Workshop::factory()->create(['price' => 75000]);

    $response = $this->actingAs($user)->post(route('registrations.store', $workshop));

    $registration = Registration::whereBelongsTo($user)->whereBelongsTo($workshop)->first();

    expect($registration)->not->toBeNull();
    expect($registration->payment->amount)->toBe(75000);
    expect($registration->payment->payment_status)->toBe('pending');

    $response->assertRedirect(route('payments.show', $registration));
});

test('a participant cannot register twice for the same workshop', function () {
    $user = User::factory()->create();
    $workshop = Workshop::factory()->create();

    $this->actingAs($user)->post(route('registrations.store', $workshop));

    $this->actingAs($user)
        ->post(route('registrations.store', $workshop))
        ->assertRedirect();

    expect(Registration::whereBelongsTo($user)->whereBelongsTo($workshop)->count())->toBe(1);
});

test('guests are redirected to login when registering', function () {
    $workshop = Workshop::factory()->create();

    $this->post(route('registrations.store', $workshop))->assertRedirect(route('login'));
});

test('a participant cannot view another participant payment page', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $workshop = Workshop::factory()->create();

    $this->actingAs($owner)->post(route('registrations.store', $workshop));
    $registration = Registration::whereBelongsTo($owner)->firstOrFail();

    $this->actingAs($intruder)
        ->get(route('payments.show', $registration))
        ->assertForbidden();
});

test('the payment amount always comes from the database, not the request', function () {
    $user = User::factory()->create();
    $workshop = Workshop::factory()->create(['price' => 100000]);

    $this->actingAs($user)->post(route('registrations.store', $workshop), ['amount' => 1]);

    $registration = Registration::whereBelongsTo($user)->firstOrFail();

    expect($registration->payment->amount)->toBe(100000);
});

test('a participant cannot open a ticket belonging to someone else', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $workshop = Workshop::factory()->create();

    $this->actingAs($owner)->post(route('registrations.store', $workshop));
    $registration = Registration::whereBelongsTo($owner)->firstOrFail();

    $registration->payment()->update(['payment_status' => 'settlement', 'paid_at' => now()]);
    $ticket = $registration->ticket()->create([
        'ticket_code' => 'TIX-TEST1234',
        'status' => 'active',
        'issued_at' => now(),
    ]);

    $this->actingAs($intruder)
        ->get(route('tickets.show', $ticket))
        ->assertForbidden();
});

test('a ticket is not accessible until payment is paid', function () {
    $user = User::factory()->create();
    $workshop = Workshop::factory()->create();

    $this->actingAs($user)->post(route('registrations.store', $workshop));
    $registration = Registration::whereBelongsTo($user)->firstOrFail();

    $ticket = $registration->ticket()->create([
        'ticket_code' => 'TIX-PENDING01',
        'status' => 'active',
        'issued_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('tickets.show', $ticket))
        ->assertForbidden();
});

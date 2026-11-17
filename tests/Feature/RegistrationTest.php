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

    $response = $this->actingAs($user)->post(route('registrations.store', $workshop), [
        'attendees' => ['Nabhan Peserta'],
    ]);

    $registration = Registration::whereBelongsTo($user)->whereBelongsTo($workshop)->first();

    expect($registration)->not->toBeNull();
    expect($registration->attendees)->toBe(['Nabhan Peserta']);
    expect($registration->payment->amount)->toBe(75000);
    expect($registration->payment->payment_status)->toBe('pending');

    $response->assertRedirect(route('payments.show', $registration));
});

test('a participant cannot register twice for the same workshop', function () {
    $user = User::factory()->create();
    $workshop = Workshop::factory()->create();

    $this->actingAs($user)->post(route('registrations.store', $workshop), [
        'attendees' => ['Nabhan Peserta'],
    ]);

    $this->actingAs($user)
        ->post(route('registrations.store', $workshop), ['attendees' => ['Nabhan Peserta']])
        ->assertRedirect();

    expect(Registration::whereBelongsTo($user)->whereBelongsTo($workshop)->count())->toBe(1);
});

test('a participant can order five tickets with a separate name for each ticket', function () {
    $user = User::factory()->create();
    $workshop = Workshop::factory()->create(['price' => 75000]);
    $attendees = ['Nabhan', 'Alya', 'Bima', 'Citra', 'Dimas'];

    $response = $this->actingAs($user)
        ->post(route('registrations.store', $workshop), ['attendees' => $attendees]);

    $registration = Registration::whereBelongsTo($user)->whereBelongsTo($workshop)->firstOrFail();

    $response->assertRedirect(route('payments.show', $registration));
    expect($registration->attendees)->toBe($attendees);
    expect($registration->payment->amount)->toBe(375000);
    expect($registration->tickets)->toHaveCount(0);
});

test('a participant cannot order more than five tickets or omit a ticket identity', function (array $attendees) {
    $user = User::factory()->create();
    $workshop = Workshop::factory()->create();

    $this->actingAs($user)
        ->from(route('workshops.show', $workshop))
        ->post(route('registrations.store', $workshop), ['attendees' => $attendees])
        ->assertRedirect(route('workshops.show', $workshop))
        ->assertSessionHasErrors();

    expect(Registration::whereBelongsTo($user)->whereBelongsTo($workshop)->exists())->toBeFalse();
})->with([
    'six tickets exceed the limit' => [['A', 'B', 'C', 'D', 'E', 'F']],
    'a ticket name is required' => [['Nabhan', '']],
    'at least one attendee is required' => [[]],
]);

test('guests are redirected to login when registering', function () {
    $workshop = Workshop::factory()->create();

    $this->post(route('registrations.store', $workshop))->assertRedirect(route('login'));
});

test('a participant cannot view another participant payment page', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $workshop = Workshop::factory()->create();

    $this->actingAs($owner)->post(route('registrations.store', $workshop), [
        'attendees' => ['Nabhan Peserta'],
    ]);
    $registration = Registration::whereBelongsTo($owner)->firstOrFail();

    $this->actingAs($intruder)
        ->get(route('payments.show', $registration))
        ->assertForbidden();
});

test('the payment amount uses the database price and attendee count', function () {
    $user = User::factory()->create();
    $workshop = Workshop::factory()->create(['price' => 100000]);

    $this->actingAs($user)->post(route('registrations.store', $workshop), [
        'amount' => 1,
        'attendees' => ['Nabhan Peserta', 'Alya Peserta'],
    ]);

    $registration = Registration::whereBelongsTo($user)->firstOrFail();

    expect($registration->payment->amount)->toBe(200000);
});

test('a participant cannot open a ticket belonging to someone else', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $workshop = Workshop::factory()->create();

    $this->actingAs($owner)->post(route('registrations.store', $workshop), [
        'attendees' => ['Nabhan Peserta'],
    ]);
    $registration = Registration::whereBelongsTo($owner)->firstOrFail();

    $registration->payment()->update(['payment_status' => 'settlement', 'paid_at' => now()]);
    $ticket = $registration->tickets()->create([
        'attendee_name' => 'Nabhan Peserta',
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

    $this->actingAs($user)->post(route('registrations.store', $workshop), [
        'attendees' => ['Nabhan Peserta'],
    ]);
    $registration = Registration::whereBelongsTo($user)->firstOrFail();

    $ticket = $registration->tickets()->create([
        'attendee_name' => 'Nabhan Peserta',
        'ticket_code' => 'TIX-PENDING01',
        'status' => 'active',
        'issued_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('tickets.show', $ticket))
        ->assertForbidden();
});

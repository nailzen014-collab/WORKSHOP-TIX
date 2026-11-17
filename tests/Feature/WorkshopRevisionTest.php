<?php

use App\Models\Payment;
use App\Models\Registration;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

test('workshop cards show an image without a schedule and details show the full information', function () {
    $startsAt = now()->addYears(2)->setTime(16, 30);
    $description = 'Deskripsi lengkap untuk workshop ini harus tersedia pada halaman detail.';
    $imageUrl = 'https://workshoptix.test/storage/workshops/sample.jpg';
    $workshop = Workshop::factory()->create([
        'description' => $description,
        'image_url' => $imageUrl,
        'location' => 'Aula Workshop',
        'starts_at' => $startsAt,
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee($imageUrl)
        ->assertDontSee($startsAt->translatedFormat('d M Y, H:i'));

    $this->get(route('workshops.show', $workshop))
        ->assertOk()
        ->assertSee($imageUrl)
        ->assertSee('Aula Workshop')
        ->assertSee($startsAt->translatedFormat('l, d F Y, H:i'))
        ->assertSee($description)
        ->assertSee('Masuk')
        ->assertSee('Buat Akun')
        ->assertSee('Lanjut Tanpa Akun');
});

test('admin can upload a workshop image to local storage', function () {
    Storage::fake('public');

    $admin = User::factory()->admin()->create();
    $image = UploadedFile::fake()->image('workshop.jpg');

    $this->actingAs($admin)
        ->post(route('admin.workshops.store'), [
            'title' => 'Workshop Bergambar',
            'description' => 'Deskripsi workshop.',
            'price' => 50000,
            'starts_at' => now()->addWeek()->format('Y-m-d\TH:i'),
            'location' => 'Lab 1',
            'status' => 'published',
            'image' => $image,
        ])
        ->assertRedirect(route('admin.workshops.index'));

    $workshop = Workshop::query()->where('title', 'Workshop Bergambar')->firstOrFail();

    expect($workshop->image_path)->toStartWith('workshops/');
    expect($workshop->image_url)->toBe(Storage::disk('public')->url($workshop->image_path));
    Storage::disk('public')->assertExists($workshop->image_path);
});

test('admin can replace a workshop image and the previous local file is removed', function () {
    Storage::fake('public');

    $admin = User::factory()->admin()->create();
    $previousPath = UploadedFile::fake()->image('previous.jpg')->store('workshops', 'public');
    $workshop = Workshop::factory()->create([
        'image_path' => $previousPath,
        'image_url' => Storage::disk('public')->url($previousPath),
    ]);

    $this->actingAs($admin)
        ->put(route('admin.workshops.update', $workshop), [
            'title' => $workshop->title,
            'description' => $workshop->description,
            'price' => $workshop->price,
            'starts_at' => $workshop->starts_at->format('Y-m-d\TH:i'),
            'ends_at' => $workshop->ends_at?->format('Y-m-d\TH:i'),
            'location' => $workshop->location,
            'status' => $workshop->status,
            'image' => UploadedFile::fake()->image('replacement.jpg'),
        ])
        ->assertRedirect(route('admin.workshops.index'));

    $workshop->refresh();

    expect($workshop->image_path)->not->toBe($previousPath);
    expect($workshop->image_url)->toBe(Storage::disk('public')->url($workshop->image_path));
    Storage::disk('public')->assertMissing($previousPath);
    Storage::disk('public')->assertExists($workshop->image_path);
});

test('guests can create a private payment page without creating an account', function () {
    $workshop = Workshop::factory()->create(['price' => 75000]);

    $response = $this->post(route('registrations.guest.store', $workshop), [
        'attendees' => ['Nabhan Guest', 'Alya Guest'],
    ]);

    $response->assertRedirect();

    $registration = Registration::query()
        ->whereNull('user_id')
        ->where('guest_name', 'Nabhan Guest')
        ->firstOrFail();

    expect($registration->attendees)->toBe(['Nabhan Guest', 'Alya Guest']);
    expect($registration->payment->amount)->toBe(150000);
    expect($registration->payment->payment_status)->toBe(Payment::STATUS_PENDING);

    $this->get(route('guest.payments.show', $registration))
        ->assertForbidden();

    $this->get($response->headers->get('Location'))
        ->assertOk()
        ->assertSee('Nabhan Guest')
        ->assertSee('Tautan halaman ini bersifat privat');
});

test('a guest cannot order more than five tickets for one workshop', function () {
    $workshop = Workshop::factory()->create();

    $this->from(route('workshops.show', $workshop))
        ->post(route('registrations.guest.store', $workshop), [
            'attendees' => ['A', 'B', 'C', 'D', 'E', 'F'],
        ])
        ->assertRedirect(route('workshops.show', $workshop))
        ->assertSessionHasErrors('attendees');

    expect(Registration::query()->whereNull('user_id')->exists())->toBeFalse();
});

test('a guest sees the ticket on their signed page after payment is confirmed', function () {
    config(['services.midtrans.server_key' => 'test-server-key']);

    $workshop = Workshop::factory()->create(['price' => 50000]);
    $registration = Registration::create([
        'user_id' => null,
        'workshop_id' => $workshop->id,
        'guest_name' => 'Nabhan Guest',
        'attendees' => ['Nabhan Guest', 'Alya Guest'],
        'status' => 'pending',
    ]);
    $payment = $registration->payment()->create([
        'order_id' => 'WTX-GUESTPAID01',
        'amount' => 100000,
        'payment_status' => Payment::STATUS_PENDING,
    ]);
    $statusCode = '200';
    $grossAmount = (string) $payment->amount;

    $this->postJson(route('midtrans.notification'), [
        'order_id' => $payment->order_id,
        'status_code' => $statusCode,
        'gross_amount' => $grossAmount,
        'transaction_status' => 'settlement',
        'signature_key' => hash('sha512', $payment->order_id.$statusCode.$grossAmount.'test-server-key'),
    ])->assertOk();

    $registration->refresh();
    $guestUrl = URL::temporarySignedRoute(
        'guest.payments.show',
        now()->addHour(),
        ['registration' => $registration],
    );

    $this->get($guestUrl)
        ->assertOk()
        ->assertSee($registration->tickets->first()->ticket_code)
        ->assertSee('Nabhan Guest')
        ->assertSee('Alya Guest')
        ->assertSee('screenshot tiket');

    $callbackUrl = $guestUrl.'&order_id='.$payment->order_id.'&status_code=200&transaction_status=settlement';

    $this->get($callbackUrl)->assertOk();
    $this->get($guestUrl.'&unknown=parameter')->assertForbidden();
});

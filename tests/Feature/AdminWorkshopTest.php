<?php

use App\Models\User;
use App\Models\Workshop;

test('admin can access the dashboard', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
});

test('participants cannot access the admin dashboard', function () {
    $participant = User::factory()->create();

    $this->actingAs($participant)->get(route('admin.dashboard'))->assertForbidden();
});

test('guests are redirected from the admin area', function () {
    $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
});

test('admin can create a workshop', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('admin.workshops.store'), [
        'title' => 'Laravel Dasar',
        'description' => 'Belajar dasar Laravel.',
        'price' => 50000,
        'starts_at' => now()->addWeek()->format('Y-m-d\TH:i'),
        'ends_at' => now()->addWeek()->addHours(2)->format('Y-m-d\TH:i'),
        'location' => 'Lab 1',
        'status' => 'published',
    ]);

    $response->assertRedirect(route('admin.workshops.index'));
    $this->assertDatabaseHas('workshops', ['title' => 'Laravel Dasar', 'slug' => 'laravel-dasar']);
});

test('admin can update a workshop', function () {
    $admin = User::factory()->admin()->create();
    $workshop = Workshop::factory()->create();

    $this->actingAs($admin)->put(route('admin.workshops.update', $workshop), [
        'title' => 'Judul Baru',
        'description' => 'Deskripsi baru.',
        'price' => 99000,
        'starts_at' => now()->addWeek()->format('Y-m-d\TH:i'),
        'location' => 'Lab 2',
        'status' => 'draft',
    ])->assertRedirect(route('admin.workshops.index'));

    expect($workshop->fresh()->title)->toBe('Judul Baru');
});

test('admin can delete a workshop', function () {
    $admin = User::factory()->admin()->create();
    $workshop = Workshop::factory()->create();

    $this->actingAs($admin)
        ->delete(route('admin.workshops.destroy', $workshop))
        ->assertRedirect(route('admin.workshops.index'));

    $this->assertDatabaseMissing('workshops', ['id' => $workshop->id]);
});

test('participants cannot manage workshops', function () {
    $participant = User::factory()->create();

    $this->actingAs($participant)
        ->get(route('admin.workshops.index'))
        ->assertForbidden();
});

test('admin can view the participants list', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get(route('admin.participants.index'))->assertOk();
});

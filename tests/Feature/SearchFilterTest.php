<?php

use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\Registration;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopCategory;

test('public workshop list can be filtered by category', function () {
    $web = WorkshopCategory::factory()->create(['name' => 'Web Development', 'slug' => 'web-development']);
    $design = WorkshopCategory::factory()->create(['name' => 'UI/UX Design', 'slug' => 'ui-ux-design']);

    Workshop::factory()->create(['title' => 'Workshop Alpha', 'category_id' => $web->id]);
    Workshop::factory()->create(['title' => 'Workshop Beta', 'category_id' => $design->id]);

    $this->get(route('home', ['category' => $web->slug]))
        ->assertOk()
        ->assertSee('Workshop Alpha')
        ->assertDontSee('Workshop Beta');
});

test('admin participant list can be filtered by category and payment type', function () {
    $admin = User::factory()->admin()->create();
    $category = WorkshopCategory::factory()->create(['name' => 'Data Science', 'slug' => 'data-science']);
    $paymentType = PaymentType::factory()->create(['code' => 'gopay', 'name' => 'GoPay']);
    $workshop = Workshop::factory()->create(['category_id' => $category->id]);

    $matchingUser = User::factory()->create(['name' => 'Peserta Filter']);
    $matchingRegistration = Registration::create([
        'user_id' => $matchingUser->id,
        'workshop_id' => $workshop->id,
        'status' => 'pending',
    ]);
    $matchingRegistration->payment()->create([
        'order_id' => 'WTX-FILTER01',
        'amount' => $workshop->price,
        'payment_status' => Payment::STATUS_PENDING,
        'payment_type' => 'gopay',
        'payment_type_id' => $paymentType->id,
    ]);

    $otherUser = User::factory()->create(['name' => 'Peserta Lain']);
    $otherRegistration = Registration::create([
        'user_id' => $otherUser->id,
        'workshop_id' => Workshop::factory()->create()->id,
        'status' => 'pending',
    ]);
    $otherRegistration->payment()->create([
        'order_id' => 'WTX-FILTER02',
        'amount' => 100000,
        'payment_status' => Payment::STATUS_PENDING,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.participants.index', [
            'category' => $category->slug,
            'payment_type' => $paymentType->id,
            'payment_status' => Payment::STATUS_PENDING,
        ]))
        ->assertOk()
        ->assertSee('Peserta Filter')
        ->assertDontSee('Peserta Lain');
});

test('admin workshop list can be filtered by category and status', function () {
    $admin = User::factory()->admin()->create();
    $category = WorkshopCategory::factory()->create(['slug' => 'mobile-apps']);

    Workshop::factory()->create([
        'title' => 'Workshop Terbit',
        'category_id' => $category->id,
        'status' => 'published',
    ]);
    Workshop::factory()->draft()->create([
        'title' => 'Workshop Draft',
        'category_id' => $category->id,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.workshops.index', [
            'category' => $category->id,
            'status' => 'published',
        ]))
        ->assertOk()
        ->assertSee('Workshop Terbit')
        ->assertDontSee('Workshop Draft');
});

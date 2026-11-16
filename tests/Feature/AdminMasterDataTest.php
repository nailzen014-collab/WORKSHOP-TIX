<?php

use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\Registration;
use App\Models\Status;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopCategory;

test('admin can create master data', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->post(route('admin.categories.store'), [
        'name' => 'Cloud Computing',
        'description' => 'Workshop cloud.',
        'is_active' => '1',
        'sort_order' => 1,
    ])->assertRedirect(route('admin.categories.index'));

    $this->actingAs($admin)->post(route('admin.payment-types.store'), [
        'code' => 'gopay',
        'name' => 'GoPay',
        'group' => 'ewallet',
        'is_active' => '1',
        'sort_order' => 1,
    ])->assertRedirect(route('admin.payment-types.index'));

    $this->actingAs($admin)->post(route('admin.statuses.store'), [
        'entity' => Status::ENTITY_WORKSHOP,
        'code' => 'archived',
        'name' => 'Diarsipkan',
        'color' => 'neutral',
        'is_active' => '1',
        'sort_order' => 1,
    ])->assertRedirect(route('admin.statuses.index', ['entity' => Status::ENTITY_WORKSHOP]));

    $this->assertDatabaseHas('workshop_categories', ['name' => 'Cloud Computing', 'slug' => 'cloud-computing']);
    $this->assertDatabaseHas('payment_types', ['code' => 'gopay', 'name' => 'GoPay']);
    $this->assertDatabaseHas('statuses', ['entity' => Status::ENTITY_WORKSHOP, 'code' => 'archived']);
});

test('participants cannot manage master data', function () {
    $participant = User::factory()->create();

    $this->actingAs($participant)->get(route('admin.categories.index'))->assertForbidden();
    $this->actingAs($participant)->get(route('admin.payment-types.index'))->assertForbidden();
    $this->actingAs($participant)->get(route('admin.statuses.index'))->assertForbidden();
});

test('a category in use cannot be deleted', function () {
    $admin = User::factory()->admin()->create();
    $category = WorkshopCategory::factory()->create();
    Workshop::factory()->create(['category_id' => $category->id]);

    $this->actingAs($admin)
        ->delete(route('admin.categories.destroy', $category))
        ->assertSessionHas('error');

    $this->assertDatabaseHas('workshop_categories', ['id' => $category->id]);
});

test('a payment type in use cannot be deleted', function () {
    $admin = User::factory()->admin()->create();
    $paymentType = PaymentType::factory()->create(['code' => 'gopay']);
    $user = User::factory()->create();
    $workshop = Workshop::factory()->create();
    $registration = Registration::create([
        'user_id' => $user->id,
        'workshop_id' => $workshop->id,
        'status' => 'pending',
    ]);

    $registration->payment()->create([
        'order_id' => 'WTX-MASTER01',
        'amount' => $workshop->price,
        'payment_status' => Payment::STATUS_PENDING,
        'payment_type' => 'gopay',
        'payment_type_id' => $paymentType->id,
    ]);

    $this->actingAs($admin)
        ->delete(route('admin.payment-types.destroy', $paymentType))
        ->assertSessionHas('error');

    $this->assertDatabaseHas('payment_types', ['id' => $paymentType->id]);
});

test('user role statuses are read only', function () {
    $admin = User::factory()->admin()->create();
    $status = Status::create([
        'entity' => Status::ENTITY_USER_ROLE,
        'code' => 'admin',
        'name' => 'Admin',
        'color' => 'forest',
        'is_active' => true,
        'sort_order' => 0,
    ]);

    $this->actingAs($admin)->get(route('admin.statuses.edit', $status))->assertForbidden();
    $this->actingAs($admin)->put(route('admin.statuses.update', $status), [
        'name' => 'Administrator',
        'color' => 'forest',
        'is_active' => '1',
    ])->assertForbidden();
    $this->actingAs($admin)->delete(route('admin.statuses.destroy', $status))->assertForbidden();
});

test('a status in use cannot be deleted', function () {
    $admin = User::factory()->admin()->create();
    $status = Status::create([
        'entity' => Status::ENTITY_WORKSHOP,
        'code' => 'published',
        'name' => 'Terbit',
        'color' => 'lime',
        'is_active' => true,
        'sort_order' => 0,
    ]);
    Workshop::factory()->create(['status' => 'published']);

    $this->actingAs($admin)
        ->delete(route('admin.statuses.destroy', $status))
        ->assertSessionHas('error');

    $this->assertDatabaseHas('statuses', ['id' => $status->id]);
});

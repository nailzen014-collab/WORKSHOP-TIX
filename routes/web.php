<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ParticipantController;
use App\Http\Controllers\Admin\PaymentTypeController;
use App\Http\Controllers\Admin\StatusController;
use App\Http\Controllers\Admin\WorkshopCategoryController;
use App\Http\Controllers\Admin\WorkshopController as AdminWorkshopController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\WorkshopController;
use Illuminate\Support\Facades\Route;
use Kreait\Firebase\Contract\Firestore;


Route::get('/', [WorkshopController::class, 'index'])->name('home');
Route::get('/workshops/{workshop:slug}', [WorkshopController::class, 'show'])->name('workshops.show');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('home'))->name('dashboard');

    Route::post('/workshops/{workshop:slug}/register', [RegistrationController::class, 'store'])
        ->name('registrations.store');

    Route::get('/registrations/{registration}/payment', [PaymentController::class, 'show'])
        ->name('payments.show');
    Route::post('/registrations/{registration}/payment', [PaymentController::class, 'pay'])
        ->name('payments.pay');
    Route::post('/registrations/{registration}/payment/check', [PaymentController::class, 'check'])
        ->name('payments.check');

    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/midtrans/notification', [PaymentController::class, 'notification'])
    ->name('midtrans.notification');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('workshops', AdminWorkshopController::class)->except('show');
    Route::get('participants', [ParticipantController::class, 'index'])->name('participants.index');
    Route::resource('categories', WorkshopCategoryController::class)->except('show');
    Route::resource('payment-types', PaymentTypeController::class)
        ->except('show')
        ->parameters(['payment-types' => 'paymentType']);
    Route::resource('statuses', StatusController::class)->except('show');
});



require __DIR__.'/auth.php';

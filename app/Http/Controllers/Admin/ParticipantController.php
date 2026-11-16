<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentType;
use App\Models\Registration;
use App\Models\Status;
use App\Models\Workshop;
use App\Models\WorkshopCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParticipantController extends Controller
{
    public function index(Request $request): View
    {
        $registrations = Registration::query()
            ->with([
                'user:id,name,email',
                'workshop:id,title,price,category_id',
                'workshop.category:id,name',
                'payment:id,registration_id,payment_status,amount,paid_at,payment_type,payment_type_id',
                'payment.paymentType:id,code,name',
            ])
            ->when($request->filled('workshop'), function ($query) use ($request) {
                $query->where('workshop_id', $request->integer('workshop'));
            })
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q')->toString().'%';

                $query->whereHas('user', fn ($query) => $query
                    ->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term));
            })
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->whereHas('workshop.category', fn ($query) => $query->where('slug', $request->string('category')->toString()));
            })
            ->when($request->filled('payment_type'), function ($query) use ($request) {
                $query->whereHas('payment', fn ($query) => $query->where('payment_type_id', $request->integer('payment_type')));
            })
            ->when($request->filled('payment_status'), function ($query) use ($request) {
                $query->whereHas('payment', fn ($query) => $query->where('payment_status', $request->string('payment_status')->toString()));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.participants.index', [
            'registrations' => $registrations,
            'workshops' => Workshop::query()->orderBy('title')->pluck('title', 'id')->all(),
            'categories' => WorkshopCategory::query()->orderBy('sort_order')->orderBy('name')->pluck('name', 'slug')->all(),
            'paymentTypes' => PaymentType::query()->orderBy('sort_order')->orderBy('name')->pluck('name', 'id')->all(),
            'paymentStatuses' => $this->paymentStatusOptions(),
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function paymentStatusOptions(): array
    {
        $statuses = Status::query()
            ->forEntity(Status::ENTITY_PAYMENT)
            ->orderBy('sort_order')
            ->pluck('name', 'code')
            ->all();

        return $statuses ?: [
            'pending' => 'Menunggu',
            'settlement' => 'Lunas',
            'capture' => 'Ditangkap',
            'deny' => 'Ditolak',
            'expire' => 'Kedaluwarsa',
            'cancel' => 'Dibatalkan',
        ];
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\Workshop;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'workshops' => Workshop::count(),
            'registrations' => Registration::count(),
            'paid' => Payment::whereIn('payment_status', [Payment::STATUS_SETTLEMENT, Payment::STATUS_CAPTURE])->count(),
            'pending' => Payment::where('payment_status', Payment::STATUS_PENDING)->count(),
        ];

        $latestRegistrations = Registration::query()
            ->with(['user:id,name,email', 'workshop:id,title', 'payment:id,registration_id,payment_status,amount'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', [
            'stats' => $stats,
            'latestRegistrations' => $latestRegistrations,
        ]);
    }
}

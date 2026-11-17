<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $registrations = $request->user()
            ->registrations()
            ->with(['workshop', 'payment', 'tickets'])
            ->latest()
            ->get();

        return view('tickets.index', [
            'registrations' => $registrations,
        ]);
    }

    public function show(Request $request, Ticket $ticket): View
    {
        $ticket->load('registration.workshop', 'registration.user', 'registration.payment');

        abort_unless($ticket->registration->user_id === $request->user()->id, 403);

        abort_unless($ticket->registration->payment?->isPaid(), 403);

        return view('tickets.show', [
            'ticket' => $ticket,
        ]);
    }
}

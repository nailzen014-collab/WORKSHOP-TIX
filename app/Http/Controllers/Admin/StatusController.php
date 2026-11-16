<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StatusRequest;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\Status;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StatusController extends Controller
{
    public function index(Request $request): View
    {
        $entities = Status::entities();
        $selectedEntity = $request->string('entity')->toString();

        if (! array_key_exists($selectedEntity, $entities)) {
            $selectedEntity = null;
        }

        $statuses = Status::query()
            ->when($selectedEntity, fn ($query) => $query->forEntity($selectedEntity))
            ->orderBy('entity')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(50)
            ->withQueryString();

        return view('admin.statuses.index', [
            'statuses' => $statuses,
            'entities' => $entities,
            'selectedEntity' => $selectedEntity,
        ]);
    }

    public function create(Request $request): View
    {
        $entity = $request->string('entity')->toString() ?: Status::ENTITY_WORKSHOP;
        $entities = Status::entities();

        if (! array_key_exists($entity, $entities) || $entity === Status::ENTITY_USER_ROLE) {
            abort(403);
        }

        return view('admin.statuses.create', [
            'status' => new Status(['entity' => $entity, 'color' => 'neutral', 'is_active' => true]),
            'entities' => $entities,
        ]);
    }

    public function store(StatusRequest $request): RedirectResponse
    {
        if ($request->input('entity') === Status::ENTITY_USER_ROLE) {
            abort(403);
        }

        Status::create($request->validated());

        return redirect()
            ->route('admin.statuses.index', ['entity' => $request->input('entity')])
            ->with('status', 'Status berhasil ditambahkan.');
    }

    public function edit(Status $status): View
    {
        $this->ensureMutable($status);

        return view('admin.statuses.edit', [
            'status' => $status,
            'entities' => Status::entities(),
        ]);
    }

    public function update(StatusRequest $request, Status $status): RedirectResponse
    {
        $this->ensureMutable($status);

        $status->update([
            'name' => $request->input('name'),
            'color' => $request->input('color'),
            'sort_order' => $request->integer('sort_order'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.statuses.index', ['entity' => $status->entity])
            ->with('status', 'Status berhasil diperbarui.');
    }

    public function destroy(Status $status): RedirectResponse
    {
        $this->ensureMutable($status);

        if ($this->isInUse($status)) {
            return back()->with('error', 'Status tidak dapat dihapus karena masih dipakai oleh data.');
        }

        $status->delete();

        return redirect()
            ->route('admin.statuses.index', ['entity' => $status->entity])
            ->with('status', 'Status berhasil dihapus.');
    }

    private function ensureMutable(Status $status): void
    {
        if ($status->entity === Status::ENTITY_USER_ROLE) {
            abort(403);
        }
    }

    private function isInUse(Status $status): bool
    {
        return match ($status->entity) {
            Status::ENTITY_WORKSHOP => Workshop::query()->where('status', $status->code)->exists(),
            Status::ENTITY_REGISTRATION => Registration::query()->where('status', $status->code)->exists(),
            Status::ENTITY_PAYMENT => Payment::query()->where('payment_status', $status->code)->exists(),
            Status::ENTITY_TICKET => Ticket::query()->where('status', $status->code)->exists(),
            Status::ENTITY_USER_ROLE => User::query()->where('role', $status->code)->exists(),
            default => false,
        };
    }
}

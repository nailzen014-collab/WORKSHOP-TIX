<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PaymentTypeRequest;
use App\Models\PaymentType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentTypeController extends Controller
{
    public function index(Request $request): View
    {
        $paymentTypes = PaymentType::query()
            ->withCount('payments')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q')->toString().'%';
                $query->where(fn ($query) => $query
                    ->where('name', 'like', $term)
                    ->orWhere('code', 'like', $term));
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.payment-types.index', [
            'paymentTypes' => $paymentTypes,
        ]);
    }

    public function create(): View
    {
        return view('admin.payment-types.create', [
            'paymentType' => new PaymentType(['is_active' => true]),
            'groups' => PaymentTypeRequest::GROUPS,
        ]);
    }

    public function store(PaymentTypeRequest $request): RedirectResponse
    {
        PaymentType::create($request->validated());

        return redirect()
            ->route('admin.payment-types.index')
            ->with('status', 'Jenis pembayaran berhasil ditambahkan.');
    }

    public function edit(PaymentType $paymentType): View
    {
        return view('admin.payment-types.edit', [
            'paymentType' => $paymentType,
            'groups' => PaymentTypeRequest::GROUPS,
        ]);
    }

    public function update(PaymentTypeRequest $request, PaymentType $paymentType): RedirectResponse
    {
        $paymentType->update($request->validated());

        return redirect()
            ->route('admin.payment-types.index')
            ->with('status', 'Jenis pembayaran berhasil diperbarui.');
    }

    public function destroy(PaymentType $paymentType): RedirectResponse
    {
        if ($paymentType->payments()->exists()) {
            return back()->with('error', 'Jenis pembayaran tidak dapat dihapus karena masih dipakai oleh data pembayaran.');
        }

        $paymentType->delete();

        return redirect()
            ->route('admin.payment-types.index')
            ->with('status', 'Jenis pembayaran berhasil dihapus.');
    }
}

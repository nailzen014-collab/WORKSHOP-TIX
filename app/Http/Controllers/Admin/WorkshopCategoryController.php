<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WorkshopCategoryRequest;
use App\Models\WorkshopCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkshopCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $categories = WorkshopCategory::query()
            ->withCount('workshops')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q')->toString().'%';
                $query->where(fn ($query) => $query
                    ->where('name', 'like', $term)
                    ->orWhere('slug', 'like', $term));
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.categories.index', [
            'categories' => $categories,
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.create', [
            'category' => new WorkshopCategory(['is_active' => true]),
        ]);
    }

    public function store(WorkshopCategoryRequest $request): RedirectResponse
    {
        WorkshopCategory::create($request->validated());

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Kategori berhasil ditambahkan.');
    }

    public function edit(WorkshopCategory $category): View
    {
        return view('admin.categories.edit', [
            'category' => $category,
        ]);
    }

    public function update(WorkshopCategoryRequest $request, WorkshopCategory $category): RedirectResponse
    {
        $category->update($request->validated());

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Kategori berhasil diperbarui.');
    }

    public function destroy(WorkshopCategory $category): RedirectResponse
    {
        if ($category->workshops()->exists()) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih dipakai oleh workshop.');
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Kategori berhasil dihapus.');
    }
}

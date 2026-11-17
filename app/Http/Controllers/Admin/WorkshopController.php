<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WorkshopRequest;
use App\Models\Status;
use App\Models\Workshop;
use App\Models\WorkshopCategory;
use App\Services\WorkshopImageStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkshopController extends Controller
{
    public function index(Request $request): View
    {
        $workshops = Workshop::query()
            ->with('category')
            ->withCount('registrations')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q')->toString().'%';

                $query->where(fn ($query) => $query
                    ->where('title', 'like', $term)
                    ->orWhere('location', 'like', $term));
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('category'), fn ($query) => $query->where('category_id', $request->integer('category')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.workshops.index', [
            'workshops' => $workshops,
            'categories' => $this->categoryOptions(),
            'statuses' => $this->workshopStatusOptions(),
        ]);
    }

    public function create(): View
    {
        return view('admin.workshops.create', [
            'workshop' => new Workshop,
            'categories' => WorkshopCategory::query()->active()->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function store(WorkshopRequest $request, WorkshopImageStorage $images): RedirectResponse
    {
        $data = $request->validated();
        $image = $data['image'] ?? null;
        unset($data['image']);

        if ($image) {
            $storedImage = $images->store($image);
            $data['image_path'] = $storedImage['path'];
            $data['image_url'] = $storedImage['url'];
        }

        Workshop::create($data);

        return redirect()
            ->route('admin.workshops.index')
            ->with('status', 'Workshop berhasil ditambahkan.');
    }

    public function edit(Workshop $workshop): View
    {
        return view('admin.workshops.edit', [
            'workshop' => $workshop,
            'categories' => WorkshopCategory::query()->active()->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function update(WorkshopRequest $request, Workshop $workshop, WorkshopImageStorage $images): RedirectResponse
    {
        $data = $request->validated();
        $image = $data['image'] ?? null;
        unset($data['image']);

        if ($image) {
            $previousImagePath = $workshop->image_path;
            $storedImage = $images->store($image);
            $data['image_path'] = $storedImage['path'];
            $data['image_url'] = $storedImage['url'];
        }

        $workshop->update($data);

        if ($image && $previousImagePath) {
            $images->delete($previousImagePath);
        }

        return redirect()
            ->route('admin.workshops.index')
            ->with('status', 'Workshop berhasil diperbarui.');
    }

    public function destroy(Workshop $workshop, WorkshopImageStorage $images): RedirectResponse
    {
        $imagePath = $workshop->image_path;
        $workshop->delete();

        if ($imagePath) {
            $images->delete($imagePath);
        }

        return redirect()
            ->route('admin.workshops.index')
            ->with('status', 'Workshop berhasil dihapus.');
    }

    /**
     * @return array<int, string>
     */
    private function categoryOptions(): array
    {
        return WorkshopCategory::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    /**
     * @return array<string, string>
     */
    private function workshopStatusOptions(): array
    {
        $statuses = Status::query()
            ->forEntity(Status::ENTITY_WORKSHOP)
            ->orderBy('sort_order')
            ->pluck('name', 'code')
            ->all();

        return $statuses ?: ['draft' => 'Draft', 'published' => 'Terbit'];
    }
}

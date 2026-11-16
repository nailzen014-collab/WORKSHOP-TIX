<x-admin-layout>
    <x-slot name="header">
        <a href="{{ route('admin.statuses.index', ['entity' => $status->entity]) }}" class="inline-flex items-center gap-1 text-sm font-semibold text-forest-600 hover:text-forest-900">&larr; Kembali</a>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card-cream p-6 sm:p-8">
                <p class="eyebrow">Master Data</p>
                <h1 class="mt-1 font-display text-2xl font-extrabold uppercase tracking-tight text-forest-900">Edit Status</h1>

                <form method="POST" action="{{ route('admin.statuses.update', $status) }}" class="mt-6 space-y-6">
                    @csrf
                    @method('PUT')
                    @include('admin.statuses.partials.form', ['status' => $status, 'entities' => $entities])

                    <div class="flex justify-end gap-3 border-t border-forest-900/10 pt-6">
                        <a href="{{ route('admin.statuses.index', ['entity' => $status->entity]) }}" class="pill-button-outline">Batal</a>
                        <button type="submit" class="pill-button">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>

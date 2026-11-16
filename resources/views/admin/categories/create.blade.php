<x-admin-layout>
    <x-slot name="header">
        <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-forest-600 hover:text-forest-900">&larr; Kembali</a>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card-cream p-6 sm:p-8">
                <p class="eyebrow">Master Data</p>
                <h1 class="mt-1 font-display text-2xl font-extrabold uppercase tracking-tight text-forest-900">Tambah Kategori</h1>

                <form method="POST" action="{{ route('admin.categories.store') }}" class="mt-6 space-y-6">
                    @csrf
                    @include('admin.categories.partials.form', ['category' => $category])

                    <div class="flex justify-end gap-3 border-t border-forest-900/10 pt-6">
                        <a href="{{ route('admin.categories.index') }}" class="pill-button-outline">Batal</a>
                        <button type="submit" class="pill-button">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>

<x-admin-layout>
    <x-slot name="header">
        <a href="{{ route('admin.workshops.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-forest-600 hover:text-forest-900">&larr; Kembali</a>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card-cream p-6 sm:p-8">
                <p class="eyebrow">Panel Panitia</p>
                <h1 class="mt-1 font-display text-2xl font-extrabold uppercase tracking-tight text-forest-900">Edit Workshop</h1>

                <form method="POST" action="{{ route('admin.workshops.update', $workshop) }}" class="mt-6 space-y-6">
                    @csrf
                    @method('PUT')
                    @include('admin.workshops.partials.form', ['workshop' => $workshop])

                    <div class="flex justify-end gap-3 border-t border-forest-900/10 pt-6">
                        <a href="{{ route('admin.workshops.index') }}" class="pill-button-outline">Batal</a>
                        <button type="submit" class="pill-button">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>

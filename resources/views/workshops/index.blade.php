<x-app-layout>
    {{-- Hero --}}
    <section class="border-b border-forest-900/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-14">
            <div class="grid items-center gap-8 lg:grid-cols-2">
                <div class="card-cream relative overflow-hidden p-8 sm:p-10">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="badge-chip-lime">Pendaftaran Dibuka</span>
                        <span class="badge-chip">{{ now()->translatedFormat('F Y') }}</span>
                    </div>

                    <h1 class="display-heading mt-6 text-5xl sm:text-6xl">
                        Workshop<br>
                        <span class="text-forest-600">IT</span> Untuk<br>
                        <span class="accent-serif text-forest-700">Generasi Berikutnya.</span>
                    </h1>

                    <p class="mt-5 max-w-md text-sm text-forest-700">
                        Pilih workshop, daftar dalam hitungan menit, dan dapatkan tiket digital Anda setelah pembayaran terverifikasi.
                    </p>

                    <div class="mt-7 flex flex-wrap items-center gap-3">
                        <a href="#workshops" class="pill-button-lime">
                            Lihat Workshop
                            <span class="grid h-6 w-6 place-items-center rounded-full bg-forest-900 text-lime-500">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path d="M6 4l6 6-6 6V4z" /></svg>
                            </span>
                        </a>
                        @guest
                            <a href="{{ route('register') }}" class="pill-button-outline">Buat Akun</a>
                        @endguest
                    </div>
                </div>

                <div class="relative">
                    <div class="overflow-hidden rounded-t-[14rem] rounded-b-4xl border border-forest-900/10 shadow-card">
                        <img src="{{ asset('images/hero-workshop.png') }}" alt="Peserta workshop IT belajar bersama" class="h-[26rem] w-full object-cover" />
                    </div>
                    <div class="absolute bottom-4 left-4 right-4 rounded-2xl bg-forest-900/90 px-4 py-3 text-cream-50 backdrop-blur">
                        <p class="eyebrow text-lime-500">Diselenggarakan di</p>
                        <p class="mt-1 text-sm font-semibold">SMK PESAT — Lab Komputer &amp; Aula</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Marquee --}}
    <div class="overflow-hidden border-b border-forest-900/10 bg-forest-900 py-3 text-cream-50">
        <div class="marquee-track flex w-max gap-8 whitespace-nowrap font-display text-sm font-bold uppercase tracking-widest">
            @for ($i = 0; $i < 2; $i++)
                @foreach (['Web Development', 'UI/UX Design', 'Data Science', 'Mobile Apps', 'Cyber Security', 'Pendaftaran Mudah', 'Tiket Digital'] as $item)
                    <span class="flex items-center gap-8">
                        {{ $item }}
                        <span class="text-lime-500">&bull;</span>
                    </span>
                @endforeach
            @endfor
        </div>
    </div>

    {{-- Workshops --}}
    <section id="workshops" class="scroll-mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="flex flex-wrap items-end justify-between gap-4 border-b border-forest-900/10 pb-6">
                <div>
                    <p class="eyebrow">Pilihan Kegiatan</p>
                    <h2 class="display-heading mt-2 text-4xl sm:text-5xl">
                        Pilih <span class="accent-serif text-forest-700">Workshop-nya.</span>
                    </h2>
                </div>
                <p class="text-sm text-forest-600">{{ $workshops->total() }} workshop tersedia</p>
            </div>

            <x-filter-bar :action="route('home').'#workshops'" :reset="route('home').'#workshops'" class="mt-6">
                <div class="w-full sm:w-64">
                    <label for="q" class="text-xs font-semibold uppercase tracking-wide text-forest-600">Cari Workshop</label>
                    <input id="q" name="q" type="search" value="{{ request('q') }}" placeholder="Judul, lokasi, atau deskripsi" class="mt-1 block w-full rounded-xl border-forest-900/20 bg-cream-50 text-sm text-forest-900 shadow-sm focus:border-forest-600 focus:ring-forest-600">
                </div>
                <x-select-field label="Kategori" name="category" :value="request('category')" :options="$categories->pluck('name', 'slug')->all()" placeholder="Semua kategori" />
            </x-filter-bar>

            @if ($workshops->isEmpty())
                <div class="card-cream mt-8 p-12 text-center">
                    <p class="text-forest-700">Belum ada workshop yang tersedia saat ini.</p>
                </div>
            @else
                <div class="mt-10 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($workshops as $workshop)
                        <article class="card-cream group flex flex-col overflow-hidden transition-transform hover:-translate-y-1">
                            <a href="{{ route('workshops.show', $workshop) }}" class="block aspect-[16/10] overflow-hidden bg-cream-200">
                                <img
                                    src="{{ $workshop->image_url ?: asset('images/hero-workshop.png') }}"
                                    alt="{{ $workshop->title }}"
                                    class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.03]"
                                    loading="lazy"
                                    decoding="async"
                                />
                            </a>

                            <div class="flex flex-1 flex-col p-6">
                                <h3 class="font-display text-xl font-extrabold uppercase leading-tight tracking-tight text-forest-900">
                                    <a href="{{ route('workshops.show', $workshop) }}">{{ $workshop->title }}</a>
                                </h3>

                                <p class="mt-2 flex-1 text-sm text-forest-700">
                                    {{ \Illuminate\Support\Str::limit($workshop->description, 110) }}
                                </p>

                                <div class="mt-5 flex flex-wrap items-end justify-between gap-3 border-t border-forest-900/10 pt-4">
                                    <div class="flex flex-wrap items-center gap-2">
                                        @if ($workshop->category)
                                            <span class="badge-chip-lime">{{ $workshop->category->name }}</span>
                                        @endif
                                        <span class="badge-chip">{{ $workshop->location }}</span>
                                    </div>
                                    <div class="ml-auto text-right">
                                        <p class="text-xs font-semibold text-forest-500">{{ $workshop->registrations_count }} peserta</p>
                                        <p class="eyebrow">Harga</p>
                                        <p class="mt-0.5 font-display text-lg font-extrabold text-forest-900">Rp{{ number_format($workshop->price, 0, ',', '.') }}</p>
                                    </div>
                                </div>

                                <a href="{{ route('workshops.show', $workshop) }}" class="pill-button mt-4 justify-center">
                                    Lihat Detail
                                    <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" viewBox="0 0 20 20" fill="currentColor"><path d="M6 4l6 6-6 6V4z" /></svg>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $workshops->links() }}
                </div>
            @endif
        </div>
    </section>

    {{-- How it works --}}
    <section class="border-y border-forest-900/10 bg-cream-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <p class="eyebrow">Alur Sederhana</p>
            <h2 class="display-heading mt-2 text-4xl sm:text-5xl">Empat Langkah <span class="accent-serif text-forest-700">ke Tiket.</span></h2>

            <div class="mt-10 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                    ['no' => '01', 'title' => 'Pilih Workshop', 'desc' => 'Telusuri daftar workshop IT dan buka detailnya.'],
                    ['no' => '02', 'title' => 'Pilih Cara Daftar', 'desc' => 'Masuk, buat akun, atau lanjut sebagai guest.'],
                    ['no' => '03', 'title' => 'Bayar', 'desc' => 'Selesaikan pembayaran lewat Midtrans Sandbox.'],
                    ['no' => '04', 'title' => 'Dapatkan Tiket', 'desc' => 'Tiket digital terbit otomatis setelah lunas.'],
                ] as $step)
                    <div class="card-cream flex flex-col p-6">
                        <span class="font-display text-3xl font-extrabold text-forest-200">{{ $step['no'] }}</span>
                        <h3 class="mt-3 font-display text-lg font-extrabold uppercase tracking-tight text-forest-900">{{ $step['title'] }}</h3>
                        <p class="mt-2 text-sm text-forest-700">{{ $step['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</x-app-layout>

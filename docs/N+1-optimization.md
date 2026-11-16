# Optimasi Query & N+1

Dokumen ini mencatat pola N+1 yang diperbaiki dan dampaknya terhadap jumlah query.

## Pencegahan N+1 global

Di `app/Providers/AppServiceProvider.php` lazy loading dicegah di luar lingkungan produksi:

```php
Model::preventLazyLoading(! $this->app->isProduction());
```

Akibatnya, setiap relasi yang diakses tanpa di-eager-load akan melempar `LazyLoadingViolationException` saat pengembangan, sehingga N+1 tertangkap lebih awal. Semua controller yang menampilkan relasi memakai `with()` / `load()` / `withCount()`.

## Contoh 1 — Daftar workshop (halaman publik)

Halaman `/` menampilkan daftar workshop beserta jumlah pesertanya.

| Pendekatan | Query |
|---|---|
| Loop `$workshop->registrations()->count()` per baris (N+1) | **7** (1 daftar + 6 hitung) |
| `Workshop::published()->withCount('registrations')` | **1** |

`resources/views/workshops/index.blade.php` memakai `$workshop->registrations_count` dari `withCount`, tanpa query di view.

## Contoh 2 — Daftar peserta (admin)

`Admin\ParticipantController` memuat relasi yang dipakai tabel dalam satu eager load:

```php
Registration::query()
    ->with(['user:id,name,email', 'workshop:id,title,price', 'payment:id,registration_id,payment_status,amount,paid_at'])
    ->latest()
    ->paginate(15);
```

Tanpa eager load, tabel 15 baris akan memicu 1 + (3 × 15) query. Dengan eager load menjadi konstan (1 + 3) berapa pun jumlah baris per halaman.

## Contoh 3 — Dashboard admin

`Admin\DashboardController` memuat `user`, `workshop`, dan `payment` sekaligus untuk 10 pendaftaran terbaru, dan statistik memakai agregat (`count`) alih-alih memuat semua baris ke memori.

## Ringkasan perbaikan

- Halaman publik daftar workshop: **7 → 1** query.
- Tabel peserta & dashboard: query relasi konstan (tidak tumbuh linear dengan jumlah baris).
- View tidak menjalankan query; semua data disiapkan di controller.

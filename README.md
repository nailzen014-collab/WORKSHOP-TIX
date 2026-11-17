# WorkshopTIX

Sistem Pendaftaran dan Tiket Digital Workshop IT berbasis **Laravel**. Peserta dapat melihat daftar workshop, mendaftar, membayar melalui **Midtrans Sandbox**, dan memperoleh **tiket digital** setelah pembayaran terverifikasi. Panitia (admin) dapat mengelola workshop dan memantau peserta serta status pembayaran.

Dokumen kebutuhan lengkap ada di [`PRD.md`](PRD.md).

## Fitur

- Registrasi, login, logout (Laravel Breeze).
- Daftar & detail workshop (hanya workshop berstatus `published`).
- Gambar workshop di kartu dan halaman detail; jadwal dan deskripsi lengkap tersedia di detail.
- Pendaftaran dengan akun atau sebagai guest. Guest mendapat tautan privat selama 7 hari untuk membuka tiket setelah pembayaran lunas.
- Pendaftaran peserta + pencegahan pendaftaran ganda (unique `user_id` + `workshop_id`).
- Transaksi pembayaran dibuat otomatis dengan status awal `pending`, nominal **selalu dari database**.
- Integrasi Midtrans Snap (Sandbox): buat transaksi, cek status server-side, dan webhook notifikasi dengan verifikasi signature `SHA512`.
- Tiket digital diterbitkan **hanya** setelah pembayaran `settlement`/`capture`.
- Tiket hanya dapat dilihat oleh pemiliknya; tiket pending/gagal tidak dapat diakses.
- Dashboard admin: ringkasan statistik, CRUD workshop, daftar peserta & status pembayaran.
- Query dioptimalkan (eager loading / `withCount`), lihat [`docs/N+1-optimization.md`](docs/N+1-optimization.md).

## Kebutuhan

- PHP 8.3+
- Composer 2
- Node.js 20+ & npm
- MySQL

## Instalasi

```bash
composer install
npm install
cp .env.example .env        # Windows: copy .env.example .env
php artisan key:generate
```

Atur koneksi database di `.env` (MySQL `workshop_tix`), lalu:

```bash
php artisan migrate --seed
php artisan storage:link
npm run build
```

`--seed` membuat dua akun demo dan enam workshop contoh.

## Gambar workshop

Admin dapat mengunggah atau mengganti gambar workshop JPG, PNG, atau WebP (maksimal 5 MB) dari form workshop. File disimpan di disk lokal pada `storage/app/public/workshops`; jalankan `php artisan storage:link` agar gambar dapat ditampilkan melalui web.

### Akun demo

| Peran   | Email                      | Password   |
|---------|----------------------------|------------|
| Admin   | `admin@workshoptix.test`   | `password` |
| Peserta | `peserta@workshoptix.test` | `password` |

## Konfigurasi Midtrans

Tambahkan kredensial **Midtrans Sandbox** ke `.env` (jangan di-commit):

```env
MIDTRANS_IS_PRODUCTION=false
KEY_MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxx
KEY_MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxx
```

Aplikasi memakai `KEY_MIDTRANS_SERVER_KEY` untuk membuat transaksi dan memverifikasi signature. URL notifikasi (callback) yang didaftarkan di dashboard Midtrans:

```
https://<domain-publik-anda>/midtrans/notification
```

> **Catatan lokal:** Midtrans tidak dapat menjangkau `localhost`. Saat pengembangan lokal, setelah simulasi pembayaran di halaman Snap, kembali ke halaman pembayaran dan tekan **"Cek Status Pembayaran"** — status diverifikasi langsung ke API Midtrans dari sisi server. Webhook `/midtrans/notification` tetap aktif dan siap dipakai di server publik.

## Menjalankan

```bash
php artisan serve
```

Buka `http://127.0.0.1:8000`.

Untuk pengembangan frontend (hot reload):

```bash
composer run dev
```

## Pengujian

```bash
php artisan test
```

Suite mencakup autentikasi, akses berbasis peran, pendaftaran (termasuk anti-duplikasi dan guest checkout), gambar workshop, verifikasi nominal server-side, isolasi akses tiket, dan webhook pembayaran (signature & re-issue tiket).

## Struktur Singkat

| Area | Lokasi |
|------|--------|
| Model | `app/Models` (User, Workshop, Registration, Payment, Ticket) |
| Controller peserta | `app/Http/Controllers/WorkshopController`, `RegistrationController`, `PaymentController`, `TicketController` |
| Controller admin | `app/Http/Controllers/Admin/*` |
| Middleware admin | `app/Http/Middleware/EnsureUserIsAdmin.php` |
| Policy | `app/Policies/RegistrationPolicy.php` |
| Migrasi | `database/migrations` |
| Views | `resources/views` (tema hijau "festival") |

## Lisensi

Proyek ini dibuat untuk keperluan tugas SMK PESAT — Rekayasa Perangkat Lunak.

---
title: "Panduan Alur Deployment: VS Code, Firebase, dan Vercel"
slug: panduan-alur-deployment-vs-code-firebase-vercel
excerpt: "Alur kerja teknis, aturan konfigurasi, dan langkah menghubungkan proyek Laravel ke Cloud Firestore hingga rilis produksi di Vercel."
category: Tutorial
published_at: 2026-11-16 22:18
sort_order: 2
meta_description: "Panduan lengkap deployment proyek Laravel dengan database Cloud Firestore (Firebase) dari VS Code ke GitHub hingga produksi di Vercel."
is_published: true
---

Dokumentasi ini menjelaskan alur kerja teknis, aturan konfigurasi, dan langkah-langkah menghubungkan proyek Laravel dari lingkungan lokal (VS Code) ke database Cloud Firestore (Firebase) hingga rilis produksi di Vercel.

Alur utamanya dimulai dari konfigurasi lokal di VS Code, koneksi ke Firebase Firestore, push ke GitHub, lalu pengaturan environment dan deploy di Vercel.

## Konfigurasi lokal dan Firebase di VS Code

Pastikan proyek Laravel di komputer lokal sudah terhubung dan berhasil melakukan transaksi data dengan Firebase sebelum diunggah ke repositori.

Pasang library resmi Firebase untuk Laravel melalui terminal VS Code:

```bash
composer require kreait/laravel-firebase google/cloud-firestore --ignore-platform-req=ext-grpc
```

Publish file konfigurasi Firebase di Laravel:

```bash
php artisan vendor:publish --provider="Kreait\Firebase\ServiceProvider" --tag=config
```

Selanjutnya, unduh file kunci privat (Service Account) dari Firebase Console lewat menu Project Settings, lalu Service accounts, dan pilih Generate new private key. Simpan file tersebut di direktori lokal proyek dengan nama `storage/app/firebase_credentials.json`.

Tambahkan variabel berikut ke dalam file `.env` lokal Anda:

```env
FIREBASE_CREDENTIALS=storage/app/firebase_credentials.json
FIREBASE_PROJECT_ID=next-code-febe3
```

Pastikan file `storage/app/firebase_credentials.json` dan `.env` tercantum di dalam file `.gitignore` agar kunci rahasia tidak bocor ke publik saat di-push ke GitHub.

## Persiapan file konfigurasi Vercel

Karena Vercel menjalankan arsitektur serverless, Laravel memerlukan file jembatan agar dapat dieksekusi menggunakan PHP runtime.

Buat folder baru bernama `api` di root proyek Anda, lalu buat file `index.php` di dalamnya dengan isi berikut:

```php
<?php

require __DIR__ . '/../public/index.php';
```

Setelah itu, buat file `vercel.json` di root proyek Anda untuk mengatur routing dan versi runtime PHP:

```json
{
  "version": 2,
  "framework": null,
  "functions": {
    "api/index.php": {
      "runtime": "vercel-php@0.7.3"
    }
  },
  "routes": [
    {
      "src": "/(css|js|images|build)/(.*)",
      "dest": "/public/$1/$2"
    },
    {
      "src": "/(.*)",
      "dest": "/api/index.php"
    }
  ],
  "env": {
    "APP_ENV": "production",
    "APP_DEBUG": "false",
    "APP_CONFIG_CACHE": "/tmp/config.php",
    "APP_EVENTS_CACHE": "/tmp/events.php",
    "APP_PACKAGES_CACHE": "/tmp/packages.php",
    "APP_ROUTES_CACHE": "/tmp/routes.php",
    "APP_SERVICES_CACHE": "/tmp/services.php",
    "VIEW_COMPILED_PATH": "/tmp",
    "CACHE_DRIVER": "array",
    "LOG_CHANNEL": "stderr",
    "SESSION_DRIVER": "cookie"
  }
}
```

Sistem file Vercel bersifat read-only kecuali folder `/tmp`. Oleh karena itu, semua cache kompilasi Blade dan konfigurasi diarahkan ke `/tmp`.

## Push kode dari VS Code ke GitHub

Unggah seluruh perubahan konfigurasi tanpa menyertakan file rahasia ke repositori GitHub Anda:

```bash
git add .
git commit -m "Setup konfigurasi Firebase dan Vercel"
git push origin main
```

## Konfigurasi environment variables di Vercel

Karena file `firebase_credentials.json` tidak ikut terunggah ke GitHub, kredensial Firebase wajib dimasukkan melalui dashboard Vercel.

Buka file `storage/app/firebase_credentials.json` di VS Code, lalu ubah seluruh isinya menjadi satu baris memanjang dengan menghapus seluruh baris baru tanpa mengubah karakter apa pun di dalamnya.

Buka proyek Anda di Vercel, masuk ke menu Settings, lalu pilih Environment Variables. Tambahkan tiga variabel berikut:

- `APP_KEY`: Salin nilai `base64:...` persis dari file `.env` lokal Anda.
- `FIREBASE_PROJECT_ID`: Isi dengan ID Proyek Firebase Anda (`next-code-febe3`).
- `FIREBASE_CREDENTIALS`: Tempel isi JSON satu baris yang sudah disiapkan sebelumnya.

## Deploy dan verifikasi

Setelah seluruh variabel tersimpan, masuk ke tab Deployments di Vercel. Klik ikon titik tiga pada deployment terakhir, lalu pilih Redeploy agar Environment Variables baru terbaca oleh server.

Buka domain produksi Vercel Anda dan lakukan pengujian pengisian data. Terakhir, buka Firebase Console dan masuk ke Firestore Database untuk memastikan dokumen baru berhasil masuk dari server Vercel.

## Hal-hal yang perlu diingat

Ada beberapa aturan penting yang perlu diperhatikan setelah aplikasi rilis:

- Aturan keamanan Firestore (Rules) wajib diatur ke `allow read, write: if false;` sebelum rilis publik agar database hanya dapat diakses melalui backend Laravel.
- Perubahan kode di VS Code cukup diunggah dengan perintah `git push origin main` agar Vercel otomatis melakukan proses build ulang.
- Perubahan nilai Environment Variables di pengaturan Vercel mewajibkan Anda melakukan Redeploy secara manual agar perubahan diterapkan.

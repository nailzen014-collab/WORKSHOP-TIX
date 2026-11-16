# Panduan Alur Deployment: VS Code, Firebase, dan Vercel

Dokumentasi ini menjelaskan alur kerja teknis, aturan konfigurasi, dan langkah-langkah menghubungkan proyek **Laravel** dari lingkungan lokal (VS Code) ke database **Cloud Firestore (Firebase)** hingga rilis produksi di **Vercel**.

## Alur Utama
Konfigurasi Lokal (VS Code) $\rightarrow$ Koneksi Firebase Firestore $\rightarrow$ Push ke GitHub $\rightarrow$ Konfigurasi Environment & Deploy Vercel.

---

## Langkah 1: Konfigurasi Lokal & Firebase di VS Code

Pastikan proyek Laravel di komputer lokal sudah terhubung dan berhasil melakukan transaksi data dengan Firebase sebelum diunggah ke repositori.

### 1.1 Instalasi Paket & Kredensial
* Pasang library resmi Firebase untuk Laravel di terminal VS Code:
  ```bash
  composer require kreait/laravel-firebase google/cloud-firestore --ignore-platform-req=ext-grpc
  ```
* Publish file konfigurasi Firebase di Laravel:
  ```bash
  php artisan vendor:publish --provider="Kreait\Firebase\ServiceProvider" --tag=config
  ```
* Unduh file kunci privat (*Service Account*) dari **Firebase Console** (`Project Settings` $\rightarrow$ `Service accounts` $\rightarrow$ `Generate new private key`).
* Simpan file tersebut di direktori lokal proyek dengan nama `storage/app/firebase_credentials.json`.

### 1.2 Pengaturan `.env` Lokal
Tambahkan variabel berikut ke dalam file `.env` lokal Anda:

```env
FIREBASE_CREDENTIALS=storage/app/firebase_credentials.json
FIREBASE_PROJECT_ID=next-code-febe3
```

> ⚠️ **Keamanan Git:** Pastikan file `storage/app/firebase_credentials.json` dan `.env` tercantum di dalam file `.gitignore` agar kunci rahasia tidak bocor ke publik saat di-*push* ke GitHub.

---

## Langkah 2: Persiapan File Konfigurasi Vercel

Karena Vercel menjalankan arsitektur *serverless*, Laravel memerlukan file jembatan agar dapat dieksekusi menggunakan PHP runtime.

### 2.1 Buat File `api/index.php`
Buat folder baru bernama `api` di root proyek Anda, lalu buat file `index.php` di dalamnya dengan isi berikut:

```php
<?php

require __DIR__ . '/../public/index.php';
```

### 2.2 Buat File `vercel.json`
Buat file `vercel.json` di root proyek Anda untuk mengatur routing dan versi runtime PHP:

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

> **Catatan Arsitektur:** Sistem file Vercel bersifat *read-only* kecuali folder `/tmp`. Oleh karena itu, semua cache kompilasi Blade dan konfigurasi diarahkan ke `/tmp`.

---

## Langkah 3: Push Kode dari VS Code ke GitHub

Unggah seluruh perubahan konfigurasi (tanpa menyertakan file rahasia) ke repositori GitHub Anda:

```bash
git add .
git commit -m "Setup konfigurasi Firebase dan Vercel"
git push origin main
```

---

## Langkah 4: Konfigurasi Environment Variables di Vercel

Karena file `firebase_credentials.json` tidak ikut terunggah ke GitHub, kredensial Firebase wajib dimasukkan melalui dashboard Vercel.

### 4.1 Format Kredensial JSON
Buka file `storage/app/firebase_credentials.json` di VS Code, lalu ubah seluruh isinya menjadi **satu baris memanjang** (hapus seluruh *enter* / baris baru) tanpa mengubah karakter apa pun di dalamnya.

### 4.2 Input Variabel di Dashboard Vercel
Buka proyek Anda di **Vercel** $\rightarrow$ masuk ke menu **Settings** $\rightarrow$ pilih **Environment Variables**, lalu tambahkan variabel berikut:

| Key | Value | Keterangan |
| :--- | :--- | :--- |
| `APP_KEY` | `base64:...` | Salin persis dari file `.env` lokal Anda. |
| `FIREBASE_PROJECT_ID` | `next-code-febe3` | ID Proyek Firebase Anda. |
| `FIREBASE_CREDENTIALS` | `{"type":"service_account",...}` | Tempel isi JSON satu baris dari langkah 4.1. |

---

## Langkah 5: Deploy & Verifikasi

1. **Trigger Redeploy:** Setelah seluruh variabel tersimpan, masuk ke tab **Deployments** di Vercel, klik ikon titik tiga pada deployment terakhir, lalu pilih **Redeploy** agar *Environment Variables* baru terbaca oleh server.
2. **Uji Transaksi Data:** Buka domain produksi Vercel Anda (misalnya `https://nama-proyek.vercel.app`) dan lakukan pengujian pengisian data.
3. **Cek Firestore:** Buka **Firebase Console** $\rightarrow$ **Firestore Database** untuk memastikan dokumen baru berhasil masuk dari server Vercel.

---

## Hal-Hal yang Perlu Diingat

* **Aturan Keamanan Firestore (Rules):** Pastikan tab **Rules** pada Firestore diatur ke `allow read, write: if false;` sebelum rilis publik agar database hanya dapat diakses melalui backend Laravel (*Admin SDK*) dan tidak dapat dimanipulasi dari browser.
* **Perubahan Kode:** Setiap kali Anda mengubah kode di VS Code, cukup jalankan `git push origin main` dan Vercel akan otomatis melakukan proses *build* ulang.
* **Perubahan Environment Variables:** Mengubah nilai variabel di pengaturan Vercel **tidak otomatis** mengubah aplikasi yang sedang berjalan; Anda wajib melakukan **Redeploy** secara manual.

# PRODUCT REQUIREMENTS DOCUMENT (PRD)
# WORKSHOPTIX
### Sistem Pendaftaran dan Tiket Digital Workshop IT

| Informasi | Detail |
|---|---|
| Nama Proyek | WorkshopTIX |
| Kelompok | Abbad |
| Anggota | Abbad |
| Maintainer | Abbad |
| PIC | Abbad |
| Platform | Website |
| Framework | Laravel MVC |
| Database | MySQL |
| Payment Gateway | Midtrans Sandbox |
| Versi Dokumen | 1.0 |

---

# 1. Ringkasan Produk

WorkshopTIX merupakan aplikasi berbasis website yang dirancang untuk membantu panitia sekolah dalam mengelola kegiatan workshop IT berbayar.

Melalui aplikasi ini, peserta dapat melihat daftar workshop, membaca detail workshop, melakukan pendaftaran, menyelesaikan pembayaran, dan mendapatkan tiket digital setelah pembayaran berhasil diverifikasi.

Panitia dapat mengelola data workshop, melihat daftar peserta, serta memantau status pembayaran melalui dashboard admin.

**Catatan penting mengenai Payment Gateway:**

Modul payment gateway sudah dibuat sebelumnya. Oleh karena itu, pengembangan tidak perlu membangun payment gateway dari awal.

Pekerjaan yang perlu dilakukan hanya:
- Mengonfigurasi modul payment gateway yang sudah tersedia.
- Mengatur kredensial Midtrans Sandbox.
- Menghubungkan payment gateway dengan aplikasi WorkshopTIX.
- Mengatur endpoint callback atau notification.
- Melakukan pengujian transaksi dan verifikasi pembayaran.

# 2. Latar Belakang dan Permasalahan

Beberapa permasalahan yang ingin diselesaikan melalui WorkshopTIX:

1. Panitia membutuhkan sistem terpusat untuk mengelola informasi workshop.
2. Peserta membutuhkan platform untuk melihat dan memilih workshop.
3. Proses pendaftaran perlu dilakukan secara digital.
4. Pembayaran tiket membutuhkan sistem pembayaran yang terintegrasi.
5. Panitia membutuhkan informasi status pembayaran peserta.
6. Peserta membutuhkan tiket digital sebagai bukti pendaftaran.
7. Panitia membutuhkan dashboard untuk mengelola workshop dan melihat data peserta.
8. Peserta dapat memesan maksimal lima tiket workshop dan mengisi nama setiap peserta.

# 3. Tujuan Produk

Tujuan pengembangan WorkshopTIX:

1. Menyediakan platform pendaftaran workshop IT berbasis website.
2. Mempermudah peserta melihat informasi dan mendaftar workshop.
3. Menyediakan sistem pembayaran digital.
4. Menghasilkan tiket digital setelah pembayaran terverifikasi.
5. Mempermudah panitia mengelola workshop dan peserta.
6. Menjaga keamanan data melalui autentikasi dan otorisasi.
7. Mengoptimalkan query database agar aplikasi berjalan efisien.

# 4. Pengguna dan Hak Akses

## 4.1 Peserta

Peserta merupakan pengguna yang ingin mengikuti workshop.

Hak akses:
- Register akun.
- Login dan logout.
- Melihat daftar workshop.
- Melihat detail workshop.
- Melakukan pendaftaran.
- Melakukan pembayaran.
- Melihat status pembayaran.
- Melihat tiket digital miliknya.

## 4.2 Panitia / Admin

Panitia merupakan pengguna yang bertanggung jawab mengelola workshop.

Hak akses:
- Login admin.
- Mengakses dashboard.
- Menambahkan workshop.
- Mengubah informasi workshop.
- Menghapus workshop.
- Melihat daftar peserta.
- Memantau status pembayaran.

# 5. Ruang Lingkup Produk

## 5.1 Fitur yang Dikembangkan

1. Autentikasi pengguna.
2. Register, login, dan logout.
3. Halaman daftar workshop.
4. Halaman detail workshop.
5. Form pendaftaran workshop.
6. Dashboard admin.
7. CRUD data workshop.
8. Pengelolaan data peserta.
9. Integrasi modul payment gateway yang sudah dibuat.
10. Konfigurasi Midtrans Sandbox.
11. Verifikasi status pembayaran melalui backend.
12. Pembuatan tiket digital.
13. Validasi form.
14. Pencegahan pendaftaran ganda.
15. Pemesanan maksimal lima tiket per workshop dengan identitas nama untuk setiap tiket.
16. Optimasi query database dan pemeriksaan N+1.

## 5.2 Di Luar Ruang Lingkup

- Membuat payment gateway baru dari awal.
- Pembayaran uang sungguhan pada tahap pengembangan.
- Aplikasi mobile native.
- Fitur scan QR untuk absensi.
- Pengiriman tiket otomatis melalui WhatsApp atau SMS.

# 6. Alur Penggunaan Sistem

## 6.1 Alur Peserta

1. Peserta membuka website WorkshopTIX.
2. Peserta melihat daftar workshop.
3. Peserta memilih workshop.
4. Peserta membuka detail workshop.
5. Peserta melakukan register atau login.
6. Peserta mengisi nama setiap pemegang tiket, maksimal lima orang.
7. Sistem menyimpan data pendaftaran dan membuat transaksi sesuai jumlah tiket.
8. Peserta melakukan pembayaran melalui payment gateway.
9. Backend menerima dan memverifikasi status pembayaran.
10. Jika pembayaran berhasil, sistem menerbitkan satu tiket digital untuk setiap nama peserta.
11. Peserta dapat melihat seluruh tiket melalui halaman tiket saya.

## 6.2 Alur Panitia

1. Panitia membuka halaman login.
2. Panitia login menggunakan akun admin.
3. Sistem mengarahkan panitia ke dashboard.
4. Panitia dapat mengelola data workshop.
5. Panitia dapat melihat daftar peserta.
6. Panitia dapat memantau status pembayaran.

## 6.3 Alur Pembayaran

1. Sistem membuat transaksi berdasarkan harga workshop dari database.
2. Sistem menggunakan modul payment gateway yang sudah tersedia.
3. Modul dikonfigurasi menggunakan kredensial Midtrans Sandbox.
4. Peserta melakukan simulasi pembayaran.
5. Backend menerima notification/callback.
6. Backend memverifikasi status transaksi.
7. Sistem memperbarui status pembayaran.
8. Jika status lunas, tiket digital dapat diakses peserta.

# 7. Kebutuhan Fungsional

| ID | Fitur | Deskripsi |
|---|---|---|
| FR-01 | Register | Pengguna dapat membuat akun. |
| FR-02 | Login | Pengguna dapat masuk ke akun. |
| FR-03 | Logout | Pengguna dapat keluar dari akun. |
| FR-04 | Role Management | Sistem membedakan akses peserta dan admin. |
| FR-05 | Daftar Workshop | Peserta dapat melihat workshop tersedia. |
| FR-06 | Detail Workshop | Peserta dapat melihat deskripsi, harga, dan jadwal. |
| FR-07 | Pendaftaran | Peserta dapat mendaftarkan diri. |
| FR-08 | CRUD Workshop | Admin dapat mengelola data workshop. |
| FR-09 | Daftar Peserta | Admin dapat melihat peserta terdaftar. |
| FR-10 | Pembuatan Transaksi | Sistem membuat transaksi dengan status pending. |
| FR-11 | Konfigurasi Payment Gateway | Mengonfigurasi modul pembayaran yang sudah tersedia. |
| FR-12 | Verifikasi Pembayaran | Backend memverifikasi status transaksi. |
| FR-13 | Tiket Digital | Sistem menerbitkan tiket setelah pembayaran lunas. |
| FR-14 | Detail Tiket | Tiket menampilkan kode, peserta, workshop, dan jadwal. |
| FR-15 | Akses Tiket | Peserta hanya dapat melihat tiket miliknya. |
| FR-16 | Optimasi Database | Pengembang memeriksa dan mengurangi query N+1. |

# 8. Aturan Bisnis

| ID | Aturan |
|---|---|
| BR-01 | Nominal transaksi harus berasal dari database/server. |
| BR-02 | Status pembayaran awal adalah pending. |
| BR-03 | Status pembayaran diperbarui berdasarkan verifikasi backend. |
| BR-04 | Tiket hanya dapat diakses setelah pembayaran terverifikasi lunas. |
| BR-05 | Peserta hanya dapat melihat tiket miliknya. |
| BR-06 | Hanya admin yang dapat mengelola workshop. |
| BR-07 | Peserta tidak boleh mendaftar dua kali pada workshop yang sama. |
| BR-08 | Transaksi yang belum lunas tidak boleh menghasilkan tiket aktif. |
| BR-09 | Satu akun atau pesanan guest maksimal membeli lima tiket untuk satu workshop. |
| BR-10 | Setiap tiket harus memiliki nama peserta masing-masing. |
| BR-11 | Nominal transaksi adalah harga workshop dikalikan jumlah tiket. |

# 9. Integrasi Payment Gateway

## 9.1 Kondisi Saat Ini

Modul payment gateway sudah dibuat sebelumnya.

Dengan demikian, pekerjaan pada tahap ini hanya melakukan konfigurasi dan integrasi modul tersebut ke dalam aplikasi WorkshopTIX.

## 9.2 Pekerjaan yang Harus Dilakukan

1. Memeriksa source code modul payment gateway.
2. Memasukkan Server Key Midtrans Sandbox.
3. Memasukkan Client Key Midtrans Sandbox.
4. Mengatur environment ke mode Sandbox.
5. Mengonfigurasi endpoint notification/callback.
6. Menghubungkan modul pembayaran dengan proses pendaftaran workshop.
7. Memastikan nominal transaksi berasal dari server.
8. Memastikan backend memverifikasi status transaksi.
9. Menguji pembayaran berhasil, pending, gagal, dan expired.
10. Memastikan tiket hanya tersedia setelah pembayaran lunas.

**Catatan keamanan:** Kredensial rahasia harus disimpan dalam environment/config server dan tidak boleh di-hardcode atau dimasukkan ke repository publik.

## 9.3 Kriteria Keberhasilan Integrasi

- Modul payment gateway dapat digunakan oleh aplikasi.
- Transaksi Sandbox dapat dibuat.
- Peserta dapat menyelesaikan simulasi pembayaran.
- Backend dapat menerima notification.
- Status pembayaran diperbarui dengan benar.
- Tiket digital hanya muncul setelah pembayaran lunas.

# 10. Rancangan Database

Berikut rancangan awal entitas database.

| Entitas | Atribut Utama | Fungsi |
|---|---|---|
| Users | id, nama, email, password, role | Menyimpan akun pengguna. |
| Workshops | id, nama, deskripsi, harga, jadwal, lokasi, status | Menyimpan data workshop. |
| Registrations | id, user_id, workshop_id, status_pendaftaran | Menyimpan pendaftaran. |
| Payments | id, registration_id, order_id, amount, payment_status, paid_at | Menyimpan transaksi. |
| Tickets | id, registration_id, ticket_code, ticket_status, issued_at | Menyimpan tiket digital. |

Catatan: Struktur database final menyesuaikan implementasi Laravel dan modul payment gateway yang sudah tersedia.

# 11. Kebutuhan Non-Fungsional

| Kategori | Kebutuhan |
|---|---|
| Keamanan | Password harus di-hash. |
| Otorisasi | Hak akses peserta dan admin harus dibedakan. |
| Keamanan Transaksi | Kredensial payment gateway disimpan secara aman. |
| Integritas Data | Validasi server-side diterapkan. |
| Privasi | Peserta tidak dapat mengakses tiket pengguna lain. |
| Performa | Query database harus dioptimalkan. |
| Maintainability | Kode mengikuti pola MVC Laravel. |
| Kemudahan Penggunaan | Navigasi aplikasi harus mudah dipahami. |

# 12. Rancangan Halaman

| Halaman | Fungsi |
|---|---|
| Homepage | Menampilkan informasi dan daftar workshop. |
| Detail Workshop | Menampilkan informasi lengkap workshop. |
| Register | Form pendaftaran akun. |
| Login | Form login pengguna. |
| Form Pendaftaran | Form untuk mendaftar workshop. |
| Pembayaran | Menampilkan proses dan status pembayaran. |
| Tiket Saya | Menampilkan tiket digital peserta. |
| Dashboard Admin | Menampilkan ringkasan data. |
| Manajemen Workshop | Mengelola workshop. |
| Daftar Peserta | Menampilkan data peserta dan status pembayaran. |

# 13. Acceptance Criteria

Sistem dianggap berhasil apabila:

- [ ] Pengunjung dapat melihat daftar workshop.
- [ ] Peserta dapat melihat detail workshop.
- [ ] Peserta dapat register dan login.
- [ ] Peserta dapat melakukan pendaftaran.
- [ ] Sistem mencegah pendaftaran ganda.
- [ ] Satu pendaftaran dapat berisi maksimal lima tiket dan setiap tiket memiliki nama peserta.
- [ ] Total pembayaran sesuai jumlah tiket yang dipesan.
- [ ] Admin dapat melakukan CRUD workshop.
- [ ] Admin dapat melihat daftar peserta.
- [ ] Status transaksi awal adalah pending.
- [ ] Modul payment gateway yang sudah ada berhasil dikonfigurasi.
- [ ] Pembayaran Sandbox berhasil diuji.
- [ ] Backend dapat memverifikasi status pembayaran.
- [ ] Tiket digital muncul setelah pembayaran lunas.
- [ ] Peserta tidak dapat mengakses tiket pengguna lain.
- [ ] Transaksi pending atau gagal tidak menghasilkan tiket lunas.
- [ ] Optimasi N+1 dilakukan dan didokumentasikan.
- [ ] README tersedia.

# 14. Rencana Pengujian dan Demo

| No. | Skenario | Hasil yang Diharapkan |
|---|---|---|
| 1 | Login peserta | Peserta berhasil login. |
| 2 | Melihat workshop | Daftar workshop tampil. |
| 3 | Membuka detail | Detail workshop tampil. |
| 4 | Mendaftar workshop | Data pendaftaran tersimpan. |
| 5 | Mendaftar dua kali | Sistem menolak duplikasi. |
| 6 | Pembayaran Sandbox | Simulasi pembayaran berjalan. |
| 7 | Verifikasi transaksi | Status transaksi diperbarui. |
| 8 | Membuka tiket | Tiket tampil setelah lunas. |
| 9 | Akses tiket akun lain | Sistem menolak akses. |
| 10 | Login admin | Admin dapat mengelola workshop. |
| 11 | Pemeriksaan N+1 | Jumlah query sebelum dan sesudah optimasi dibandingkan. |

# 15. Sprint Backlog

| No. | Task | Estimasi | PIC |
|---|---|---|---|
| 1 | Desain halaman login/register dan homepage | 2 hari | Abbad |
| 2 | Setup Laravel dan database MySQL | 2 hari | Abbad |
| 3 | Implementasi register, login, logout, dan middleware | 2 hari | Abbad |
| 4 | Implementasi daftar dan detail workshop | 2 hari | Abbad |
| 5 | Implementasi CRUD workshop dan dashboard admin | 3 hari | Abbad |
| 6 | Implementasi pendaftaran dan validasi duplikasi | 2 hari | Abbad |
| 7 | Konfigurasi modul payment gateway yang sudah dibuat | 2 hari | Abbad |
| 8 | Pengujian Midtrans Sandbox dan callback | 2 hari | Abbad |
| 9 | Implementasi tiket digital | 2 hari | Abbad |
| 10 | Pengujian validasi dan hak akses | 2 hari | Abbad |
| 11 | Optimasi query N+1 | 2 hari | Abbad |
| 12 | README, Pull Request, merge, dan persiapan demo | 1 hari | Abbad |

# 16. Asumsi, Ketergantungan, dan Risiko

## 16.1 Asumsi dan Ketergantungan

- Source code payment gateway yang sudah dibuat tersedia.
- Kredensial Midtrans Sandbox tersedia.
- Endpoint callback dapat dijangkau oleh layanan pembayaran.
- Data workshop disediakan atau dikelola oleh panitia.

## 16.2 Risiko dan Mitigasi

| Risiko | Mitigasi |
|---|---|
| Callback tidak berjalan | Memeriksa endpoint dan log notification. |
| Status pembayaran tidak sinkron | Melakukan verifikasi transaksi melalui backend. |
| Tiket muncul sebelum lunas | Memastikan pengecekan status pembayaran di backend. |
| Kredensial bocor | Menyimpan key dalam environment/config server. |
| Konflik saat merge | Menggunakan branch dan Pull Request. |

# 17. Deliverables

Hasil akhir yang harus dikumpulkan:

1. Satu repository GitHub berisi source code WorkshopTIX.
2. Aplikasi Laravel dan database MySQL.
3. Halaman peserta dan dashboard admin.
4. Modul payment gateway yang sudah dikonfigurasi dan diuji menggunakan Midtrans Sandbox.
5. Sistem verifikasi pembayaran.
6. Tiket digital peserta.
7. README berisi cara menjalankan aplikasi.
8. Bukti pengujian fitur.
9. Dokumentasi perbandingan query sebelum dan sesudah optimasi N+1.
10. Bahan demonstrasi aplikasi.

---

**Dokumen PRD WorkshopTIX**

Kelompok: Abbad  
Anggota: Abbad  
Maintainer: Abbad  
PIC: Abbad

SMK PESAT — REKAYASA PERANGKAT LUNAK
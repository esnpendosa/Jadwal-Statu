<h1 align="center">
  📅 Jadwal Status
</h1>

<p align="center">
  <strong>Aplikasi penjadwal otomatis untuk posting konten ke berbagai platform sosial media</strong><br>
  WhatsApp Status · Instagram · Facebook · TikTok
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/Filament-3.x-FDAE4B?style=for-the-badge&logo=laravel&logoColor=white" alt="Filament">
  <img src="https://img.shields.io/badge/Node.js-18-339933?style=for-the-badge&logo=node.js&logoColor=white" alt="Node.js">
  <img src="https://img.shields.io/badge/SQLite-003B57?style=for-the-badge&logo=sqlite&logoColor=white" alt="SQLite">
</p>

---

## 📋 Daftar Isi

- [Fitur](#-fitur)
- [Persyaratan Sistem](#-persyaratan-sistem)
- [Arsitektur](#-arsitektur)
- [Setup Awal](#-setup-awal)
  - [1. Clone & Install](#1-clone--install)
  - [2. Konfigurasi Environment](#2-konfigurasi-environment)
  - [3. Konfigurasi Database](#3-konfigurasi-database)
  - [4. Setup WhatsApp Bridge](#4-setup-whatsapp-bridge)
  - [5. Daftarkan Akun di Late.dev](#5-daftarkan-akun-di-latedev)
  - [6. Storage & Symlink](#6-storage--symlink)
- [Menjalankan Aplikasi](#-menjalankan-aplikasi)
  - [Cara Cepat (Otomatis)](#cara-cepat-otomatis)
  - [Cara Manual](#cara-manual)
- [Penggunaan](#-penggunaan)
  - [Masuk ke Dashboard](#masuk-ke-dashboard)
  - [Mengisi Pengaturan API](#mengisi-pengaturan-api)
  - [Membuat Jadwal Konten](#membuat-jadwal-konten)
  - [Publish Manual Sekarang](#publish-manual-sekarang)
  - [Auto-Publish Otomatis](#auto-publish-otomatis)
- [Tipe Konten yang Didukung](#-tipe-konten-yang-didukung)
- [Troubleshooting](#-troubleshooting)
- [Struktur Proyek](#-struktur-proyek)
- [Lisensi](#-lisensi)

---

## ✨ Fitur

| Fitur | Keterangan |
|-------|------------|
| 📱 **WhatsApp Status** | Upload gambar/video langsung ke Status WA via Node.js Bridge (Baileys) |
| 📸 **Instagram Story & Post** | Posting ke Story atau Feed Instagram via Late API |
| 👤 **Facebook Story & Post** | Posting ke Story atau Feed Facebook via Late API |
| 🎵 **TikTok Story/Video** | Upload konten ke TikTok via Late API |
| ⏰ **Penjadwalan Otomatis** | Konten diposting otomatis setiap menit sesuai jadwal yang ditentukan |
| 🖼️ **Multi-Tipe Konten** | Pilih apakah konten akan menjadi **Story/Status** atau **Postingan (Feed)** |
| 🎛️ **Dashboard Admin** | Antarmuka Filament yang modern untuk mengelola semua konten |
| 📊 **Status Tracking** | Pantau status setiap konten: Pending, Posted, atau Failed |

---

## 💻 Persyaratan Sistem

Pastikan sistem Anda sudah memiliki:

| Komponen | Versi Minimum |
|----------|---------------|
| **PHP** | 8.2+ |
| **Composer** | 2.x |
| **Node.js** | 18.x |
| **npm** | 9.x |
| **Git** | Terbaru |

> 💡 **Direkomendasikan:** Gunakan [Laragon](https://laragon.org/) di Windows untuk manajemen PHP, Node.js, dan server lokal yang mudah.

---

## 🏗️ Arsitektur

```
Browser (Filament Admin)
        │
        ▼
Laravel App (port 8000)
        │
        ├─── WhatsApp Bridge ──► Node.js Server (port 3000)
        │                              │
        │                         Baileys Library
        │                              │
        │                        WhatsApp Web
        │
        └─── Late API ──────────► getlate.dev
                                       │
                                  Instagram / Facebook / TikTok

Laravel Scheduler (setiap menit)
        │
        └─── Cek post pending yg sudah waktunya ──► Publish otomatis
```

---

## 🚀 Setup Awal

### 1. Clone & Install

```bash
# Clone repositori
git clone https://github.com/esnpendosa/Jadwal-Statu.git
cd Jadwal-Statu

# Install dependensi PHP
composer install

# Install dependensi Node.js (untuk WhatsApp Bridge)
cd wa-bridge
npm install
cd ..

# Install dependensi Node.js utama (opsional, untuk Vite)
npm install
```

### 2. Konfigurasi Environment

Salin file `.env.example` dan edit konfigurasi:

```bash
copy .env.example .env
php artisan key:generate
```

Edit file `.env` dengan teks editor:

```env
APP_NAME="Jadwal Status"
APP_ENV=local
APP_DEBUG=true

# ⚠️ PENTING: Gunakan 127.0.0.1:8000 (BUKAN localhost)
# agar preview gambar tidak kena CORS error
APP_URL=http://127.0.0.1:8000

# ⚠️ PENTING: Set timezone ke Asia/Jakarta (WIB)
# agar jadwal posting otomatis berjalan di waktu yang tepat
APP_TIMEZONE=Asia/Jakarta

# Database SQLite (tidak perlu konfigurasi tambahan)
DB_CONNECTION=sqlite

# Queue untuk background jobs
QUEUE_CONNECTION=database

# === API KEYS (bisa juga diisi via halaman Pengaturan di dashboard) ===

# Late API (Instagram, Facebook, TikTok)
LATE_API_KEY=sk_xxxxxxxxxxxxxxxxxxxxxxxx
LATE_PROFILE_ID=xxxxxxxxxxxxxxxxxxxxxxxx
LATE_API_URL=https://getlate.dev/api/v1

# Fonnte API (WhatsApp - opsional, jika pakai Fonnte)
FONNTE_TOKEN=xxxxxxxxxxxxxxxxxxxxxxxx
FONNTE_API_URL=https://api.fonnte.com/send
```

> ⚠️ **PENTING #1:** `APP_URL` **wajib** `http://127.0.0.1:8000` — mencegah CORS error saat preview gambar.

> ⚠️ **PENTING #2:** `APP_TIMEZONE` **wajib** `Asia/Jakarta` — tanpa ini, jadwal posting akan meleset 7 jam (terlambat dipublish karena perbandingan UTC vs WIB).

### 3. Konfigurasi Database

```bash
# Buat file database SQLite
New-Item -ItemType File -Path "database\database.sqlite" -Force   # PowerShell
# atau:
type nul > database\database.sqlite   # CMD

# Jalankan migrasi
php artisan migrate

# Buat akun admin untuk login ke dashboard
php artisan make:filament-user
```

Ikuti prompt untuk mengisi nama, email, dan password admin.

### 4. Setup WhatsApp Bridge

WhatsApp Bridge menggunakan **Baileys** (library Node.js) untuk mengirim Status WA tanpa API berbayar.

```bash
cd wa-bridge
node server.js
```

Saat pertama kali dijalankan, akan muncul **QR Code** di terminal. Scan dengan WhatsApp:
1. Buka WhatsApp di HP
2. Ketuk titik tiga sudut kanan atas → **Perangkat Tertaut**
3. Ketuk **Tautkan Perangkat**
4. Scan QR Code yang muncul di terminal

Setelah berhasil, terminal akan menampilkan `WhatsApp Connected!` dan bridge berjalan di `http://127.0.0.1:3000`.

> 📌 **Catatan:** QR Code hanya muncul sekali. Sesi login tersimpan di folder `wa-bridge/auth_info_baileys/`. Jangan hapus folder ini agar tidak perlu scan ulang.

### 5. Daftarkan Akun di Late.dev

Late.dev adalah layanan yang digunakan untuk posting ke Instagram, Facebook, dan TikTok.

1. Daftar akun di [getlate.dev](https://getlate.dev)
2. Buat **Profile** baru
3. Hubungkan akun Instagram, Facebook, dan/atau TikTok
4. Salin **API Key** dari menu Settings → API Keys
5. Salin **Profile ID** dari URL atau halaman Profile

### 6. Storage & Symlink

```bash
# Buat symlink public storage agar media bisa diakses browser
php artisan storage:link
```

---

## ▶️ Menjalankan Aplikasi

### Cara Cepat (Otomatis)

Klik 2x file **`MULAI_OTOMATIS.bat`** di root proyek. File ini akan membuka 4 jendela terminal:

| Window | Service | Keterangan |
|--------|---------|------------|
| 1 | **WhatsApp Bridge** | Node.js server di port 3000 |
| 2 | **Laravel Server** | Web app di `http://127.0.0.1:8000` |
| 3 | **Laravel Scheduler** | Cek dan publish konten setiap menit |
| 4 | **Queue Worker** | Proses background jobs |

> 🔴 **Jangan tutup window-window ini** selama ingin auto-publish berjalan.

### Cara Manual

Buka 4 terminal terpisah, jalankan masing-masing:

```bash
# Terminal 1: WhatsApp Bridge
cd wa-bridge
node server.js

# Terminal 2: Laravel Web Server
php artisan serve

# Terminal 3: Laravel Scheduler (auto-publish setiap menit)
php artisan schedule:work

# Terminal 4: Queue Worker
php artisan queue:work --sleep=3 --tries=3
```

---

## 📖 Penggunaan

### Masuk ke Dashboard

1. Pastikan semua service sudah berjalan
2. Buka browser dan akses: **`http://127.0.0.1:8000/admin`**
3. Login dengan akun admin yang dibuat tadi

### Mengisi Pengaturan API

Sebelum bisa posting, isi API Key terlebih dahulu:

1. Klik menu **⚙️ Pengaturan API** di sidebar kiri
2. Isi **Late API Key** — dapatkan dari [dashboard getlate.dev](https://getlate.dev) → Settings → API Keys
3. Isi **Late Profile ID** — salin dari halaman Profile di getlate.dev
4. (Opsional) Isi **Fonnte Token** jika menggunakan Fonnte untuk WhatsApp
5. Klik **Simpan Pengaturan**

### Membuat Jadwal Konten

1. Klik menu **✈️ Jadwal Status** di sidebar
2. Klik tombol **+ Buat Status**
3. Isi form:

   | Field | Keterangan |
   |-------|------------|
   | **Upload Media** | Upload gambar (JPG/PNG/WEBP) atau video (MP4/MOV), maks 100MB |
   | **Caption** | Teks yang menyertai konten (maks 2000 karakter) |
   | **Tipe Konten** | `📖 Story/Status` — untuk WA Status & Stories sosmed; `🖼️ Postingan (Feed)` — untuk posting ke feed/timeline |
   | **Platform Tujuan** | Pilih satu atau lebih platform yang menjadi target posting |
   | **Waktu Jadwal** | Pilih tanggal dan waktu kapan konten akan diposting otomatis |

4. Klik **Simpan** — status akan menjadi **Pending** ⏳

### Publish Manual Sekarang

Jika ingin langsung publish tanpa menunggu jadwal:

1. Di halaman daftar **Jadwal Status**, cari konten yang ingin diposting
2. Klik tombol **🚀 Publish Sekarang** di kolom aksi
3. Konfirmasi dengan klik **Publish Sekarang** di dialog
4. Status akan berubah menjadi **Posted** ✅ atau **Failed** ❌

### Auto-Publish Otomatis

Selama **Laravel Scheduler** (`php artisan schedule:work`) berjalan:

- Setiap **1 menit**, sistem akan mengecek semua post yang berstatus `pending` dan waktu jadwalnya sudah lewat
- Post yang memenuhi syarat akan dipublikasikan secara otomatis ke semua platform yang dipilih
- Status akan diperbarui menjadi `posted` atau `failed` beserta pesan errornya

---

## 🎯 Tipe Konten yang Didukung

### 📖 Story / Status

| Platform | Keterangan |
|----------|------------|
| 📱 **WhatsApp Status** | Gambar/video tampil di Status WA selama 24 jam |
| 📸 **Instagram Story** | Story di Instagram selama 24 jam |
| 👤 **Facebook Story** | Story di Facebook selama 24 jam |
| 🎵 **TikTok Story** | Video pendek di TikTok |

### 🖼️ Postingan (Feed)

| Platform | Keterangan |
|----------|------------|
| 📸 **Instagram Post** | Foto/video permanen di feed Instagram |
| 👤 **Facebook Post** | Post permanen di timeline Facebook |
| 🎵 **TikTok Video** | Video di halaman TikTok Anda |

> 💡 **Tips:** Untuk satu konten bisa memilih beberapa platform sekaligus. Misalnya, posting ke Instagram Post + Facebook Post bersamaan.

---

## 🔧 Troubleshooting

### ❌ CORS Error saat upload/preview gambar

**Gejala:** `Access-Control-Allow-Origin header is not present`

**Solusi:**
- Pastikan `APP_URL` di `.env` adalah `http://127.0.0.1:8000` (bukan `http://localhost`)
- Jalankan `php artisan config:clear`
- Akses dashboard via `http://127.0.0.1:8000/admin` (bukan `localhost/admin`)

---

### ❌ WhatsApp Bridge tidak aktif

**Gejala:** Error `Bridge Exception: Pastikan Node bridge di port 3000 aktif`

**Solusi:**
1. Pastikan terminal `WhatsApp Bridge` (window 1) terbuka dan berjalan
2. Cek apakah ada error di terminal tersebut
3. Jika QR Code muncul lagi, scan ulang dengan WhatsApp

---

### ❌ Schedule tidak berjalan otomatis

**Gejala:** Post tetap `pending` meski waktu jadwal sudah lewat

**Solusi:**
1. Pastikan terminal **Laravel Scheduler** (`php artisan schedule:work`) berjalan
2. Cek log di `storage/logs/laravel.log`
3. Test manual: `php artisan status:publish`

---

### ❌ Late API gagal (Instagram/Facebook/TikTok)

**Gejala:** Error `No accounts found` atau `No connected accounts matched`

**Solusi:**
1. Pastikan API Key dan Profile ID sudah benar di halaman **Pengaturan API**
2. Pastikan akun sosmed sudah terhubung di [dashboard getlate.dev](https://getlate.dev)
3. Cek log: `storage/logs/laravel.log` untuk detail error

---

### ❌ Halaman admin tidak bisa diakses

**Gejala:** 404 atau redirect ke halaman kosong

**Solusi:**
```bash
php artisan migrate
php artisan make:filament-user
php artisan config:clear
php artisan route:clear
```

---

## 📁 Struktur Proyek

```
Jadwal-Statu/
├── app/
│   ├── Console/Commands/
│   │   └── PublishStatus.php        # Command auto-publish (dipanggil scheduler)
│   ├── Filament/
│   │   ├── Pages/
│   │   │   └── Settings.php         # Halaman pengaturan API
│   │   └── Resources/
│   │       └── PostResource.php     # CRUD jadwal konten
│   ├── Models/
│   │   ├── Post.php                 # Model konten/jadwal
│   │   └── Setting.php             # Model penyimpanan setting API
│   └── Services/
│       ├── LateService.php          # Integrasi Late API (IG, FB, TikTok)
│       └── WhatsAppService.php      # Integrasi WA Bridge
│
├── database/migrations/             # Migrasi database
├── routes/
│   ├── console.php                  # Definisi jadwal (schedule:work)
│   └── web.php                      # Route web
│
├── wa-bridge/
│   ├── server.js                    # Node.js bridge untuk WhatsApp Baileys
│   └── package.json
│
├── .env.example                     # Template konfigurasi
├── MULAI_OTOMATIS.bat              # Script untuk menjalankan semua service
└── README.md                        # Dokumentasi ini
```

---

## 📄 Lisensi

Proyek ini dibuat untuk keperluan pribadi dan open source. Silakan fork dan modifikasi sesuai kebutuhan.

---

<p align="center">
  Dibuat dengan ❤️ menggunakan Laravel + Filament
</p>

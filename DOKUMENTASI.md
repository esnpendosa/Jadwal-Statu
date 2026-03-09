# Dokumentasi Smart Inventory Management System

## 📌 Pendahuluan
Smart Inventory Management System adalah aplikasi manajemen inventaris tingkat enterprise yang dirancang untuk mengelola aset, peminjaman, dan pengembalian barang berbasis proyek. Sistem ini dilengkapi dengan AI Suggestion Engine dan Risk Scoring System untuk memantau integritas personel dalam penggunaan alat perusahaan.

## 🛠 Teknologi Utama
- **Backend**: Laravel 12.x
- **Frontend**: Blade + Tailwind CSS v4 + Alpine.js
- **Database**: MySQL
- **Integarsi**: Google Calendar API (untuk jadwal proyek)

---

## 🚀 Fitur Utama

### 1. Manajemen Inventaris (Inventory)
- **Pelacakan Stok**: Mengelola stok total, stok tersedia, dan stok yang sedang dipinjam.
- **Kategori & Kondisi**: Pengelompokan barang berdasarkan kategori dan pemantauan kondisi (Baik, Rusak, dll).
- **Riwayat Penyesuaian**: Setiap perubahan stok dicatat dalam riwayat untuk transparansi.

### 2. Manajemen Proyek (Projects)
- **Penugasan Personel**: Setiap proyek memiliki Manager dan PIC (Person In Charge).
- **Timeline Proyek**: Pemantauan tanggal mulai dan selesai proyek yang terintegrasi dengan Google Calendar.
- **Risk Score Proyek**: Akumulasi skor risiko berdasarkan perilaku peminjaman alat di proyek tersebut.

### 3. Alur Peminjaman & Pengembalian (Borrow & Return)
- **Peminjaman**: PIC mengajukan peminjaman alat untuk proyek tertentu. Admin/Manager melakukan persetujuan.
- **Pengembalian**: Proses verifikasi jumlah dan kondisi barang saat kembali.
- **Deteksi Keterlambatan**: Sistem otomatis menandai peminjaman yang melewati batas waktu kembali (Overdue).

### 4. Risk Scoring System (Sistem Skor Risiko)
- **Poin Pelanggaran**: Personel mendapatkan poin jika terlambat mengembalikan barang, barang rusak, atau jumlah tidak sesuai.
- **Level Risiko**:
  - **Low**: Aman.
  - **Medium**: Dalam pantauan.
  - **High/Critical**: Peminjaman dibatasi atau memerlukan persetujuan ekstra.

### 5. AI Suggestion Engine
- Memberikan saran otomatis berdasarkan pola perilaku. Contoh: "Personel X sering terlambat mengembalikan alat, disarankan pelatihan manajemen waktu."

---

## 👥 Peran Pengguna (Roles)
1. **Super Admin**: Akses penuh ke seluruh sistem, pengaturan, dan log audit.
2. **Admin Gudang**: Fokus pada manajemen barang, stok, dan verifikasi pengembalian.
3. **Manager**: Mengelola proyek, menyetujui peminjaman, dan melihat laporan analitik.
4. **PIC Project**: Mengajukan peminjaman dan mengelola alat di lapangan.

---

## 📖 Cara Penggunaan

### A. Menambah Barang Baru
1. Masuk sebagai **Admin Gudang** atau **Super Admin**.
2. Buka menu **Inventori** > **Tambah Baru**.
3. Isi detail barang, pilih kategori, dan tentukan stok awal.
4. Klik Simpan.

### B. Proses Peminjaman
1. **PIC** membuka menu **Peminjaman** > **Ajukan Peminjaman**.
2. Pilih barang, tentukan jumlah dan proyek yang terkait.
3. Tunggu persetujuan dari **Manager**.
4. Setelah disetujui, pindahkan status menjadi **Borrowed** saat barang diambil.

### C. Proses Pengembalian
1. Buka menu **Peminjaman**, pilih transaksi yang aktif.
2. Klik tombol **Kembalikan**.
3. Verifikasi jumlah barang yang kembali dan kondisinya.
4. Jika ada kerusakan, unggah foto bukti kerusakan.

### D. Melihat Laporan
1. Buka menu **Laporan**.
2. Pilih jenis laporan: **Inventori**, **Sirkulasi (Peminjaman)**, atau **Analisis Risiko**.
3. Gunakan filter untuk melihat data spesifik dan klik **Cetak/PDF** untuk mengunduh.

---

## 🌓 Mode Terang & Gelap
Sistem mendukung mode tampilan yang konsisten. Anda dapat mengganti mode melalui ikon matahari/bulan di bar navigasi atas.

## 🌐 Ganti Bahasa
Untuk mengganti bahasa (Indonesia, Inggris, Mandarin):
1. Klik nama profil Anda di pojok kanan atas.
2. Pilih menu **Profil**.
3. Pilih bahasa yang diinginkan pada bagian **Bahasa Aplikasi**.
4. Sistem akan otomatis memperbarui seluruh teks sesuai bahasa terpilih.

---
*Dikembangkan dengan standar Enterprise oleh Antigravity AI.*

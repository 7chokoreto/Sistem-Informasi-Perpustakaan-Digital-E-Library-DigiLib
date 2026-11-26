# 📚 DigiLib - Sistem Informasi Perpustakaan Digital Terpadu

> **Tugas Besar: Project-Based Learning (Pengembangan Front-End Admin Panel)**  
> **Mata Kuliah:** Pemrograman Web 2 (Client-Side Programming)  
> **Estetika Desain:** Neumorphism (Soft UI Architecture)

---

## 📖 Tentang Proyek
**DigiLib** adalah aplikasi sistem administrasi dan sirkulasi perpustakaan digital terpadu yang memadukan estetika desain **Neumorphic Soft UI** (`#e6ecf5`) dengan kejelasan arsitektur informasi dan interaktivitas client-side yang responsif. 

Sistem ini mengadopsi alur sirkulasi peminjaman mandiri berbasis cabang pengambilan (referensi standar perpustakaan modern), manajemen inventaris koleksi buku fisik, kalkulasi denda otomatis, hingga pengelolaan hak akses multi-peran secara real-time.

---

## 🎨 Arsitektur Desain (Neumorphic Soft UI)
* **Link Desain Figma:** [Figma Project - DigiLib UI Design System](https://www.figma.com/design/8enUtgdAB71gVU3fzmbVLN/Sistem-Perpustakaan-DigiLib?t=K3UxiWcPvga1QF4y-0)
* **Warna Dasar Canvas:** `#E6ECF5` (Soft Powder Neumorphic Canvas)
* **Warna Elemen/Surface:** `#E9EFF8` / `#EAF0F9`
* **Brand Primary:** `#2563EB` (Royal Blue) & `#1D4ED8` (Deep Blue)
* **Shadow Tokens:** Dual soft outer shadows (embossed cards/pills) & inset inner shadows (debossed input forms).
* **Tipografi:** *Plus Jakarta Sans*

---

## 👥 Fitur Utama Berdasarkan Peran (Multi-Role)

### 1. 🛡️ Superadmin (Admin Panel Utama)
* **Dashboard Analitik:** Visualisasi pertumbuhan akun member & staf menggunakan library **Chart.js** (Line Chart tren pertumbuhan & Donut Chart rasio akun).
* **Manajemen Akun Pengguna:** 
  * Tambah & ubah akun pustakawan/member.
  * Upload, pratinjau instan (*live preview*), ganti, dan hapus foto profil pustakawan.
  * Tangguhkan (*suspend*) dan aktifkan kembali akun member dengan keterangan alasan penangguhan.
  * Hapus akun pengguna secara permanen.

### 2. 📖 Pustakawan (Layanan Sirkulasi & Inventaris)
* **Dashboard Operasional:** Metrik sirkulasi real-time (total judul, eksemplar fisik, pinjaman aktif, denda).
* **Katalog Master Buku:** Inventarisasi buku, pencarian instan, filter kategori, tambah/edit data buku dengan upload sampul lokal, serta pengaturan lokasi rak dan ketersediaan cabang.
* **Layanan Sirkulasi Peminjaman:** 
  * Validasi pengajuan pinjam online (Setujui atau Tolak dengan alasan).
  * Penyerahan buku fisik di meja resepsionis.
  * Konfirmasi penerimaan kembali buku & perhitungan denda keterlambatan otomatis (Rp 1.000 / hari).
* **Data Anggota:** Monitoring keanggotaan dan modal riwayat peminjaman per anggota.

### 3. 👤 Member (Portal Anggota Perpustakaan)
* **Beranda & Rekomendasi:** Banner sambutan, peringatan jika akun ditangguhkan, dan kartu buku pilihan.
* **Katalog & Detail Buku:** Pencarian judul/penulis, filter kategori, detail bibliografi lengkap, ketersediaan per cabang perpustakaan, dan modal bagikan (*share*) interaktif ke WhatsApp, X, Instagram, serta salin tautan.
* **Keranjang Peminjaman & Checkout:** Pemilihan cabang ketersediaan, pemesanan multi-buku, konfirmasi persetujuan syarat & ketentuan, serta pemilihan lokasi pengambilan.
* **Aktivitas & Riwayat:** Pelacakan status peminjaman (Menunggu Validasi, Siap Diambil, Sedang Dipinjam, Selesai, Ditolak).
* **Akun Saya:** Pengaturan profil, upload avatar, dan pembaruan kata sandi (*toggle show/hide password*).

---

## 📁 Struktur Direktori & Folder Proyek

Proyek ini disusun dan diorganisasikan dengan rapi sesuai kaidah arsitektur standar:

```
PWEB 2/
├── 📄 README.md                        <-- Dokumentasi Utama Repositori
├── 📄 composer.json                    <-- Dependensi Framework & Package PHP
├── 📄 .env.example                     <-- Template Konfigurasi Environment
├── 📂 docs/
│   └── 📄 perancangan.md               <-- Tugas Milestone 1 (ER-D, Flow, Design System, Link Figma)
│
├── 📂 public/                          <-- Direktori Publik Web Server
│   ├── 📄 index.php                    <-- Front Controller Entry Point
│   ├── 📂 uploads/                     <-- Penyimpanan Berkas Dinamis (Cover Buku & Avatar)
│   │   ├── 📂 covers/
│   │   └── 📂 avatars/
│   └── 📂 assets/                      <-- Aset Client-Side
│       ├── 📂 css/
│       │   ├── 📄 style.css            <-- Neumorphic Core Design System
│       │   ├── 📄 superadmin.css       <-- Styling Panel Superadmin
│       │   ├── 📄 pustakawan.css       <-- Styling Layanan Sirkulasi Pustakawan
│       │   └── 📄 member.css           <-- Styling Portal Anggota
│       ├── 📂 js/                      <-- Script Interaktivitas Client-Side
│       └── 📂 img/                     <-- Gambar & Aset Ilustrasi
│
├── 📂 resources/
│   └── 📂 views/                       <-- Antarmuka Pengguna (Blade Templates)
│       ├── 📂 auth/                    <-- Halaman Login, Registrasi, Lupa Sandi, OTP
│       ├── 📂 components/              <-- Komponen Reusable (Sidebar, Modal Logout, Toast)
│       ├── 📂 layouts/                 <-- Master Layout (app.blade.php)
│       ├── 📂 superadmin/              <-- View Dashboard & Manajemen Akun
│       ├── 📂 pustakawan/              <-- View Dashboard, Buku, Peminjaman, Anggota
│       └── 📂 member/                  <-- View Beranda, Koleksi, Detail, Keranjang, Aktivitas
│
├── 📂 app/
│   ├── 📂 Http/Controllers/            <-- Logika Kontroler (Auth, Member, Pustakawan, Superadmin)
│   └── 📂 Models/                      <-- Model Data (User, Book, Loan, CartItem, Favorite)
│
├── 📂 routes/
│   └── 📄 web.php                      <-- Definisi Routing & Middleware Multi-Role
│
├── 📂 database/
│   ├── 📂 migrations/                  <-- Skema Tabel Database
│   └── 📂 seeders/                     <-- Data Awal Uji Coba (Buku, Pengguna, Transaksi)
│
├── 📂 pages/                           <-- Arsip Slicing Desain HTML Statis (Milestone 2)
│   ├── 📄 dashboard.html
│   ├── 📄 data-buku.html
│   ├── 📄 peminjaman.html
│   └── 📄 layout.html
│
└── 📂 tests/
    └── 📂 Feature/                     <-- Pengujian Otomatis (Auth, Role, CRUD & Fitur)
```

---

## 🛠️ Teknologi yang Digunakan
* **Front-End:** HTML5 Semantik, CSS3 (CSS Grid & Flexbox), Neumorphic Custom Design System, JavaScript Vanilla (DOM Manipulation, Modal `<dialog>`, FileReader API).
* **Data Visualization:** Chart.js
* **Icons:** FontAwesome 6
* **Back-End Framework:** Laravel 11 (PHP 8.2+)
* **Database:** MySQL / SQLite
* **Dokumentasi & Diagram:** Mermaid.js (`docs/perancangan.md`)

---

## 🚀 Panduan Menjalankan Aplikasi Secara Lokal

1. **Clone repository ini:**
   ```bash
   git clone https://github.com/username-anda/digilib-pweb2.git
   cd digilib-pweb2
   ```

2. **Instal dependensi PHP:**
   ```bash
   composer install
   ```

3. **Konfigurasi Environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Migrasi & Seeder Database:**
   ```bash
   php artisan migrate --seed
   ```

5. **Jalankan Local Development Server:**
   ```bash
   php artisan serve
   ```
   Akses aplikasi di browser pada: `http://127.0.0.1:8000`

---

## 📄 Akun Uji Coba Default (Demo Credentials)
| Peran (Role) | Email | Kata Sandi |
| :--- | :--- | :--- |
| **Superadmin** | `superadmin@digilib.id` | `password` |
| **Pustakawan** | `pustakawan@digilib.id` | `password` |
| **Member** | `member@digilib.id` | `password` |

---
*Dibuat untuk memenuhi Tugas Besar Mata Kuliah Pemrograman Web 2.*

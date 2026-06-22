# 🛒 kasir_sederhana - Aplikasi Transaksi POS Modern

Aplikasi **kasir_sederhana** adalah sistem Point of Sale (POS) berbasis web yang dirancang untuk mendigitalisasi proses transaksi penjualan, manajemen produk, dan pencatatan riwayat transaksi secara real-time. Proyek ini dibangun sebagai bagian dari tugas praktikum pengembangan perangkat lunak kelas 10 PPLG.

## ✨ Fitur Utama
- **Multi-Role Authentication:** Pembagian hak akses halaman antara Admin dan Kasir secara dinamis.
- **Pilih Produk & Kuantitas Massal:** Memungkinkan kasir memasukkan jumlah item (quantity) dalam skala besar sekaligus sebelum dimasukkan ke keranjang belanja.
- **Sistem Barcode Otomatis:** Enkripsi ID Produk dan ID Transaksi menjadi kode batang secara dinamis menggunakan library JsBarcode pada kartu produk dan struk belanja.
- **Keranjang Interaktif:** Perhitungan subtotal, perubahan jumlah barang (tambah/kurang), dan reset keranjang menggunakan integrasi JavaScript dan penyimpanan lokal (*Local Storage*).

## 🔑 Akun Demo Pengujian (Login)
Untuk keperluan pengujian dan penilaian sistem, silakan gunakan akun pendaftaran berikut yang sudah tersedia di database:

| Role | Username | Password | Hak Akses |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin` | `123456` | Manajemen Produk (CRUD), Akses Dashboard Utama, & Transaksi Kasir |
| **Kasir** | `gazaboy` | `gazakece` | Akses Halaman Transaksi Kasir & Riwayat Penjualan |

## 🛠️ Teknologi & Stack
- **Backend:** PHP (Object-Oriented Programming / OOP & Session Management)
- **Frontend:** HTML5, CSS3 (Modern Dashboard Responsive Grid Layout), JavaScript (Vanilla JS)
- **Database:** MySQL / MariaDB

## 🚀 Panduan Instalasi di Lokal (XAMPP)

1. **Clone / Unduh Proyek:**
   Unduh proyek ini dan letakkan foldernya di dalam direktori web server Anda:
```bash
   C:\xampp\htdocs\kasir_sederhana

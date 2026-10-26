# 🏛️ Medan Smart Tourism

Aplikasi berbasis web untuk digitalisasi pemesanan tiket wisata, pengelolaan antrean, dan sistem *check-in* menggunakan QR Code di Kota Medan. Proyek ini dikembangkan untuk memenuhi Tugas Ujian Tengah Semester (UTS) mata kuliah E-Application, Program Studi D3 Teknik Informatika.

## 📖 Deskripsi Proyek

Sistem pemesanan tiket dan antrean tempat wisata di Kota Medan saat ini masih banyak yang dikelola secara manual, sehingga rentan menimbulkan penumpukan pengunjung di loket. Aplikasi **Medan Smart Tourism** hadir untuk menyelesaikan masalah tersebut dengan menyediakan platform terpadu. Pengunjung dapat melakukan pemesanan tiket secara *online*, mendapatkan nomor antrean digital, dan melakukan *check-in* di lokasi hanya dengan memindai (*scan*) QR Code.

## ✨ Fitur Utama

- **Multi-Role Authentication**: Akses terpisah untuk User (Wisatawan), Admin (Pengelola), dan Petugas (Penjaga Loket).
- **Manajemen Wisata (CRUD)**: Admin dapat menambah, mengubah, menghapus, dan mengelola destinasi wisata, jadwal, dan informasi tiket.
- **Pemesanan & Keranjang Belanja**: Pengunjung dapat memilih destinasi, memasukkan ke keranjang, dan melakukan *checkout*.
- **Simulasi Payment Gateway**: Integrasi pembayaran *dummy* dengan perubahan status otomatis (Pending, Lunas, Batal).
- **E-Tiket & Antrean Digital**: Pengunjung yang telah membayar akan menerima e-tiket beserta nomor antrean yang digenerate secara otomatis.
- **QR Code Check-in**: Petugas lapangan dapat memindai QR Code pengunjung menggunakan kamera perangkat untuk melakukan validasi tiket masuk (*Check-in*).

## 🛠️ Teknologi yang Digunakan

- **Backend**: PHP Native (PDO)
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, Bootstrap 5.3.3
- **Typography**: Plus Jakarta Sans (Google Fonts)

## ⚙️ Persyaratan Sistem (Prerequisites)

- Web Server lokal seperti [XAMPP](https://www.apachefriends.org/index.html) atau [Laragon](https://laragon.org/).
- PHP versi 7.4 atau lebih baru (direkomendasikan PHP 8.x).
- MySQL Server.

## 🚀 Cara Instalasi

1. **Unduh Proyek**
   Clone repositori ini atau *download* sebagai file ZIP dan ekstrak.
   ```bash
   git clone [https://github.com/username-anda/medan-smart-tourism.git](https://github.com/username-anda/medan-smart-tourism.git)

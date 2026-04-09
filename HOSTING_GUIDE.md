# Panduan Deployment Laravel 10 ke Shared Hosting (cPanel) Menggunakan Git

Dokumen ini adalah panduan lengkap langkah demi langkah untuk melakukan *deployment* aplikasi Skinbae.ID ke *Shared Hosting* yang menggunakan **cPanel**. Panduan ini disusun agar mudah dipahami oleh pengembang manusia maupun *AI Assistant* yang bertugas mengelola infrastruktur.

---

## 📋 Prasyarat (Pre-requisites)
Sebelum memulai, pastikan Anda memiliki:
1. Akses ke **cPanel** hosting Anda.
2. Akses ke **Terminal** atau **SSH** di dalam cPanel (Sangat disarankan, meskipun ada alternatif jika tidak ada).
3. Repositori GitHub/GitLab publik atau privat yang sudah berisi *source code* final (termasuk folder `public/build` dari hasil `npm run build`).
4. Akses ke database MySQL di cPanel.

---

## 🚀 Tahap 1: Persiapan Database di cPanel

1. Buka cPanel dan cari menu **MySQL® Databases**.
2. Buat database baru (misal: `u123456_skinbae`).
3. Buat *user* database baru dan buat password yang kuat.
4. Tambahkan *user* tersebut ke database yang baru dibuat, dan berikan opsi **All Privileges**.
5. Buka menu **phpMyAdmin** di cPanel.
6. *Import* file SQL dari lokal komputer Anda ke database hosting (jika Anda ingin memindahkan data yang sudah ada), atau jalankan migrasi nanti lewat terminal.

---

## 🐙 Tahap 2: Clone Repository via Git Version Control di cPanel

1. Buka cPanel dan cari menu **Git Version Control**.
2. Klik tombol **Create** untuk membuat repositori baru.
3. Isi form berikut:
   *   **Clone URL**: Masukkan URL repository Anda (contoh: `https://github.com/Shiroizx/E-Beauty.git`).
   *   **Repository Path**: Tentukan folder tempat aplikasi akan diletakkan. **PENTING:** Jangan letakkan di dalam folder `public_html`. Buat folder baru sejajar dengan `public_html` (misal: `skinbae_app`).
   *   **Repository Name**: Beri nama bebas (misal: `Skinbae E-Beauty`).
4. Klik **Create** dan tunggu proses *cloning* selesai.

---

## ⚙️ Tahap 3: Konfigurasi Environment (`.env`)

1. Buka menu **File Manager** di cPanel.
2. Masuk ke folder aplikasi Anda (misal: `skinbae_app`).
3. Cari file `.env.example`, klik kanan, lalu pilih **Copy**. Beri nama salinan tersebut `.env`.
4. Edit file `.env` yang baru dibuat. Sesuaikan konfigurasi berikut:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://domain-anda.com

   # Konfigurasi Database dari Tahap 1
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=u123456_skinbae
   DB_USERNAME=u123456_user
   DB_PASSWORD=password_db_anda
   ```
5. Simpan file `.env`.

---

## 📦 Tahap 4: Install Dependencies (Composer)

Karena folder `vendor` tidak di-push ke Git, Anda harus menginstalnya di server.

**Jika Hosting Memiliki Terminal:**
1. Buka menu **Terminal** di cPanel.
2. Masuk ke folder aplikasi Anda:
   ```bash
   cd skinbae_app
   ```
3. Jalankan perintah composer:
   ```bash
   composer install --optimize-autoloader --no-dev
   ```
4. Generate App Key:
   ```bash
   php artisan key:generate
   ```
5. (Opsional) Jalankan migrasi jika Anda tidak melakukan import SQL di Tahap 1:
   ```bash
   php artisan migrate --force
   ```

---

## 🌐 Tahap 5: Mengarahkan Domain ke Folder Public

Shared hosting biasanya membaca file website dari folder `public_html`, sedangkan Laravel menyimpan file publiknya di dalam folder `public`. Kita perlu "menyambungkan" keduanya.

1. Buka **File Manager** di cPanel.
2. Kosongkan semua isi folder `public_html` (hapus index.php bawaan hosting).
3. Masuk ke folder aplikasi Anda (`skinbae_app/public`).
4. **Pilih Semua (Select All)** file di dalam folder `public` tersebut (termasuk folder `build`, `assets`, `.htaccess`, dan `index.php`).
5. **Move (Pindahkan)** semua file tersebut ke folder `public_html`.
6. Buka file `index.php` yang sekarang berada di dalam `public_html` dan edit 2 baris berikut agar mengarah ke folder aplikasi yang benar:

   **Ubah baris 34:**
   ```php
   // Sebelum:
   require __DIR__.'/../vendor/autoload.php';
   
   // Sesudah (sesuaikan dengan nama folder aplikasi Anda):
   require __DIR__.'/../skinbae_app/vendor/autoload.php';
   ```

   **Ubah baris 47:**
   ```php
   // Sebelum:
   $app = require_once __DIR__.'/../bootstrap/app.php';
   
   // Sesudah:
   $app = require_once __DIR__.'/../skinbae_app/bootstrap/app.php';
   ```
7. Simpan file `index.php`. Sekarang website Anda sudah bisa diakses melalui nama domain!

---

## 🖼️ Tahap 6: Menampilkan Foto Produk (Storage Symlink)

Ini adalah masalah yang paling sering terjadi di *Shared Hosting*: Gambar produk yang diupload admin tidak muncul (error 404) karena folder `storage` tidak terhubung ke folder publik.

Karena kita memisahkan folder `public` (menjadi `public_html`) dengan folder utama Laravel (`skinbae_app`), perintah `php artisan storage:link` bawaan Laravel **TIDAK AKAN BERFUNGSI** dengan benar. Anda harus membuat *symlink* secara manual.

### Cara 1: Menggunakan Terminal cPanel (Direkomendasikan)
1. Buka menu **Terminal** di cPanel.
2. Ketikkan perintah ini (sesuaikan nama folder jika berbeda):
   ```bash
   ln -s /home/username_cpanel/skinbae_app/storage/app/public /home/username_cpanel/public_html/storage
   ```
   *(Ganti `username_cpanel` dengan username login cPanel Anda).*

### Cara 2: Menggunakan Script PHP (Jika tidak ada Terminal)
1. Buka **File Manager**, masuk ke folder `public_html`.
2. Buat file baru bernama `buat_link.php`.
3. Isi file tersebut dengan kode berikut:
   ```php
   <?php
   $targetFolder = $_SERVER['DOCUMENT_ROOT'].'/../skinbae_app/storage/app/public';
   $linkFolder = $_SERVER['DOCUMENT_ROOT'].'/storage';
   
   if (symlink($targetFolder, $linkFolder)) {
       echo 'Symlink berhasil dibuat! Gambar sekarang bisa diakses.';
   } else {
       echo 'Gagal membuat symlink.';
   }
   ?>
   ```
4. Buka file tersebut di browser Anda: `https://domain-anda.com/buat_link.php`.
5. Jika muncul pesan "Symlink berhasil", **hapus** file `buat_link.php` tersebut demi keamanan.

### Pindah Foto dari Lokal (Jika Ada)
Jika Anda sudah punya foto produk di laptop (lokal), lakukan ini:
1. Zip folder `storage/app/public/products` di komputer Anda.
2. Upload ke cPanel di path `skinbae_app/storage/app/public/`.
3. Extract zip tersebut. Gambar sekarang akan langsung muncul di website!

---

## 🔄 Tahap 7: Cara Update Website di Kemudian Hari

Jika sewaktu-waktu Anda merubah kode (misal: merubah warna tombol) di laptop Anda:
1. Di laptop, jalankan `npm run build`.
2. Lakukan `git add .`, `git commit -m "update"`, lalu `git push origin main`.
3. Login ke cPanel, buka **Git Version Control**.
4. Klik tombol **Update from Remote**. File PHP akan otomatis terupdate.
5. **Langkah Tambahan untuk Frontend:** Karena folder `public_html` sudah terpisah dari folder `skinbae_app/public`, file CSS/JS baru hasil `npm run build` yang ditarik oleh Git akan nyangkut di `skinbae_app/public/build`.
6. Anda harus menyalin folder `skinbae_app/public/build` dan menimpanya (*overwrite*) ke dalam folder `public_html/build` lewat File Manager agar perubahan tampilan terlihat oleh pengunjung.

---
**Selesai! Aplikasi Skinbae.ID Anda sekarang sudah *live* dan berjalan optimal di Shared Hosting.**
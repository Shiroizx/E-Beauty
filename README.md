# Skinbae.ID - Premium E-Commerce Platform

<p align="center">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
</p>

## About The Project

**Skinbae.ID** adalah platform e-commerce produk kecantikan premium yang dibangun menggunakan arsitektur modern **Laravel 10**, **Tailwind CSS**, dan **Alpine.js**. Proyek ini difokuskan pada memberikan pengalaman pengguna (UI/UX) terbaik, sistem transaksi yang aman, serta kemudahan bagi pengelola toko (Admin) dalam memproses pesanan pelanggan.

### ✨ Key Features

**👨‍💻 Customer Experience:**
*   **Modern UI/UX**: Desain antarmuka asimetris dan animasi halus berbasis Tailwind CSS & Alpine.js.
*   **Google Social Login**: Integrasi Laravel Socialite untuk login cepat menggunakan akun Google.
*   **Smart Checkout**: Sistem checkout multi-langkah (Wizard) dengan kalkulasi ongkos kirim real-time (API).
*   **Integrated Payment Gateway**: Pembayaran terintegrasi via DOKU (Virtual Account, QRIS, E-Wallet, Kartu Kredit).
*   **Real-time Live Tracking**: Pelacakan paket langsung menggunakan peta interaktif (Leaflet.js + OpenStreetMap).
*   **Order History**: Halaman riwayat pesanan interaktif dan informatif.

**⚙️ Admin Dashboard & Management:**
*   **Comprehensive Order Management**: Filter pesanan berdasarkan status (Menunggu, Diproses, Dikirim, Selesai).
*   **Label Printing**: Cetak label resi kurir dalam format A4 maupun Printer Thermal lengkap dengan QR Code otomatis.
*   **Excel/CSV Export**: Export data pesanan ke Excel dengan *UTF-8 BOM formatting* agar data langsung rapi di MS Excel.
*   **Product & Stock Management**: Fitur CRUD katalog, brand, tipe kulit, dan peringatan *Low Stock*.

### 🛠️ Tech Stack
*   **Backend Framework**: Laravel 10 (PHP 8.2+)
*   **Frontend Styling**: Tailwind CSS 3
*   **Frontend Reactivity**: Alpine.js
*   **Maps & Tracking**: Leaflet.js
*   **Payment Gateway**: DOKU Payment API
*   **Social Auth**: Laravel Socialite (Google OAuth)
*   **PDF Generation**: barryvdh/laravel-dompdf
*   **Database**: MySQL (Local/Production) & SQLite In-Memory (untuk Feature Testing)

---

## 🚀 Installation Guide

Ikuti langkah-langkah di bawah ini untuk menjalankan *Skinbae.ID* di lingkungan pengembangan lokal Anda.

### Prerequisites
*   [PHP](https://www.php.net/downloads.php) >= 8.1
*   [Composer](https://getcomposer.org/)
*   [Node.js & NPM](https://nodejs.org/en/)
*   [MySQL](https://www.mysql.com/)

### Steps

1.  **Clone the Repository**
    ```bash
    git clone https://github.com/Shiroizx/E-Beauty.git
    cd E-Beauty
    ```

2.  **Install PHP Dependencies**
    ```bash
    composer install
    ```

3.  **Install Node Dependencies**
    ```bash
    npm install
    ```

4.  **Environment Setup**
    *   Copy file `.env.example` menjadi `.env`
        ```bash
        cp .env.example .env
        ```
    *   Konfigurasikan database dan layanan pihak ketiga (DOKU & Google) di `.env`:
        ```env
        DB_CONNECTION=mysql
        DB_DATABASE=skinbae
        DB_USERNAME=root
        DB_PASSWORD=

        # DOKU Config
        DOKU_CLIENT_ID=your_client_id
        DOKU_SECRET_KEY=your_secret_key

        # Google Socialite Config
        GOOGLE_CLIENT_ID=your_google_client_id
        GOOGLE_CLIENT_SECRET=your_google_secret
        GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
        ```

5.  **Generate Application Key**
    ```bash
    php artisan key:generate
    ```

6.  **Database Migration & Seeding**
    *   Migrasi struktur database beserta data *dummy* awal:
        ```bash
        php artisan migrate --seed
        ```

7.  **Link Storage**
    *   Buat symbolic link untuk mengakses gambar produk:
        ```bash
        php artisan storage:link
        ```

8.  **Run the Application (Buka 2 Terminal)**
    *   **Terminal 1 (Backend):**
        ```bash
        php artisan serve
        ```
    *   **Terminal 2 (Frontend Build):**
        ```bash
        npm run dev
        ```
    Aplikasi dapat diakses melalui browser di: `http://localhost:8000`

### 🔑 Default Credentials

*   **Admin Account**:
    *   Email: `admin@ebeauty.com`
    *   Password: `password`

*   **Customer Account**:
    *   Email: `customer@example.com`
    *   Password: `password`

*(Anda juga dapat langsung masuk menggunakan akun Google Anda sendiri melalui tombol "Masuk dengan Google" di halaman Login).*

---
**Catatan untuk Developer:** Untuk panduan testing DOKU Webhook dan konfigurasi LocalTunnel/Ngrok, silakan merujuk ke file `DEVELOPMENT.md`.
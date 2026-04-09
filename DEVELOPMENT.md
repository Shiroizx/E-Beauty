# Panduan Development & Testing (Skinbae E-Beauty)

Dokumen ini berisi panduan *startup* untuk menjalankan *local environment*, khususnya integrasi **DOKU Payment Gateway (Sandbox)**, **Google Socialite Login**, dan **Testing Database**.

## 🚀 1. Menjalankan Server Lokal

Setiap kali Anda mulai mengoding, jalankan 3 terminal berikut di folder project:

**Terminal 1: Server Backend (Laravel)**
```bash
php artisan serve
```
*(Berjalan di port 8000 secara default).*

**Terminal 2: Server Frontend (Vite/Tailwind)**
```bash
npm run dev
```
*(Dibutuhkan agar styling Tailwind CSS dan Alpine.js dikompilasi secara real-time).*

**Terminal 3: Ngrok (Untuk Webhook DOKU & Google Login Callback)**
```bash
ngrok http 8000
```
*(Penting: DOKU dan Google membutuhkan URL HTTPS yang valid untuk mengirimkan notifikasi/callback. Salin URL Forwarding dari Ngrok, misalnya `https://1a2b3c4d.ngrok.app`).*

> **Catatan:** Jangan lupa untuk memperbarui variabel `APP_URL` dan `GOOGLE_REDIRECT_URI` di file `.env` dengan URL Ngrok terbaru Anda jika URL-nya berubah.

---

## 💳 2. Simulasi DOKU Payment (Sandbox)

Untuk mengetes fitur pembayaran (Checkout), pastikan Webhook DOKU telah diatur:
1. Login ke **DOKU Dashboard Sandbox**.
2. Masuk ke **Settings -> Payment Settings**.
3. Pilih metode pembayaran (Virtual Account/e-Wallet) -> **HTTP Notifications**.
4. Masukkan URL Ngrok Anda ditambah path notifikasi:
   `https://[url-ngrok-anda]/doku/notification`

### Alur Testing Checkout:
1. Buka aplikasi, tambahkan produk ke keranjang, lalu Checkout.
2. Pilih metode pembayaran **DOKU Payment**.
3. Saat diarahkan ke DOKU, pilih **Virtual Account** (BCA/Mandiri) atau **E-Wallet** (DANA/ShopeePay).
4. Buka **[DOKU Simulator](https://sandbox.doku.com/simulator)**.
5. Masukkan Nomor Virtual Account/Nomor HP ke simulator dan klik **Simulate/Bayar**.
6. Status pesanan di website Skinbae.ID akan otomatis berubah menjadi "Paid/Diproses" (webhook bekerja).

---

## 🔑 3. Google Socialite Login

Fitur login menggunakan akun Google telah diintegrasikan. Untuk mengetesnya:
1. Pastikan `GOOGLE_CLIENT_ID` dan `GOOGLE_CLIENT_SECRET` di `.env` sudah terisi dengan kredensial dari **Google Cloud Console**.
2. Pastikan `GOOGLE_REDIRECT_URI` di `.env` dan di Google Cloud Console sama persis (disarankan menggunakan domain Ngrok jika sedang *testing* atau `localhost:8000`).
3. Klik tombol **"Masuk dengan Google"** di halaman Login. Jika sukses, Anda akan langsung dialihkan ke beranda dalam kondisi *logged in*.

---

## 🧪 4. Testing (Feature Tests)

Project ini telah dikonfigurasi untuk menjalankan pengujian otomatis menggunakan **SQLite In-Memory** agar tidak menghapus database MySQL utama Anda secara tidak sengaja saat menggunakan *RefreshDatabase*.

1. Pastikan file `.env.testing` sudah tersedia dengan konfigurasi:
   ```env
   DB_CONNECTION=sqlite
   DB_DATABASE=:memory:
   ```
2. Jalankan perintah test:
   ```bash
   php artisan test
   ```
   *(Semua test, termasuk `TrackingTest`, akan dijalankan dengan aman di memori).*

---

## 🖨️ 5. Print Label & Export CSV

*   **Export CSV**: Di halaman Admin -> Pesanan, klik tombol "Export Excel/CSV". File yang diunduh sudah menggunakan format UTF-8 BOM dan pemisah titik koma (`;`) sehingga langsung rapi saat dibuka di Microsoft Excel.
*   **Print Label**: Tersedia dua opsi cetak resi pengiriman: **Kertas A4** (standar) dan **Thermal** (untuk printer resi). Fitur ini juga menyertakan *QR Code* nomor resi untuk discan oleh kurir.

# X BAR — Indonesian Premium Charcoal & Firewood Supplier Website

Aplikasi web profil perusahaan dan katalog ekspor produk arang & kayu bakar (X BAR) lengkap dengan panel administrasi dinamis, database MySQL, dan operasi CRUD penuh, didesain persis berdasarkan file desain `output.pdf`.

---

## 🚀 Panduan Menjalankan di Localhost Laragon

### 1. Ekstrak File Project
Ekstrak isi file `web-arang.zip` ke dalam folder web root Laragon:
```
C:\laragon\www\web-arang
```
Atau Anda bisa langsung menggunakan folder project yang sudah ada di Laragon `www`.

### 2. Nyalakan Laragon
1. Buka aplikasi **Laragon**.
2. Klik tombol **Start All** (untuk menyalakan Apache / Nginx dan MySQL).

### 3. Setup Database MySQL
Ada 2 cara mudah:

#### Cara A (Otomatis):
Buka website di browser:
```
http://localhost/web-arang
```
Sistem `config/database.php` sudah dilengkapi fitur **Auto-Create Database & Auto-Import Table**:
- Database `web_arang` akan dibuat otomatis.
- Skema tabel dan data seed dari `database.sql` akan diimpor secara otomatis saat pertama kali dibuka.

#### Cara B (Manual via phpMyAdmin / HeidiSQL):
1. Buka HeidiSQL (klik kanan di Laragon > MySQL > HeidiSQL) atau buka phpMyAdmin di `http://localhost/phpmyadmin`.
2. Buat database baru bernama `web_arang`.
3. Buka tab **Import / Execute SQL File**, lalu pilih file `database.sql` yang ada di root folder project.

---

## 🔑 Akun Login Administrator

Untuk mengakses Panel Admin:
- URL Login: `http://localhost/web-arang/admin/login.php`

| Parameter | Kredensial Default 1 | Kredensial Alternatif 2 |
| :--- | :--- | :--- |
| **Username** | `aidaa` | `admineve` |
| **Email** | `aidaa@gmail.com` | `eve@xbar.com` |
| **Password** | `aidaa123` | `aidaa123` |

---

## 📁 Struktur Halaman Sesuai Desain "output.pdf"

| Halaman Desain | File Implementasi | Deskripsi |
| :--- | :--- | :--- |
| **Page 1** | `index.php` | Landing Page: Hero section, About Us, History Timeline, 5 Alasan Memilih Kami, Visi & Misi, Ringkasan Produk, Galeri, Lokasi Peta Bondowoso, Footer. |
| **Page 2** | `products.php` | Katalog Spesifikasi Produk: Desain selang-seling (Z-pattern), filter pencarian produk, detail stok, harga terbaru, keunggulan, tombol pemesanan interaktif (WhatsApp / Form Quotation). |
| **Page 3** | `admin/login.php` | Form Login Admin dengan efek Glassmorphism, input icon, show/hide password toggle (Sign Up ditiadakan, akses admin langsung via Login). |
| **Page 5** | `admin/products.php` | Manajemen Produk Admin: Pencarian instan (live search), counter produk total, aksi **Edit** & **Delete** per item. |
| **Page 6** | `admin/index.php` | Dashboard Admin Utama: Sidebar avatar, analitik pengunjung (4.3K), log aktivitas terkini, grafik tren penjualan bulanan (Bar Chart Jan–Jun), status kesehatan inventaris (Health Score 100%), dan pertumbuhan pelanggan (+15%). |
| **Page 7** | `admin/product_edit.php` | Form Edit Produk: Ubah foto produk, nama, stok, harga terbaru, bahan baku, dan deskripsi teknis. |
| **Page 8 & 11** | `admin/profile.php` | Profil Admin: Avatar lingkaran besar, upload foto baru, ganti nama, email, dan ganti password dengan fitur toggle lihat password (mata). |
| **Page 9** | `admin/product_add.php` | Form Tambah Produk Baru: Upload foto drag & drop placeholder, nama produk, stok, harga terbaru, bahan baku, dan deskripsi. |
| **Page 10** | `index.php` (Interaksi) | Modal Form "Request Quotation": Formulir penawaran harga ekspor untuk pembeli internasional/lokal yang langsung tersimpan ke tabel `quotations` & `activity_logs`. |
| **Page 12** | `admin/website.php` | Manajemen Konten Website: Edit Header + **Live Preview Card**, Edit Tentang Kami, Edit Sejarah, Edit Visi & Misi, dan CRUD Galeri Foto. |
| **Page 13** | `index.php` (Floating Widget) | AI / Live Chatbot Assistant X BAR: Widget obrolan interaktif yang merespons pertanyaan pembeli (harga, ekspor, spesifikasi, lokasi). |

---

## 🛠️ Fitur Tambahan & Keunggulan
- **Bilingual Switcher**: Toggle bahasa Indonesia / English langsung di header publik.
- **WhatsApp Direct Order**: Integrasi tombol "BUY NOW" yang langsung mengarahkan pesan pesanan format rapi ke WhatsApp.
- **Live Search**: Pencarian produk secara instan di sisi pengguna publik maupun di dashboard admin.
- **Responsive Layout**: Menyesuaikan tampilan desktop, tablet, maupun mobile smartphone.
- **Security & Validation**: Prepared statement PDO untuk mencegah SQL Injection, session auth check middleware, password hashing bcrypt, sanitasi XSS (`htmlspecialchars`).

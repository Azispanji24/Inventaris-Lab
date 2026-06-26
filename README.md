# 📦 Inventaris Lab

Sistem Informasi Inventaris Laboratorium berbasis web yang digunakan untuk mengelola data inventaris, kategori barang, peminjaman, serta laporan inventaris secara terstruktur.

Proyek ini dikembangkan untuk mempermudah proses pendataan dan pengelolaan inventaris laboratorium agar lebih efisien dibandingkan pencatatan manual.

---

## ✨ Features

### Dashboard

* Total barang
* Total kategori
* Total peminjaman
* Statistik inventaris

### Inventory Management

* Menambahkan barang
* Mengedit barang
* Menghapus barang
* Detail informasi inventaris

### Category Management

* Menambahkan kategori
* Mengedit kategori
* Menghapus kategori

### Borrowing System

* Peminjaman barang
* Pengembalian barang
* Status peminjaman

### Reports

* Laporan inventaris
* Histori peminjaman

### Authentication

* Login admin
* Logout
* Session management

---

## 🛠 Technology Stack

### Frontend

* HTML5
* CSS3
* Bootstrap
* JavaScript

### Backend

* PHP Native

### Database

* MySQL

### Development Environment

* Laragon

---

## 📋 Requirements

Sebelum menjalankan project, pastikan perangkat memiliki:

* Laragon
* Apache atau Nginx aktif
* MySQL aktif
* Browser modern

---

## ⚙ Setup Project

### 1. Clone repository

```bash
git clone https://github.com/Azispanji24/Inventaris-Lab.git
```

### 2. Pindahkan project

Simpan project pada folder:

```bash
C:\laragon\www\inventaris-lab
```

### 3. Jalankan Laragon

Start:

* Apache/Nginx
* MySQL

### 4. Import database

Buka:

* HeidiSQL
* phpMyAdmin

Import file:

```bash
database.sql
```

### 5. Konfigurasi environment (opsional)

Jika menggunakan konfigurasi database custom:

Salin:

```bash
.env.example
```

menjadi:

```bash
.env
```

Lalu ubah:

```env
DB_HOST=
DB_USERNAME=
DB_PASSWORD=
DB_DATABASE=
```

---

## 🚀 Run Application

Akses project melalui browser:

Jika Laragon Auto Virtual Host aktif:

```bash
http://inventaris-lab.test
```

Atau:

```bash
http://localhost/inventaris-lab
```

---

## 🔐 Default Login

Username:

```text
admin
```

Password:

```text
admin123
```

---

## 🗄 Database

Database default:

```text
db_inventaris_lab
```

---

## 📝 Notes

* File `koneksi.php` membaca konfigurasi database dari `.env` jika tersedia
* Pastikan MySQL berjalan sebelum membuka aplikasi
* Pastikan database sudah berhasil di-import
* Jika terjadi error koneksi, periksa konfigurasi database

---

## 👨‍💻 Author

Azis Panji Gumilang
Informatics Engineering — UIN Bandung

# Inventaris Lab

## Menjalankan di Laragon

1. Pindahkan folder project ini ke `C:\laragon\www\inventaris-lab`.
2. Jalankan Laragon, lalu start Apache/Nginx dan MySQL.
3. Buka database manager Laragon, buat/import database dari `database.sql`.
4. Jika pakai kredensial database selain default Laragon, salin `.env.example` menjadi `.env`, lalu sesuaikan nilainya.
5. Buka salah satu URL berikut:
   - `http://inventaris-lab.test` jika auto virtual host Laragon aktif.
   - `http://localhost/inventaris-lab` jika memakai document root biasa.

Login awal:

- Username: `admin`
- Password: `admin123`

-- Membuat database
CREATE DATABASE IF NOT EXISTS db_inventaris_lab;
USE db_inventaris_lab;

-- Tabel users
CREATE TABLE users (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel kategori
CREATE TABLE kategori (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    nama_kategori VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel barang
CREATE TABLE barang (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    kode_barang VARCHAR(50) NOT NULL UNIQUE,
    nama_barang VARCHAR(100) NOT NULL,
    kategori_id INT(11),
    jumlah INT(11) DEFAULT 0,
    kondisi ENUM('Baik', 'Rusak Ringan', 'Rusak Berat') DEFAULT 'Baik',
    lokasi VARCHAR(100),
    tanggal_masuk DATE,
    status ENUM('Tersedia', 'Dipinjam', 'Rusak') DEFAULT 'Tersedia',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE SET NULL
);

-- Tabel peminjaman
CREATE TABLE peminjaman (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    nama_peminjam VARCHAR(100) NOT NULL,
    nim VARCHAR(20) NOT NULL,
    barang_id INT(11),
    tanggal_pinjam DATE NOT NULL,
    tanggal_kembali DATE,
    status ENUM('Dipinjam', 'Dikembalikan', 'Terlambat') DEFAULT 'Dipinjam',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (barang_id) REFERENCES barang(id) ON DELETE SET NULL
);

-- Insert data kategori
INSERT INTO kategori (nama_kategori) VALUES
('Komputer'),
('Perangkat Jaringan'),
('Perangkat Multimedia'),
('Perangkat Laboratorium'),
('Perangkat Kantor'),
('Perangkat Keamanan'),
('Perangkat Penyimpanan'),
('Perangkat Konektivitas'),
('Perangkat Pengujian'),
('Perangkat Aksesoris');

-- Insert data users (password: admin123)
INSERT INTO users (username, password, nama_lengkap, role) VALUES
('admin', '$2y$10$wAdCiqpUs5G5XVyZjQFgGuIIXdhnvHzQRkTK3syTl3LWOFCsDJf2K', 'Administrator', 'admin');

-- Insert data barang
INSERT INTO barang (kode_barang, nama_barang, kategori_id, jumlah, kondisi, lokasi, tanggal_masuk, status) VALUES
('BRG001', 'Laptop Dell Inspiron', 1, 5, 'Baik', 'Lab Komputer 1', '2026-01-15', 'Tersedia'),
('BRG002', 'Projector Epson', 3, 3, 'Baik', 'Ruang Audio Visual', '2026-01-20', 'Tersedia'),
('BRG003', 'Switch Cisco 24 Port', 2, 4, 'Baik', 'Ruang Server', '2026-01-10', 'Tersedia'),
('BRG004', 'PC Desktop Lenovo', 1, 10, 'Baik', 'Lab Komputer 2', '2026-01-05', 'Tersedia'),
('BRG005', 'Printer HP LaserJet', 5, 2, 'Rusak Ringan', 'Ruang Administrasi', '2026-01-25', 'Rusak'),
('BRG006', 'Router MikroTik', 2, 3, 'Baik', 'Ruang Jaringan', '2026-01-18', 'Dipinjam'),
('BRG007', 'Monitor Samsung 24"', 1, 8, 'Baik', 'Lab Komputer 1', '2026-01-12', 'Tersedia'),
('BRG008', 'Kamera DSLR Canon', 3, 2, 'Baik', 'Ruang Media', '2026-01-22', 'Tersedia'),
('BRG009', 'Harddisk Eksternal 1TB', 7, 6, 'Baik', 'Ruang Penyimpanan', '2026-01-08', 'Tersedia'),
('BRG010', 'Keyboard Wireless Logitech', 10, 4, 'Baik', 'Lab Komputer 2', '2026-01-28', 'Tersedia');

-- Insert data peminjaman
INSERT INTO peminjaman (nama_peminjam, nim, barang_id, tanggal_pinjam, tanggal_kembali, status) VALUES
('Ahmad Fauzi', '202101001', 1, '2026-02-01', '2026-02-08', 'Dikembalikan'),
('Siti Rahmah', '202101002', 3, '2026-02-02', '2026-02-09', 'Dikembalikan'),
('Budi Santoso', '202101003', 6, '2026-02-03', '2026-02-10', 'Dipinjam'),
('Dewi Lestari', '202101004', 2, '2026-02-04', '2026-02-11', 'Dipinjam'),
('Rudi Hermawan', '202101005', 4, '2026-02-05', '2026-02-12', 'Terlambat');

<?php
// Load konfigurasi lokal dari .env jika file tersedia.
// Default di bawah cocok untuk Laragon/XAMPP: host localhost, user root, password kosong.
$envFile = __DIR__ . '/.env';
if (is_readable($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);

        if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
            continue;
        }

        [$key, $value] = array_map('trim', explode('=', $line, 2));
        $value = trim($value, "\"'");

        if (getenv($key) === false) {
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
        }
    }
}

// Konfigurasi database
$host = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USERNAME') ?: 'root';
$password = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '';
$database = getenv('DB_DATABASE') ?: 'db_inventaris_lab';

// Membuat koneksi
$koneksi = mysqli_connect($host, $username, $password, $database);

// Cek koneksi
if (!$koneksi) {
    die("Koneksi database gagal. Pastikan MySQL Laragon aktif dan database '$database' sudah di-import. Detail: " . mysqli_connect_error());
}

// Set charset ke UTF-8
mysqli_set_charset($koneksi, "utf8");

// Fungsi untuk menjalankan query
function query($sql) {
    global $koneksi;
    return mysqli_query($koneksi, $sql);
}

// Fungsi untuk mengambil satu data
function fetch_assoc($result) {
    return mysqli_fetch_assoc($result);
}

// Fungsi untuk mengambil semua data
function fetch_all($result) {
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Fungsi untuk menghitung jumlah data
function count_data($result) {
    return mysqli_num_rows($result);
}
?>

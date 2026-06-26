<?php
session_start();

// Cek login
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit;
}

require_once 'koneksi.php';

$id = (int)$_GET['id'];

// Cek apakah barang ada
$sql_cek = "SELECT * FROM barang WHERE id = $id";
$result_cek = query($sql_cek);
if (count_data($result_cek) > 0) {
    // Hapus data
    $sql = "DELETE FROM barang WHERE id = $id";
    if (query($sql)) {
        $_SESSION['message'] = 'Data barang berhasil dihapus!';
    } else {
        $_SESSION['message'] = 'Gagal menghapus data!';
    }
}

header("Location: barang.php");
exit;
?>
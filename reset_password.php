<?php
require_once 'koneksi.php';

// Password baru
$password = 'admin123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Update password admin
$sql = "UPDATE users SET password = '$hashed_password' WHERE username = 'admin'";

if (query($sql)) {
    echo "Password berhasil direset!<br>";
    echo "Username: admin<br>";
    echo "Password: admin123<br>";
    echo "<a href='login.php'>Klik di sini untuk login</a>";
} else {
    echo "Gagal reset password: " . mysqli_error($koneksi);
}
?>

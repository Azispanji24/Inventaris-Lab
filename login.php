<?php
session_start();

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    header("Location: dashboard.php");
    exit;
}

require_once 'koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = 'Username dan Password harus diisi!';
    } else {
        // Query untuk mencari user
        $sql = "SELECT * FROM users WHERE username = '$username'";
        $result = query($sql);
        
        if (count_data($result) == 1) {
            $row = fetch_assoc($result);
            
            // Verifikasi password
            if (password_verify($password, $row['password'])) {
                // Set session
                $_SESSION['login'] = true;
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['nama_lengkap'] = $row['nama_lengkap'];
                $_SESSION['role'] = $row['role'];
                
                header("Location: dashboard.php");
                exit;
            } else {
                $error = 'Password salah!';
            }
        } else {
            $error = 'Username tidak ditemukan!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Informasi Inventaris Lab</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="login-page">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="bi bi-box-seam" style="font-size: 48px; color: #0d6efd;"></i>
                            <h3 class="mt-3 fw-bold">Inventaris Lab</h3>
                            <p class="text-muted">Sistem Informasi Inventaris</p>
                        </div>
                        
                        <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label fw-semibold">
                                    <i class="bi bi-person"></i> Username
                                </label>
                                <input type="text" class="form-control form-control-lg" id="username" 
                                       name="username" placeholder="Masukkan username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">
                                    <i class="bi bi-lock"></i> Password
                                </label>
                                <input type="password" class="form-control form-control-lg" id="password" 
                                       name="password" placeholder="Masukkan password" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </button>
                            <div class="text-center">
                                <small class="text-muted">Demo: admin / admin123</small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
session_start();

// Cek login
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit;
}

require_once 'koneksi.php';

$id = (int)$_GET['id'];

// Ambil data barang
$sql_barang = "SELECT * FROM barang WHERE id = $id";
$result_barang = query($sql_barang);
if (count_data($result_barang) == 0) {
    header("Location: barang.php");
    exit;
}
$barang = fetch_assoc($result_barang);

// Ambil data kategori
$sql_kategori = "SELECT * FROM kategori ORDER BY nama_kategori";
$result_kategori = query($sql_kategori);
$kategori = fetch_all($result_kategori);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode_barang = mysqli_real_escape_string($koneksi, $_POST['kode_barang']);
    $nama_barang = mysqli_real_escape_string($koneksi, $_POST['nama_barang']);
    $kategori_id = (int)$_POST['kategori_id'];
    $jumlah = (int)$_POST['jumlah'];
    $kondisi = mysqli_real_escape_string($koneksi, $_POST['kondisi']);
    $lokasi = mysqli_real_escape_string($koneksi, $_POST['lokasi']);
    $tanggal_masuk = mysqli_real_escape_string($koneksi, $_POST['tanggal_masuk']);
    $status = mysqli_real_escape_string($koneksi, $_POST['status']);

    // Validasi
    if (empty($kode_barang) || empty($nama_barang) || empty($kategori_id) || empty($lokasi)) {
        $error = 'Semua field wajib diisi!';
    } else {
        // Cek kode barang duplikat (abaikan data sendiri)
        $sql_cek = "SELECT * FROM barang WHERE kode_barang = '$kode_barang' AND id != $id";
        $result_cek = query($sql_cek);
        if (count_data($result_cek) > 0) {
            $error = 'Kode barang sudah digunakan!';
        } else {
            $sql = "UPDATE barang SET 
                    kode_barang = '$kode_barang',
                    nama_barang = '$nama_barang',
                    kategori_id = $kategori_id,
                    jumlah = $jumlah,
                    kondisi = '$kondisi',
                    lokasi = '$lokasi',
                    tanggal_masuk = '$tanggal_masuk',
                    status = '$status'
                    WHERE id = $id";
            
            if (query($sql)) {
                $success = 'Data barang berhasil diupdate!';
                echo "<script>setTimeout(function(){ window.location.href='barang.php'; }, 2000);</script>";
            } else {
                $error = 'Gagal mengupdate data: ' . mysqli_error($koneksi);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Barang - Sistem Informasi Inventaris Lab</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="d-flex">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <?php include 'includes/navbar.php'; ?>
            
            <div class="content-wrapper">
                <div class="container-fluid">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0 fw-bold">
                            <i class="bi bi-pencil-square me-2"></i>Edit Barang
                        </h4>
                        <a href="barang.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>

                    <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <div class="card shadow-sm">
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Kode Barang <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="kode_barang" 
                                               value="<?php echo htmlspecialchars($barang['kode_barang']); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Nama Barang <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="nama_barang" 
                                               value="<?php echo htmlspecialchars($barang['nama_barang']); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                                        <select class="form-select" name="kategori_id" required>
                                            <option value="">-- Pilih Kategori --</option>
                                            <?php foreach ($kategori as $k): ?>
                                            <option value="<?php echo $k['id']; ?>" 
                                                <?php echo $k['id'] == $barang['kategori_id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($k['nama_kategori']); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Jumlah</label>
                                        <input type="number" class="form-control" name="jumlah" 
                                               value="<?php echo $barang['jumlah']; ?>" min="0">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Kondisi <span class="text-danger">*</span></label>
                                        <select class="form-select" name="kondisi" required>
                                            <option value="Baik" <?php echo $barang['kondisi'] == 'Baik' ? 'selected' : ''; ?>>Baik</option>
                                            <option value="Rusak Ringan" <?php echo $barang['kondisi'] == 'Rusak Ringan' ? 'selected' : ''; ?>>Rusak Ringan</option>
                                            <option value="Rusak Berat" <?php echo $barang['kondisi'] == 'Rusak Berat' ? 'selected' : ''; ?>>Rusak Berat</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Lokasi <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="lokasi" 
                                               value="<?php echo htmlspecialchars($barang['lokasi']); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Tanggal Masuk</label>
                                        <input type="date" class="form-control" name="tanggal_masuk" 
                                               value="<?php echo $barang['tanggal_masuk']; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Status</label>
                                        <select class="form-select" name="status">
                                            <option value="Tersedia" <?php echo $barang['status'] == 'Tersedia' ? 'selected' : ''; ?>>Tersedia</option>
                                            <option value="Dipinjam" <?php echo $barang['status'] == 'Dipinjam' ? 'selected' : ''; ?>>Dipinjam</option>
                                            <option value="Rusak" <?php echo $barang['status'] == 'Rusak' ? 'selected' : ''; ?>>Rusak</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <hr>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-save"></i> Update
                                        </button>
                                        <a href="barang.php" class="btn btn-secondary">Batal</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php include 'includes/footer.php'; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>
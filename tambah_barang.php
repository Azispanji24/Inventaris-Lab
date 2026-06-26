<?php
session_start();

// Cek login
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit;
}

require_once 'koneksi.php';

// Ambil data kategori untuk dropdown
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
        // Cek kode barang duplikat
        $sql_cek = "SELECT * FROM barang WHERE kode_barang = '$kode_barang'";
        $result_cek = query($sql_cek);
        if (count_data($result_cek) > 0) {
            $error = 'Kode barang sudah digunakan!';
        } else {
            $sql = "INSERT INTO barang (kode_barang, nama_barang, kategori_id, jumlah, kondisi, lokasi, tanggal_masuk, status) 
                    VALUES ('$kode_barang', '$nama_barang', $kategori_id, $jumlah, '$kondisi', '$lokasi', '$tanggal_masuk', '$status')";
            
            if (query($sql)) {
                $success = 'Data barang berhasil ditambahkan!';
                echo "<script>setTimeout(function(){ window.location.href='barang.php'; }, 2000);</script>";
            } else {
                $error = 'Gagal menambahkan data: ' . mysqli_error($koneksi);
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
    <title>Tambah Barang - Sistem Informasi Inventaris Lab</title>
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
                            <i class="bi bi-plus-circle me-2"></i>Tambah Barang
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
                                               placeholder="Contoh: BRG001" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Nama Barang <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="nama_barang" 
                                               placeholder="Masukkan nama barang" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                                        <select class="form-select" name="kategori_id" required>
                                            <option value="">-- Pilih Kategori --</option>
                                            <?php foreach ($kategori as $k): ?>
                                            <option value="<?php echo $k['id']; ?>">
                                                <?php echo htmlspecialchars($k['nama_kategori']); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Jumlah</label>
                                        <input type="number" class="form-control" name="jumlah" 
                                               value="1" min="0">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Kondisi <span class="text-danger">*</span></label>
                                        <select class="form-select" name="kondisi" required>
                                            <option value="Baik">Baik</option>
                                            <option value="Rusak Ringan">Rusak Ringan</option>
                                            <option value="Rusak Berat">Rusak Berat</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Lokasi <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="lokasi" 
                                               placeholder="Contoh: Lab Komputer 1" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Tanggal Masuk</label>
                                        <input type="date" class="form-control" name="tanggal_masuk" 
                                               value="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Status</label>
                                        <select class="form-select" name="status">
                                            <option value="Tersedia">Tersedia</option>
                                            <option value="Dipinjam">Dipinjam</option>
                                            <option value="Rusak">Rusak</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <hr>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-save"></i> Simpan
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
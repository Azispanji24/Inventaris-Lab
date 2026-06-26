<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit;
}

require_once 'koneksi.php';

$barang_tersedia = fetch_all(query("SELECT id, nama_barang, status FROM barang WHERE status = 'Tersedia' ORDER BY nama_barang"));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    if ($action == 'add') {
        $nama_peminjam = mysqli_real_escape_string($koneksi, $_POST['nama_peminjam']);
        $nim = mysqli_real_escape_string($koneksi, $_POST['nim']);
        $barang_id = (int)$_POST['barang_id'];
        $tanggal_pinjam = mysqli_real_escape_string($koneksi, $_POST['tanggal_pinjam']);
        $tanggal_kembali = mysqli_real_escape_string($koneksi, $_POST['tanggal_kembali']);

        $sql = "INSERT INTO peminjaman (nama_peminjam, nim, barang_id, tanggal_pinjam, tanggal_kembali, status)
                VALUES ('$nama_peminjam', '$nim', $barang_id, '$tanggal_pinjam', '$tanggal_kembali', 'Dipinjam')";

        if (query($sql)) {
            query("UPDATE barang SET status = 'Dipinjam' WHERE id = $barang_id");
            $success = 'Data peminjaman berhasil ditambahkan!';
        } else {
            $error = 'Gagal menambahkan data: ' . mysqli_error($koneksi);
        }
    } elseif ($action == 'edit') {
        $id = (int)$_POST['id'];
        $nama_peminjam = mysqli_real_escape_string($koneksi, $_POST['nama_peminjam']);
        $nim = mysqli_real_escape_string($koneksi, $_POST['nim']);
        $barang_id = (int)$_POST['barang_id'];
        $tanggal_pinjam = mysqli_real_escape_string($koneksi, $_POST['tanggal_pinjam']);
        $tanggal_kembali = mysqli_real_escape_string($koneksi, $_POST['tanggal_kembali']);
        $status = mysqli_real_escape_string($koneksi, $_POST['status']);

        $sql = "UPDATE peminjaman SET
                nama_peminjam = '$nama_peminjam',
                nim = '$nim',
                barang_id = $barang_id,
                tanggal_pinjam = '$tanggal_pinjam',
                tanggal_kembali = '$tanggal_kembali',
                status = '$status'
                WHERE id = $id";

        if (query($sql)) {
            if ($status == 'Dikembalikan') {
                query("UPDATE barang SET status = 'Tersedia' WHERE id = $barang_id");
            }
            $success = 'Data peminjaman berhasil diupdate!';
        } else {
            $error = 'Gagal mengupdate data: ' . mysqli_error($koneksi);
        }
    } elseif ($action == 'delete') {
        $id = (int)$_POST['id'];
        $barang_id = (int)$_POST['barang_id'];

        if (query("DELETE FROM peminjaman WHERE id = $id")) {
            query("UPDATE barang SET status = 'Tersedia' WHERE id = $barang_id");
            $success = 'Data peminjaman berhasil dihapus!';
        } else {
            $error = 'Gagal menghapus data!';
        }
    }
}

$peminjaman = fetch_all(query("SELECT p.*, b.nama_barang, b.kode_barang FROM peminjaman p
    LEFT JOIN barang b ON p.barang_id = b.id
    ORDER BY p.created_at DESC"));

$total_peminjaman = count($peminjaman);
$aktif = 0;
$selesai = 0;
$terlambat = 0;
foreach ($peminjaman as $row) {
    if ($row['status'] == 'Dipinjam') {
        $aktif++;
    } elseif ($row['status'] == 'Dikembalikan') {
        $selesai++;
    } elseif ($row['status'] == 'Terlambat') {
        $terlambat++;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman - LabInventory</title>
    <?php include 'includes/head_assets.php'; ?>
</head>
<body>
    <div class="app-shell">
        <?php include 'includes/sidebar.php'; ?>

        <main class="main-content">
            <?php include 'includes/navbar.php'; ?>

            <div class="content-wrapper">
                <div class="container-fluid">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                        <div>
                            <h1 class="page-title">Peminjaman</h1>
                            <p class="page-subtitle">Pantau transaksi peminjaman aset laboratorium dan status pengembaliannya.</p>
                        </div>
                        <button type="button" class="btn btn-primary px-4" data-bs-toggle="modal" data-bs-target="#modalPeminjaman">
                            <i class="bi bi-plus-lg me-2"></i>Tambah Peminjaman
                        </button>
                    </div>

                    <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <?php if (isset($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle-fill me-2"></i><?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6 col-xl-3">
                            <div class="stat-card">
                                <div class="stat-label mt-0">Total Transaksi</div>
                                <div class="stat-value fs-3"><?php echo number_format($total_peminjaman, 0, ',', '.'); ?></div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <div class="stat-card">
                                <div class="stat-label mt-0">Sedang Dipinjam</div>
                                <div class="stat-value fs-3"><?php echo number_format($aktif, 0, ',', '.'); ?></div>
                                <span class="mini-pill mt-2">Aktif</span>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <div class="stat-card">
                                <div class="stat-label mt-0">Dikembalikan</div>
                                <div class="stat-value fs-3 text-success"><?php echo number_format($selesai, 0, ',', '.'); ?></div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <div class="stat-card">
                                <div class="stat-label mt-0">Terlambat</div>
                                <div class="stat-value fs-3 text-danger"><?php echo number_format($terlambat, 0, ',', '.'); ?></div>
                                <span class="mini-pill red mt-2">Perlu Follow-up</span>
                            </div>
                        </div>
                    </div>

                    <section class="toolbar-card mb-4">
                        <div class="row g-3 align-items-center">
                            <div class="col-lg">
                                <div class="toolbar-input">
                                    <i class="bi bi-search"></i>
                                    <input type="search" class="form-control" placeholder="Cari peminjam, NIM, atau nama barang...">
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-auto">
                                <select class="form-select h-100">
                                    <option>Semua Status</option>
                                    <option>Dipinjam</option>
                                    <option>Dikembalikan</option>
                                    <option>Terlambat</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-light-soft px-3" type="button"><i class="bi bi-sliders"></i></button>
                            </div>
                        </div>
                    </section>

                    <section class="table-card">
                        <div class="table-card-header">
                            <h2 class="panel-title">Riwayat Peminjaman</h2>
                            <button class="btn btn-light-soft btn-sm" type="button"><i class="bi bi-download me-1"></i> Export</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Peminjam</th>
                                        <th>Barang</th>
                                        <th>Tanggal Pinjam</th>
                                        <th>Tanggal Kembali</th>
                                        <th>Status</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($peminjaman)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-5">Tidak ada data peminjaman</td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($peminjaman as $p): ?>
                                    <?php
                                    $statusClass = $p['status'] == 'Dipinjam' ? 'borrowed' : ($p['status'] == 'Dikembalikan' ? 'available' : 'damaged');
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="item-cell">
                                                <div class="avatar"><?php echo strtoupper(substr($p['nama_peminjam'], 0, 1)); ?></div>
                                                <div>
                                                    <span class="item-name"><?php echo htmlspecialchars($p['nama_peminjam']); ?></span>
                                                    <span class="item-meta">NIM: <?php echo htmlspecialchars($p['nim']); ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="item-name"><?php echo htmlspecialchars($p['nama_barang'] ?? '-'); ?></span>
                                            <span class="item-meta">Kode: <?php echo htmlspecialchars($p['kode_barang'] ?? '-'); ?></span>
                                        </td>
                                        <td><?php echo date('d M Y', strtotime($p['tanggal_pinjam'])); ?></td>
                                        <td><?php echo $p['tanggal_kembali'] ? date('d M Y', strtotime($p['tanggal_kembali'])) : '-'; ?></td>
                                        <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($p['status']); ?></span></td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="icon-button" type="button" data-bs-toggle="dropdown" aria-label="Aksi">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <button type="button" class="dropdown-item btn-edit"
                                                                data-id="<?php echo $p['id']; ?>"
                                                                data-nama="<?php echo htmlspecialchars($p['nama_peminjam']); ?>"
                                                                data-nim="<?php echo htmlspecialchars($p['nim']); ?>"
                                                                data-barang="<?php echo $p['barang_id']; ?>"
                                                                data-tgl_pinjam="<?php echo $p['tanggal_pinjam']; ?>"
                                                                data-tgl_kembali="<?php echo $p['tanggal_kembali']; ?>"
                                                                data-status="<?php echo $p['status']; ?>"
                                                                data-bs-toggle="modal" data-bs-target="#modalPeminjaman">
                                                            <i class="bi bi-pencil me-2"></i>Edit
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <form method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                                            <input type="hidden" name="action" value="delete">
                                                            <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                                            <input type="hidden" name="barang_id" value="<?php echo $p['barang_id']; ?>">
                                                            <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>Hapus</button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="table-card-footer">
                            <span>Menampilkan <?php echo number_format($total_peminjaman, 0, ',', '.'); ?> transaksi peminjaman</span>
                            <span>Data terbaru otomatis berada di urutan atas</span>
                        </div>
                    </section>
                </div>
            </div>

            <?php include 'includes/footer.php'; ?>
        </main>
    </div>

    <div class="modal fade" id="modalPeminjaman" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Tambah Peminjaman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <input type="hidden" name="action" id="action" value="add">
                        <input type="hidden" name="id" id="edit_id">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Peminjam</label>
                            <input type="text" class="form-control" name="nama_peminjam" id="nama_peminjam" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">NIM</label>
                            <input type="text" class="form-control" name="nim" id="nim" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Barang</label>
                            <select class="form-select" name="barang_id" id="barang_id" required>
                                <option value="">-- Pilih Barang --</option>
                                <?php foreach ($barang_tersedia as $b): ?>
                                <option value="<?php echo $b['id']; ?>"><?php echo htmlspecialchars($b['nama_barang']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Pinjam</label>
                            <input type="date" class="form-control" name="tanggal_pinjam" id="tanggal_pinjam" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Kembali</label>
                            <input type="date" class="form-control" name="tanggal_kembali" id="tanggal_kembali">
                        </div>
                        <div class="mb-3" id="status_group" style="display:none;">
                            <label class="form-label fw-semibold">Status</label>
                            <select class="form-select" name="status" id="status">
                                <option value="Dipinjam">Dipinjam</option>
                                <option value="Dikembalikan">Dikembalikan</option>
                                <option value="Terlambat">Terlambat</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-soft" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.btn-edit').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('action').value = 'edit';
                document.getElementById('edit_id').value = this.dataset.id;
                document.getElementById('nama_peminjam').value = this.dataset.nama;
                document.getElementById('nim').value = this.dataset.nim;
                document.getElementById('barang_id').value = this.dataset.barang;
                document.getElementById('tanggal_pinjam').value = this.dataset.tgl_pinjam;
                document.getElementById('tanggal_kembali').value = this.dataset.tgl_kembali;
                document.getElementById('status').value = this.dataset.status;
                document.querySelector('#modalPeminjaman .modal-title').innerHTML = '<i class="bi bi-pencil-square me-2"></i>Edit Peminjaman';
                document.querySelector('#status_group').style.display = 'block';
            });
        });

        document.querySelector('#modalPeminjaman').addEventListener('show.bs.modal', function(e) {
            if (!e.relatedTarget || !e.relatedTarget.classList.contains('btn-edit')) {
                document.getElementById('action').value = 'add';
                document.getElementById('edit_id').value = '';
                document.getElementById('nama_peminjam').value = '';
                document.getElementById('nim').value = '';
                document.getElementById('barang_id').value = '';
                document.getElementById('tanggal_pinjam').value = '<?php echo date("Y-m-d"); ?>';
                document.getElementById('tanggal_kembali').value = '';
                document.getElementById('status').value = 'Dipinjam';
                document.querySelector('#modalPeminjaman .modal-title').innerHTML = '<i class="bi bi-plus-circle me-2"></i>Tambah Peminjaman';
                document.querySelector('#status_group').style.display = 'none';
            }
        });
    </script>
    <script src="assets/js/script.js"></script>
</body>
</html>

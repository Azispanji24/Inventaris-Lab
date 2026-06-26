<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit;
}

require_once 'koneksi.php';

$limit = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';

$where = "WHERE b.nama_barang LIKE '%$search%' OR b.kode_barang LIKE '%$search%' OR b.lokasi LIKE '%$search%'";

$total_data = fetch_assoc(query("SELECT COUNT(*) as total FROM barang b LEFT JOIN kategori k ON b.kategori_id = k.id $where"))['total'];
$total_pages = max(1, ceil($total_data / $limit));

$barang = fetch_all(query("SELECT b.*, k.nama_kategori FROM barang b
    LEFT JOIN kategori k ON b.kategori_id = k.id
    $where
    ORDER BY b.created_at DESC
    LIMIT $offset, $limit"));

$total_barang = fetch_assoc(query("SELECT COUNT(*) as total FROM barang"))['total'];
$tersedia = fetch_assoc(query("SELECT COUNT(*) as total FROM barang WHERE status = 'Tersedia'"))['total'];
$dipinjam = fetch_assoc(query("SELECT COUNT(*) as total FROM barang WHERE status = 'Dipinjam'"))['total'];
$rusak = fetch_assoc(query("SELECT COUNT(*) as total FROM barang WHERE status = 'Rusak'"))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Inventaris - LabInventory</title>
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
                            <h1 class="page-title">Daftar Inventaris</h1>
                            <p class="page-subtitle">Kelola dan pantau seluruh aset laboratorium kampus Anda.</p>
                        </div>
                        <a href="tambah_barang.php" class="btn btn-primary px-4">
                            <i class="bi bi-plus-lg me-2"></i>Tambah Barang
                        </a>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6 col-xl-3">
                            <div class="stat-card">
                                <div class="stat-label mt-0">Total Barang</div>
                                <div class="stat-value fs-3"><?php echo number_format($total_barang, 0, ',', '.'); ?></div>
                                <span class="mini-pill green mt-2">+12%</span>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <div class="stat-card">
                                <div class="stat-label mt-0">Tersedia</div>
                                <div class="stat-value fs-3"><?php echo number_format($tersedia, 0, ',', '.'); ?></div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <div class="stat-card">
                                <div class="stat-label mt-0">Dipinjam</div>
                                <div class="stat-value fs-3"><?php echo number_format($dipinjam, 0, ',', '.'); ?></div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <div class="stat-card">
                                <div class="stat-label mt-0">Rusak</div>
                                <div class="stat-value fs-3"><?php echo number_format($rusak, 0, ',', '.'); ?></div>
                                <span class="mini-pill red mt-2">Perlu Perhatian</span>
                            </div>
                        </div>
                    </div>

                    <section class="toolbar-card mb-4">
                        <form method="GET" class="row g-3 align-items-center">
                            <div class="col-lg">
                                <div class="toolbar-input">
                                    <i class="bi bi-search"></i>
                                    <input type="text" class="form-control" name="search" placeholder="Cari nama barang, serial number, atau lokasi..." value="<?php echo htmlspecialchars($search); ?>">
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-auto">
                                <select class="form-select h-100" aria-label="Kategori">
                                    <option>Semua Kategori</option>
                                </select>
                            </div>
                            <div class="col-sm-6 col-lg-auto">
                                <select class="form-select h-100" aria-label="Status">
                                    <option>Semua Status</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-light-soft px-3" type="submit" title="Terapkan filter">
                                    <i class="bi bi-sliders"></i>
                                </button>
                            </div>
                        </form>
                    </section>

                    <section class="table-card">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Kategori</th>
                                        <th>Stok / Qty</th>
                                        <th>Lokasi</th>
                                        <th>Status</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($barang)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-5">Tidak ada data barang</td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($barang as $b): ?>
                                    <?php
                                    $statusClass = $b['status'] == 'Tersedia' ? 'available' : ($b['status'] == 'Dipinjam' ? 'borrowed' : 'damaged');
                                    $progressClass = $b['status'] == 'Rusak' ? 'danger' : ($b['status'] == 'Dipinjam' ? 'warn' : '');
                                    $qtyPercent = min(100, max(8, (int)$b['jumlah'] * 8));
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="item-cell">
                                                <div class="item-thumb">
                                                    <i class="bi <?php echo stripos($b['nama_barang'], 'laptop') !== false || stripos($b['nama_barang'], 'pc') !== false ? 'bi-pc-display' : 'bi-router'; ?>"></i>
                                                </div>
                                                <div>
                                                    <span class="item-name"><?php echo htmlspecialchars($b['nama_barang']); ?></span>
                                                    <span class="item-meta">SN: <?php echo htmlspecialchars($b['kode_barang']); ?>-<?php echo str_pad($b['id'], 4, '0', STR_PAD_LEFT); ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($b['nama_kategori'] ?? '-'); ?></td>
                                        <td>
                                            <strong><?php echo (int)$b['jumlah']; ?>/15</strong>
                                            <div class="progress-line <?php echo $progressClass; ?> mt-2"><span style="width: <?php echo $qtyPercent; ?>%"></span></div>
                                        </td>
                                        <td><i class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($b['lokasi']); ?></td>
                                        <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo $b['status'] == 'Tersedia' ? 'Available' : ($b['status'] == 'Dipinjam' ? 'Borrowed' : 'Damaged'); ?></span></td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="icon-button" type="button" data-bs-toggle="dropdown" aria-label="Aksi">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="edit_barang.php?id=<?php echo $b['id']; ?>"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                                    <li><a class="dropdown-item text-danger" href="hapus_barang.php?id=<?php echo $b['id']; ?>" onclick="return confirm('Yakin ingin menghapus data ini?')"><i class="bi bi-trash me-2"></i>Hapus</a></li>
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
                            <span>Menampilkan <?php echo $total_data ? $offset + 1 : 0; ?>-<?php echo min($offset + $limit, $total_data); ?> dari <?php echo number_format($total_data, 0, ',', '.'); ?> barang</span>
                            <div class="d-flex gap-2">
                                <a class="btn btn-light-soft btn-sm <?php echo $page <= 1 ? 'disabled' : ''; ?>" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>">Sebelumnya</a>
                                <a class="btn btn-primary btn-sm <?php echo $page >= $total_pages ? 'disabled' : ''; ?>" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>">Berikutnya</a>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <?php include 'includes/footer.php'; ?>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>

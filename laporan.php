<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit;
}

require_once 'koneksi.php';

$filter = isset($_GET['filter']) ? mysqli_real_escape_string($koneksi, $_GET['filter']) : '';
$start_date = isset($_GET['start_date']) ? mysqli_real_escape_string($koneksi, $_GET['start_date']) : '';
$end_date = isset($_GET['end_date']) ? mysqli_real_escape_string($koneksi, $_GET['end_date']) : '';

$where = '';
if ($filter == 'tanggal' && $start_date && $end_date) {
    $where = "WHERE tanggal_masuk BETWEEN '$start_date' AND '$end_date'";
}

$laporan = fetch_all(query("SELECT b.*, k.nama_kategori FROM barang b
    LEFT JOIN kategori k ON b.kategori_id = k.id
    $where
    ORDER BY b.created_at DESC"));

$total = count($laporan);
$tersedia = 0;
$dipinjam = 0;
$rusak = 0;
$total_qty = 0;
foreach ($laporan as $l) {
    $total_qty += (int)$l['jumlah'];
    if ($l['status'] == 'Tersedia') {
        $tersedia++;
    } elseif ($l['status'] == 'Dipinjam') {
        $dipinjam++;
    } elseif ($l['status'] == 'Rusak') {
        $rusak++;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - LabInventory</title>
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
                            <h1 class="page-title">Laporan Inventaris</h1>
                            <p class="page-subtitle">Rekap kondisi, status, dan distribusi aset laboratorium.</p>
                        </div>
                        <button onclick="window.print()" class="btn btn-primary px-4">
                            <i class="bi bi-printer me-2"></i>Print Laporan
                        </button>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6 col-xl-3">
                            <div class="stat-card">
                                <div class="stat-label mt-0">Total Item</div>
                                <div class="stat-value fs-3"><?php echo number_format($total, 0, ',', '.'); ?></div>
                                <span class="mini-pill mt-2"><?php echo number_format($total_qty, 0, ',', '.'); ?> qty</span>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <div class="stat-card">
                                <div class="stat-label mt-0">Tersedia</div>
                                <div class="stat-value fs-3 text-success"><?php echo number_format($tersedia, 0, ',', '.'); ?></div>
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
                                <div class="stat-value fs-3 text-danger"><?php echo number_format($rusak, 0, ',', '.'); ?></div>
                                <span class="mini-pill red mt-2">Audit Prioritas</span>
                            </div>
                        </div>
                    </div>

                    <section class="toolbar-card mb-4 no-print">
                        <form method="GET" class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Filter Berdasarkan</label>
                                <select class="form-select" name="filter" onchange="toggleDateFilter(this.value)">
                                    <option value="">Semua Data</option>
                                    <option value="tanggal" <?php echo $filter == 'tanggal' ? 'selected' : ''; ?>>Tanggal Masuk</option>
                                </select>
                            </div>
                            <div class="col-md-3" id="dateRange">
                                <label class="form-label fw-semibold">Tanggal Awal</label>
                                <input type="date" class="form-control" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
                            </div>
                            <div class="col-md-3" id="dateRange2">
                                <label class="form-label fw-semibold">Tanggal Akhir</label>
                                <input type="date" class="form-control" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-funnel me-2"></i>Terapkan Filter
                                </button>
                            </div>
                        </form>
                    </section>

                    <section class="table-card">
                        <div class="table-card-header">
                            <h2 class="panel-title">Data Laporan Inventaris</h2>
                            <span class="mini-pill"><?php echo number_format($total, 0, ',', '.'); ?> data</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover" id="laporanTable">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Kategori</th>
                                        <th>Jumlah</th>
                                        <th>Lokasi</th>
                                        <th>Kondisi</th>
                                        <th>Status</th>
                                        <th>Tanggal Masuk</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($laporan)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-5">Tidak ada data laporan</td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($laporan as $l): ?>
                                    <?php
                                    $statusClass = $l['status'] == 'Tersedia' ? 'available' : ($l['status'] == 'Dipinjam' ? 'borrowed' : 'damaged');
                                    $kondisiClass = $l['kondisi'] == 'Baik' ? 'available' : ($l['kondisi'] == 'Rusak Ringan' ? 'borrowed' : 'damaged');
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="item-cell">
                                                <div class="item-thumb"><i class="bi bi-box-seam"></i></div>
                                                <div>
                                                    <span class="item-name"><?php echo htmlspecialchars($l['nama_barang']); ?></span>
                                                    <span class="item-meta">Kode: <?php echo htmlspecialchars($l['kode_barang']); ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($l['nama_kategori'] ?? '-'); ?></td>
                                        <td><strong><?php echo (int)$l['jumlah']; ?></strong></td>
                                        <td><i class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($l['lokasi']); ?></td>
                                        <td><span class="status-badge <?php echo $kondisiClass; ?>"><?php echo htmlspecialchars($l['kondisi']); ?></span></td>
                                        <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($l['status']); ?></span></td>
                                        <td><?php echo date('d M Y', strtotime($l['tanggal_masuk'])); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="table-card-footer">
                            <span>Dicetak dari LabInventory pada <?php echo date('d M Y'); ?></span>
                            <span><?php echo $filter == 'tanggal' ? 'Filter tanggal aktif' : 'Menampilkan semua data'; ?></span>
                        </div>
                    </section>
                </div>
            </div>

            <?php include 'includes/footer.php'; ?>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleDateFilter(value) {
            const dateRange = document.getElementById('dateRange');
            const dateRange2 = document.getElementById('dateRange2');
            const display = value === 'tanggal' ? 'block' : 'none';
            dateRange.style.display = display;
            dateRange2.style.display = display;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const filter = document.querySelector('select[name="filter"]');
            if (filter) {
                toggleDateFilter(filter.value);
            }
        });
    </script>
    <style>
        @media print {
            .sidebar, .topbar, .footer, .no-print, .btn {
                display: none !important;
            }
            .main-content {
                margin-left: 0 !important;
            }
            .content-wrapper {
                padding: 0 !important;
            }
            .table-card, .stat-card {
                border-color: #999 !important;
            }
            .status-badge, .mini-pill {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
    <script src="assets/js/script.js"></script>
</body>
</html>

<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit;
}

require_once 'koneksi.php';

$total_barang = fetch_assoc(query("SELECT COUNT(*) as total FROM barang"))['total'];
$tersedia = fetch_assoc(query("SELECT COUNT(*) as total FROM barang WHERE status = 'Tersedia'"))['total'];
$dipinjam = fetch_assoc(query("SELECT COUNT(*) as total FROM barang WHERE status = 'Dipinjam'"))['total'];
$rusak = fetch_assoc(query("SELECT COUNT(*) as total FROM barang WHERE status = 'Rusak'"))['total'];

$kategori = fetch_all(query("SELECT k.nama_kategori, COUNT(b.id) AS total
    FROM kategori k
    LEFT JOIN barang b ON b.kategori_id = k.id
    GROUP BY k.id, k.nama_kategori
    ORDER BY total DESC
    LIMIT 4"));

$chart_kategori = fetch_all(query("SELECT k.nama_kategori, COUNT(b.id) AS total
    FROM kategori k
    LEFT JOIN barang b ON b.kategori_id = k.id
    GROUP BY k.id, k.nama_kategori
    HAVING total > 0
    ORDER BY total DESC"));

$peminjaman_aktif = fetch_all(query("SELECT p.*, b.nama_barang, b.kode_barang
    FROM peminjaman p
    LEFT JOIN barang b ON p.barang_id = b.id
    ORDER BY p.created_at DESC
    LIMIT 3"));

$aset_populer = fetch_all(query("SELECT b.*, k.nama_kategori, COUNT(p.id) AS total_pinjam
    FROM barang b
    LEFT JOIN kategori k ON b.kategori_id = k.id
    LEFT JOIN peminjaman p ON p.barang_id = b.id
    GROUP BY b.id
    ORDER BY total_pinjam DESC, b.created_at DESC
    LIMIT 4"));

$namaUser = $_SESSION['nama_lengkap'] ?? 'Admin';
$totalKategori = max(array_sum(array_column($kategori, 'total')), 1);
$barClasses = ['bar-blue', 'bar-slate', 'bar-light', 'bar-pale'];
$dotColors = ['#075adf', '#53667c', '#a9c4f1', '#c9d2e6'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - LabInventory</title>
    <?php include 'includes/head_assets.php'; ?>
</head>
<body>
    <div class="app-shell">
        <?php include 'includes/sidebar.php'; ?>

        <main class="main-content">
            <?php include 'includes/navbar.php'; ?>

            <div class="content-wrapper">
                <div class="container-fluid">
                    <div class="mb-4">
                        <h1 class="page-title">Halo, <?php echo htmlspecialchars(explode(' ', $namaUser)[0]); ?>!</h1>
                        <p class="page-subtitle">Berikut ringkasan laboratorium hari ini.</p>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6 col-xl-3">
                            <div class="stat-card">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="stat-icon"><i class="bi bi-archive-fill"></i></div>
                                    <span class="mini-pill">+12% bln ini</span>
                                </div>
                                <div class="stat-label">Total Barang</div>
                                <div class="stat-value"><?php echo number_format($total_barang, 0, ',', '.'); ?></div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <div class="stat-card">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="stat-icon green"><i class="bi bi-check-circle-fill"></i></div>
                                    <span class="mini-pill green">Stabil</span>
                                </div>
                                <div class="stat-label">Tersedia</div>
                                <div class="stat-value"><?php echo number_format($tersedia, 0, ',', '.'); ?></div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <div class="stat-card">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="stat-icon slate"><i class="bi bi-clock-fill"></i></div>
                                    <span class="mini-pill">+4%</span>
                                </div>
                                <div class="stat-label">Dipinjam</div>
                                <div class="stat-value"><?php echo number_format($dipinjam, 0, ',', '.'); ?></div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <div class="stat-card">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="stat-icon red"><i class="bi bi-exclamation-octagon-fill"></i></div>
                                    <span class="mini-pill red">-2%</span>
                                </div>
                                <div class="stat-label">Rusak</div>
                                <div class="stat-value"><?php echo number_format($rusak, 0, ',', '.'); ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-xl-8">
                            <section class="ui-card p-4 h-100">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h2 class="panel-title">Struktur Inventaris</h2>
                                    <a href="barang.php" class="section-link">Detail Kategori</a>
                                </div>

                                <div class="inventory-bar mb-4">
                                    <?php foreach ($kategori as $index => $kat): ?>
                                        <?php $percent = max(8, round(($kat['total'] / $totalKategori) * 100)); ?>
                                        <span class="<?php echo $barClasses[$index] ?? 'bar-pale'; ?>" style="width: <?php echo $percent; ?>%"><?php echo $percent; ?>%</span>
                                    <?php endforeach; ?>
                                </div>

                                <div class="legend-grid">
                                    <?php foreach ($kategori as $index => $kat): ?>
                                    <div class="legend-item">
                                        <span class="legend-dot" style="background: <?php echo $dotColors[$index] ?? '#c9d2e6'; ?>"></span>
                                        <?php echo htmlspecialchars($kat['nama_kategori']); ?>
                                        <strong><?php echo number_format($kat['total'], 0, ',', '.'); ?> Item</strong>
                                    </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="chart-panel mt-4">
                                    <div id="kategoriChart" class="interactive-chart" aria-label="Diagram jumlah item per kategori"></div>
                                    <div id="kategoriChartTooltip" class="chart-tooltip" role="status"></div>
                                </div>
                            </section>
                        </div>

                        <div class="col-xl-4">
                            <section class="ui-card p-4 h-100">
                                <h2 class="panel-title">Aktivitas Terbaru</h2>
                                <div class="activity-list">
                                    <?php if (empty($peminjaman_aktif)): ?>
                                        <p class="text-muted mb-0">Belum ada aktivitas terbaru.</p>
                                    <?php else: ?>
                                        <?php foreach ($peminjaman_aktif as $index => $p): ?>
                                        <div class="activity-item">
                                            <div class="activity-icon <?php echo $index == 2 ? 'green' : ($index == 1 ? 'slate' : ''); ?>">
                                                <i class="bi <?php echo $index == 0 ? 'bi-arrow-left-right' : ($index == 1 ? 'bi-arrow-clockwise' : 'bi-box-arrow-in-left'); ?>"></i>
                                            </div>
                                            <div>
                                                <p class="activity-text">
                                                    <?php echo htmlspecialchars($p['status']); ?> <?php echo htmlspecialchars($p['kode_barang'] ?: $p['nama_barang']); ?> oleh <?php echo htmlspecialchars($p['nama_peminjam']); ?>
                                                </p>
                                                <span class="activity-time"><?php echo date('d M Y', strtotime($p['created_at'])); ?></span>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                                <a href="peminjaman.php" class="btn btn-light-soft w-100">Lihat Semua Aktivitas</a>
                            </section>
                        </div>
                    </div>

                    <section class="table-card">
                        <div class="table-card-header">
                            <h2 class="panel-title">Aset Paling Sering Dipinjam</h2>
                            <div class="d-flex gap-2">
                                <button class="btn btn-light-soft btn-sm" type="button"><i class="bi bi-funnel me-1"></i> Filter</button>
                                <button class="btn btn-light-soft btn-sm" type="button"><i class="bi bi-download me-1"></i> Export</button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Aset</th>
                                        <th>Kategori</th>
                                        <th>Total Peminjaman</th>
                                        <th>Status Stok</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($aset_populer as $aset): ?>
                                    <?php $qtyPercent = min(100, max(8, (int)$aset['jumlah'] * 8)); ?>
                                    <tr>
                                        <td>
                                            <div class="item-cell">
                                                <div class="item-thumb"><i class="bi bi-laptop"></i></div>
                                                <div>
                                                    <span class="item-name"><?php echo htmlspecialchars($aset['nama_barang']); ?></span>
                                                    <span class="item-meta">ID: <?php echo htmlspecialchars($aset['kode_barang']); ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($aset['nama_kategori'] ?? '-'); ?></td>
                                        <td><?php echo (int)$aset['total_pinjam']; ?> Kali</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress-line"><span style="width: <?php echo $qtyPercent; ?>%"></span></div>
                                                <small><?php echo (int)$aset['jumlah']; ?>/15</small>
                                            </div>
                                        </td>
                                        <td class="text-end"><i class="bi bi-three-dots-vertical"></i></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>
            </div>

            <?php include 'includes/footer.php'; ?>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const kategoriChartData = <?php echo json_encode($chart_kategori); ?>;

        function renderKategoriChart() {
            const chart = document.getElementById('kategoriChart');
            const tooltip = document.getElementById('kategoriChartTooltip');
            if (!chart || !kategoriChartData.length) {
                return;
            }

            const width = chart.clientWidth || 720;
            const height = chart.clientHeight || 230;
            const padding = { top: 20, right: 18, bottom: 48, left: 36 };
            const plotWidth = width - padding.left - padding.right;
            const plotHeight = height - padding.top - padding.bottom;
            const maxValue = Math.max(...kategoriChartData.map(item => Number(item.total)), 1);
            const gap = 18;
            const barWidth = Math.max(24, (plotWidth - gap * (kategoriChartData.length - 1)) / kategoriChartData.length);

            let svg = `<svg viewBox="0 0 ${width} ${height}" preserveAspectRatio="none" role="img">`;
            svg += `<line x1="${padding.left}" y1="${padding.top + plotHeight}" x2="${width - padding.right}" y2="${padding.top + plotHeight}" stroke="#c9d6ec" stroke-width="1" />`;

            for (let i = 0; i <= 3; i++) {
                const y = padding.top + plotHeight - (plotHeight / 3) * i;
                svg += `<line x1="${padding.left}" y1="${y}" x2="${width - padding.right}" y2="${y}" stroke="#dce6f8" stroke-width="1" />`;
            }

            kategoriChartData.forEach((item, index) => {
                const value = Number(item.total);
                const barHeight = Math.max(8, (value / maxValue) * plotHeight);
                const x = padding.left + index * (barWidth + gap);
                const y = padding.top + plotHeight - barHeight;
                const label = String(item.nama_kategori).replace(/[&<>"']/g, '');

                svg += `<rect class="chart-bar" data-label="${label}" data-value="${value}" x="${x}" y="${y}" width="${barWidth}" height="${barHeight}" rx="5" fill="#aecaef" />`;
                svg += `<text x="${x + barWidth / 2}" y="${height - 18}" text-anchor="middle" fill="#536176" font-size="11" font-weight="700">${label.length > 14 ? label.slice(0, 13) + '...' : label}</text>`;
            });

            svg += `</svg>`;
            chart.innerHTML = svg;

            chart.querySelectorAll('.chart-bar').forEach(function(bar) {
                bar.addEventListener('mouseenter', function(event) {
                    tooltip.textContent = `${this.dataset.label}: ${this.dataset.value} item`;
                    tooltip.style.opacity = '1';
                    tooltip.style.left = `${event.offsetX + 12}px`;
                    tooltip.style.top = `${event.offsetY - 12}px`;
                });
                bar.addEventListener('mousemove', function(event) {
                    tooltip.style.left = `${event.offsetX + 12}px`;
                    tooltip.style.top = `${event.offsetY - 12}px`;
                });
                bar.addEventListener('mouseleave', function() {
                    tooltip.style.opacity = '0';
                });
            });
        }

        window.addEventListener('resize', renderKategoriChart);
        renderKategoriChart();
    </script>
    <script src="assets/js/script.js"></script>
</body>
</html>

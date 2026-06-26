<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$namaUser = $_SESSION['nama_lengkap'] ?? 'Admin Utama';
?>
<aside class="sidebar">
    <div class="brand-block">
        <h1 class="brand-title">LabInventory</h1>
        <div class="brand-subtitle">Academic Inventory</div>
    </div>

    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-link <?php echo $currentPage == 'dashboard.php' ? 'active' : ''; ?>">
            <i class="bi bi-grid"></i> Dashboard
        </a>
        <a href="barang.php" class="nav-link <?php echo in_array($currentPage, ['barang.php', 'tambah_barang.php', 'edit_barang.php']) ? 'active' : ''; ?>">
            <i class="bi bi-archive"></i> Inventaris
        </a>
        <a href="#" class="nav-link">
            <i class="bi bi-diagram-3"></i> Kategori
        </a>
        <a href="peminjaman.php" class="nav-link <?php echo $currentPage == 'peminjaman.php' ? 'active' : ''; ?>">
            <i class="bi bi-arrow-left-right"></i> Peminjaman
        </a>
        <a href="laporan.php" class="nav-link <?php echo $currentPage == 'laporan.php' ? 'active' : ''; ?>">
            <i class="bi bi-file-earmark-text"></i> Laporan
        </a>
        <a href="#" class="nav-link">
            <i class="bi bi-people"></i> Pengguna
        </a>
    </nav>

    <div class="sidebar-profile">
        <div class="avatar photo"><?php echo strtoupper(substr($namaUser, 0, 1)); ?></div>
        <div>
            <strong><?php echo htmlspecialchars($namaUser); ?></strong>
            <span>Lab Manager</span>
        </div>
    </div>
</aside>

<?php $namaUser = $_SESSION['nama_lengkap'] ?? 'Admin'; ?>
<header class="topbar">
    <div class="container-fluid d-flex align-items-center gap-3 px-4">
        <button class="icon-button d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarCollapse" aria-label="Buka menu">
            <i class="bi bi-list"></i>
        </button>

        <div class="ms-auto d-flex align-items-center gap-3">
            <span class="fw-bold small d-none d-md-inline"><?php echo htmlspecialchars($namaUser); ?></span>
            <div class="dropdown">
                <button class="avatar photo border-0" type="button" data-bs-toggle="dropdown" aria-label="Menu akun">
                    <?php echo strtoupper(substr($namaUser, 0, 1)); ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</header>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
$role = $_SESSION['role'] ?? 'user';
$is_admin = ($role == 'admin');
$is_owner = ($role == 'owner');
$is_management = ($is_admin || $is_owner);

// Define public pages where we want to show the Public Menu
$public_pages = ['beranda.php', 'wisata.php', 'kuliner.php', 'penginapan.php', 'artikel.php', 'kontak.php', 'detail.php', 'article_detail.php'];
$is_on_public_page = in_array($current_page, $public_pages);

// For Owners, get their business details (for management menu only)
$owner_category = '';
$owner_item_id = 0;
if ($is_owner) {
    include 'db_config.php';
    $uid = $_SESSION['user_id'];
    $cat_res = mysqli_query($conn, "SELECT id, category FROM items WHERE owner_id = $uid LIMIT 1");
    if ($row = mysqli_fetch_assoc($cat_res)) {
        $owner_category = $row['category'];
        $owner_item_id = $row['id'];
    }
}
?>
<aside class="sidebar">
    <!-- Profile Section -->
    <div class="sidebar-auth-section" style="margin-bottom: 25px; padding: 0 5px;">
        <?php if (isset($_SESSION['username'])): ?>
            <div class="compact-profile" style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 32px; height: 32px; background: var(--primary); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 0.7rem; flex-shrink: 0;">
                    <?= strtoupper(substr($_SESSION['username'], 0, 1)) ?>
                </div>
                <div style="flex-grow: 1; min-width: 0;">
                    <p style="font-weight: 700; color: var(--primary); font-size: 0.8rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin: 0;">
                        <?= htmlspecialchars($_SESSION['username']) ?>
                        <span style="font-size: 0.55rem; background: <?= $is_admin ? '#ef4444' : 'var(--nature)' ?>; color: white; padding: 1px 4px; border-radius: 4px; margin-left: 2px;"><?= strtoupper($role) ?></span>
                    </p>
                    <a href="logout.php" style="font-size: 0.65rem; color: #ef4444; text-decoration: none; font-weight: 600;">Keluar</a>
                </div>
            </div>
        <?php else: ?>
            <a href="login.php" style="display: flex; align-items: center; gap: 8px; text-decoration: none; color: var(--primary); font-weight: 700; font-size: 0.8rem;">
                <i class="fas fa-user-circle" style="font-size: 1.2rem; opacity: 0.5;"></i>
                <span>Masuk Akun</span>
            </a>
        <?php endif; ?>
    </div>

    <div class="brand" style="margin-bottom: 25px; font-size: 1.4rem;">JEJAK <span>NEGERI</span></div>

    <nav>
        <?php if ($is_admin && !$is_on_public_page): ?>
            <!-- ADMIN MANAGEMENT MENU -->
            <p style="font-size: 0.55rem; color: #94a3b8; text-transform: uppercase; font-weight: 800; letter-spacing: 1.5px; margin-bottom: 10px; padding-left: 10px;">Control Center</p>
            <a href="dashboard.php" class="<?= ($current_page == 'dashboard.php') ? 'active' : '' ?>"><i class="fas fa-chart-line"></i> Dashboard Pusat</a>
            <a href="manage_articles.php" class="<?= ($current_page == 'manage_articles.php') ? 'active' : '' ?>"><i class="fas fa-newspaper"></i> Manajemen Jurnal</a>
            <a href="manage_owners.php" class="<?= ($current_page == 'manage_owners.php') ? 'active' : '' ?>"><i class="fas fa-users-cog"></i> Manajemen Owner</a>
            <a href="beranda.php" target="_blank"><i class="fas fa-globe"></i> Lihat Website</a>

        <?php elseif ($is_owner && !$is_on_public_page): ?>
            <!-- OWNER MANAGEMENT MENU -->
            <p style="font-size: 0.55rem; color: #94a3b8; text-transform: uppercase; font-weight: 800; letter-spacing: 1.5px; margin-bottom: 10px; padding-left: 10px;">Owner Panel</p>
            <a href="owner_dashboard.php" class="<?= ($current_page == 'owner_dashboard.php') ? 'active' : '' ?>"><i class="fas fa-store"></i> Ringkasan Bisnis</a>
            
            <?php if ($owner_category == 'kuliner'): ?>
                <a href="owner_dashboard.php#manage"><i class="fas fa-utensils"></i> Update Daftar Menu</a>
            <?php elseif ($owner_category == 'penginapan'): ?>
                <a href="owner_dashboard.php#manage"><i class="fas fa-bed"></i> Kelola Kamar & Fasilitas</a>
            <?php elseif ($owner_category == 'wisata'): ?>
                <a href="owner_dashboard.php#bullhorn"><i class="fas fa-bullhorn"></i> Status & Pengumungan</a>
            <?php endif; ?>

            <a href="beranda.php" target="_blank"><i class="fas fa-eye"></i> Cek Tampilan Live</a>

        <?php else: ?>
            <!-- PUBLIC MENU (Visible on Beranda, Wisata, etc.) -->
            <p style="font-size: 0.55rem; color: #94a3b8; text-transform: uppercase; font-weight: 800; letter-spacing: 1.5px; margin-bottom: 10px; padding-left: 10px;">Menu Utama</p>
            <a href="beranda.php" class="<?= ($current_page == 'beranda.php') ? 'active' : '' ?>"><i class="fas fa-home"></i> Beranda</a>
            <a href="wisata.php" class="<?= ($current_page == 'wisata.php') ? 'active' : '' ?>"><i class="fas fa-mountain"></i> Wisata</a>
            <a href="kuliner.php" class="<?= ($current_page == 'kuliner.php') ? 'active' : '' ?>"><i class="fas fa-utensils"></i> Kuliner</a>
            <a href="penginapan.php" class="<?= ($current_page == 'penginapan.php') ? 'active' : '' ?>"><i class="fas fa-hotel"></i> Akomodasi</a>
            <a href="artikel.php" class="<?= ($current_page == 'artikel.php') ? 'active' : '' ?>"><i class="fas fa-newspaper"></i> Informasi</a>
            <a href="kontak.php" class="<?= ($current_page == 'kontak.php') ? 'active' : '' ?>"><i class="fas fa-envelope"></i> Kontak</a>
        <?php endif; ?>
    </nav>
</aside>

<script>
    window.isLoggedIn = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;
    window.currentUserId = <?= $_SESSION['user_id'] ?? 'null' ?>;
</script>

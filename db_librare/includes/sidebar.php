<nav class="main-nav">
    <div style="margin-bottom: 20px; font-weight: bold; color: var(--accent-pink);">
        MENU UTAMA
    </div>
    
    <a href="beranda.php" style="display: block; margin-bottom: 12px; text-decoration: none; color: #1A1A1A; font-weight: 600;">
        🏠 Beranda Pelanggan
    </a>

    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'owner'): ?>
        <a href="owner_dashboard.php" style="display: block; margin-bottom: 12px; text-decoration: none; color: #1A1A1A; font-weight: 600;">
            📊 Dashboard Owner
        </a>
    <?php endif; ?>

    <!-- Menu Kelola Produk Katalog Admin/Staff -->
    <a href="admin_produk.php" style="display: block; margin-bottom: 12px; text-decoration: none; color: #1A1A1A; font-weight: 600;">
        👕 Kelola Produk Toko
    </a>

    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'kasir'): ?>
        <a href="pos.php" style="display: block; margin-bottom: 12px; text-decoration: none; color: #1A1A1A; font-weight: 600;">
            🛒 Kasir (POS)
        </a>
    <?php endif; ?>

    <a href="pengeluaran.php" style="display: block; margin-bottom: 12px; text-decoration: none; color: #1A1A1A; font-weight: 600;">
        💸 Catat Pengeluaran
    </a>

    <a href="logout.php" class="btn-pink" style="margin-top: 20px; text-align: center; display: block; text-decoration: none;">
        🚪 Logout
    </a>
</nav>
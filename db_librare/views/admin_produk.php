<?php
// 1. PINDAHKAN KONEKSI & LOGIKA PROSES KE PALING ATAS (SEBELUM HEADER HTML DIPANGGIL)
require_once __DIR__ . '/../config/koneksi.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// LOGIKA TAMBAH PRODUK BARU
if (isset($_POST['tambah_produk'])) {
    $nama_kaos   = mysqli_real_escape_string($koneksi, $_POST['nama_kaos']);
    $category_id = intval($_POST['category_id']);
    $koleksi     = mysqli_real_escape_string($koneksi, $_POST['koleksi']);
    $harga       = intval($_POST['harga']);
    $stok        = intval($_POST['stok']);
    $status_stok = ($stok > 0) ? 'READY' : 'OUT OF STOCK';
    $deskripsi   = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);

    // Upload Foto Produk
    $foto_baru = 'default.jpg';
    if (!empty($_FILES['foto']['name'])) {
        $target_dir = __DIR__ . "/../assets/images/products/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto_baru = time() . '_' . rand(100, 999) . '.' . $ext;
        move_uploaded_file($_FILES['foto']['tmp_name'], $target_dir . $foto_baru);
    }

    // Insert ke Database
    mysqli_query($koneksi, "INSERT INTO products (category_id, nama_kaos, koleksi, harga, stok, status_stok, foto, deskripsi) 
                    VALUES ($category_id, '$nama_kaos', '$koleksi', $harga, $stok, '$status_stok', '$foto_baru', '$deskripsi')");

    $product_id = mysqli_insert_id($koneksi);

    // Varian Ukuran
    if (isset($_POST['ukuran_s'])) mysqli_query($koneksi, "INSERT INTO product_variants (product_id, ukuran, stok_ukuran) VALUES ($product_id, 'S', " . intval($_POST['stok_s']) . ")");
    if (isset($_POST['ukuran_m'])) mysqli_query($koneksi, "INSERT INTO product_variants (product_id, ukuran, stok_ukuran) VALUES ($product_id, 'M', " . intval($_POST['stok_m']) . ")");
    if (isset($_POST['ukuran_l'])) mysqli_query($koneksi, "INSERT INTO product_variants (product_id, ukuran, stok_ukuran) VALUES ($product_id, 'L', " . intval($_POST['stok_l']) . ")");
    if (isset($_POST['ukuran_xl'])) mysqli_query($koneksi, "INSERT INTO product_variants (product_id, ukuran, stok_ukuran) VALUES ($product_id, 'XL', " . intval($_POST['stok_xl']) . ")");

    header('Location: admin_produk.php');
    exit;
}

// LOGIKA UBAH STATUS STOK (TOGGLE READY / OUT OF STOCK)
if (isset($_GET['toggle_status'])) {
    $id = intval($_GET['toggle_status']);
    $q = mysqli_query($koneksi, "SELECT status_stok, stok FROM products WHERE id=$id");
    if ($row = mysqli_fetch_assoc($q)) {
        $new_status = ($row['status_stok'] == 'READY') ? 'OUT OF STOCK' : 'READY';
        $new_stok = ($new_status == 'OUT OF STOCK') ? 0 : (($row['stok'] == 0) ? 5 : $row['stok']);
        mysqli_query($koneksi, "UPDATE products SET status_stok='$new_status', stok=$new_stok WHERE id=$id");
    }
    header('Location: admin_produk.php');
    exit;
}

// LOGIKA HAPUS PRODUK
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($koneksi, "DELETE FROM products WHERE id=$id");
    header('Location: admin_produk.php');
    exit;
}

// 2. TAMPILKAN HEADER HTML SETELAH SELURUH PROSES PHP / REDIRECT SELESAI
require_once '../includes/header.php';
?>

<div class="content-area" style="gap: 20px;">
    <!-- Sidebar Menu Staff/Owner -->
    <div style="flex: 1; max-width: 220px; background: white; padding: 20px; border-radius: 8px; border: 1px solid var(--border-color);">
        <?php include '../includes/sidebar.php'; ?>
    </div>

    <div style="flex: 4; display: flex; flex-direction: column; gap: 20px;">
        <!-- Header Management -->
        <div style="background: white; padding: 15px 20px; border-radius: 8px; border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h2>KELOLA KATALOG PRODUK ADMIN</h2>
            <a href="beranda.php" target="_blank" class="btn-secondary" style="font-size: 12px; text-decoration: none;">👁️ Lihat Beranda Toko</a>
        </div>

        <div style="display: flex; gap: 20px;">
            <!-- Form Input Produk Baru -->
            <div class="form-card" style="flex: 1; max-width: 380px;">
                <h3>+ TAMBAH PRODUK BARU</h3>
                <form method="POST" enctype="multipart/form-data" style="margin-top: 15px;">
                    <div class="form-group">
                        <label>Nama Produk / Artikel:</label>
                        <input type="text" name="nama_kaos" class="form-control" placeholder="Contoh: PURE SHINE" required>
                    </div>

                    <div class="form-group">
                        <label>Kategori Produk:</label>
                        <select name="category_id" class="form-control" required>
                            <?php
                            $cats = mysqli_query($koneksi, "SELECT * FROM categories");
                            while ($c = mysqli_fetch_assoc($cats)) {
                                echo "<option value='{$c['id']}'>{$c['nama_kategori']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Koleksi:</label>
                        <input type="text" name="koleksi" class="form-control" value="BEYOND MEASURE" required>
                    </div>

                    <div class="form-group">
                        <label>Harga Produk (Rp):</label>
                        <input type="number" name="harga" class="form-control" placeholder="330000" min="1" required>
                    </div>

                    <div class="form-group">
                        <label>Total Stok Keseluruhan:</label>
                        <input type="number" name="stok" class="form-control" placeholder="8" min="0" required>
                    </div>

                    <!-- Input Detail Ukuran -->
                    <div class="form-group">
                        <label>Varian & Stok Ukuran:</label>
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px;">
                            <label><input type="checkbox" name="ukuran_s" checked> S: <input type="number" name="stok_s" value="2" style="width: 50px;"></label>
                            <label><input type="checkbox" name="ukuran_m" checked> M: <input type="number" name="stok_m" value="3" style="width: 50px;"></label>
                            <label><input type="checkbox" name="ukuran_l" checked> L: <input type="number" name="stok_l" value="2" style="width: 50px;"></label>
                            <label><input type="checkbox" name="ukuran_xl" checked> XL: <input type="number" name="stok_xl" value="1" style="width: 50px;"></label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Foto Produk (Format Gambar):</label>
                        <input type="file" name="foto" class="form-control" accept="image/*">
                    </div>

                    <div class="form-group">
                        <label>Deskripsi Produk:</label>
                        <textarea name="deskripsi" class="form-control" rows="3" placeholder="Heavyweight cotton America 16s..."></textarea>
                    </div>

                    <button type="submit" name="tambah_produk" class="btn-pink" style="width: 100%;">SIMPAN & TAMPILKAN</button>
                </form>
            </div>

            <!-- Tabel Daftar Produk -->
            <div style="flex: 2; background: white; padding: 20px; border-radius: 8px; border: 1px solid var(--border-color); overflow-x: auto;">
                <h3>DAFTAR KATALOG AKTIF</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Nama Artikel</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Status Beranda</th>
                            <th>Aksi Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $products = mysqli_query($koneksi, "SELECT p.*, c.nama_kategori FROM products p LEFT JOIN categories c ON p.category_id=c.id ORDER BY p.id DESC");
                        while ($p = mysqli_fetch_assoc($products)):
                            $foto_path = "../assets/images/products/" . $p['foto'];
                            if (!file_exists($foto_path) || empty($p['foto'])) {
                                $foto_path = "https://via.placeholder.com/80?text=" . urlencode($p['nama_kaos']);
                            }
                        ?>
                        <tr>
                            <td>
                                <img src="<?= $foto_path ?>" alt="<?= $p['nama_kaos'] ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                            </td>
                            <td>
                                <strong><?= $p['nama_kaos'] ?></strong><br>
                                <small style="color: #888;"><?= $p['nama_kategori'] ?> | <?= $p['koleksi'] ?></small>
                            </td>
                            <td>Rp <?= number_format($p['harga'], 0, ',', '.') ?></td>
                            <td><strong><?= $p['stok'] ?> Pcs</strong></td>
                            <td>
                                <?php if ($p['status_stok'] == 'READY'): ?>
                                    <span style="background: green; color: white; padding: 3px 8px; border-radius: 3px; font-size: 10px; font-weight: bold;">READY</span>
                                <?php else: ?>
                                    <span style="background: black; color: white; padding: 3px 8px; border-radius: 3px; font-size: 10px; font-weight: bold;">OUT OF STOCK</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="admin_produk.php?toggle_status=<?= $p['id'] ?>" class="btn-secondary" style="padding: 4px 8px; font-size: 11px; text-decoration: none;">
                                    🔄 Ubah Status
                                </a>
                                <a href="admin_produk.php?hapus=<?= $p['id'] ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')" style="color: red; margin-left: 8px; font-size: 12px; text-decoration: none;">
                                    🗑️ Hapus
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
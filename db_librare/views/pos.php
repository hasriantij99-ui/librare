<?php
require_once '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'kasir') {
    header('Location: index.php');
    exit;
}

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if (isset($_GET['add'])) {
    $id = intval($_GET['add']);
    $query = mysqli_query($koneksi, "SELECT * FROM products WHERE id=$id");
    if ($prod = mysqli_fetch_assoc($query)) {
        $in_cart = $_SESSION['cart'][$id]['qty'] ?? 0;
        if ($prod['stok'] > $in_cart) {
            $_SESSION['cart'][$id] = [
                'nama'  => $prod['nama_kaos'],
                'harga' => $prod['harga'],
                'qty'   => $in_cart + 1
            ];
        }
    }
    header('Location: pos.php');
    exit;
}

if (isset($_GET['clear'])) {
    unset($_SESSION['cart']);
    header('Location: pos.php');
    exit;
}

$receipt_data = null;
if (isset($_POST['checkout'])) {
    $total = intval($_POST['total']);
    $paid = intval($_POST['paid']);
    $change = $paid - $total;
    $user_id = $_SESSION['user_id'];

    if ($total <= 0) {
        echo "<script>alert('Keranjang belanja kosong!'); window.location='pos.php';</script>";
    } else if ($paid < $total) {
        echo "<script>alert('Uang pembayaran tidak cukup!'); window.location='pos.php';</script>";
    } else {
        mysqli_query($koneksi, "INSERT INTO transactions (user_id, total_bayar) VALUES ($user_id, $total)");
        foreach ($_SESSION['cart'] as $p_id => $item) {
            $qty = $item['qty'];
            mysqli_query($koneksi, "UPDATE products SET stok = stok - $qty WHERE id = $p_id");
            mysqli_query($koneksi, "UPDATE products SET status_stok = 'OUT OF STOCK' WHERE id = $p_id AND stok <= 0");
        }

        $receipt_data = [
            'items'  => $_SESSION['cart'],
            'total'  => $total,
            'paid'   => $paid,
            'change' => $change
        ];
        unset($_SESSION['cart']);
    }
}
?>

<div class="content-area">
    <div style="flex: 1; max-width: 200px; background: white; padding: 20px; border-radius: 8px; border: 1px solid var(--border-color);">
        <?php include '../includes/sidebar.php'; ?>
    </div>

    <div style="flex: 2;">
        <h2>KATALOG PRODUK KASIR</h2>
        <div class="product-grid" style="grid-template-columns: repeat(3, 1fr); padding: 15px 0;">
            <?php
            $products = mysqli_query($koneksi, "SELECT * FROM products");
            while ($row = mysqli_fetch_assoc($products)):
                $in_cart = $_SESSION['cart'][$row['id']]['qty'] ?? 0;
                $real_stok = $row['stok'] - $in_cart;
                
                $foto_path = "../assets/images/products/" . $row['foto'];
                if (!file_exists($foto_path) || empty($row['foto'])) {
                    $foto_path = "https://via.placeholder.com/150?text=" . urlencode($row['nama_kaos']);
                }
            ?>
            <div class="product-card" style="border: 1px solid #ddd; padding: 10px; border-radius: 6px;">
                <img src="<?= $foto_path ?>" alt="<?= $row['nama_kaos'] ?>" class="product-img" style="height: 120px;">
                <h4 style="font-size: 13px; margin: 5px 0;"><?= $row['nama_kaos'] ?></h4>
                <p style="color: var(--accent-pink); font-weight: bold; font-size: 12px;">Rp <?= number_format($row['harga'], 0, ',', '.') ?></p>
                <p style="font-size: 11px; margin-bottom: 8px;">Stok: <strong><?= $real_stok ?></strong></p>
                <?php if ($real_stok > 0): ?>
                    <a href="pos.php?add=<?= $row['id'] ?>" class="btn-pink" style="padding: 4px 10px; font-size: 11px;">+ Tambah</a>
                <?php else: ?>
                    <button class="btn-secondary" style="padding: 4px 10px; font-size: 11px;" disabled>Habis</button>
                <?php endif; ?>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <div style="flex: 1; background: white; padding: 20px; border-radius: 8px; border: 1px solid var(--border-color);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2>KERANJANG</h2>
            <a href="pos.php?clear=1" style="color: red; font-size: 12px; text-decoration: none;">Reset</a>
        </div>
        <hr style="margin: 15px 0; border: 0; border-top: 1px solid var(--border-color);">
        
        <div style="max-height: 250px; overflow-y: auto;">
            <?php 
            $subtotal = 0;
            if (!empty($_SESSION['cart'])):
                foreach ($_SESSION['cart'] as $item): 
                    $t = $item['harga'] * $item['qty']; 
                    $subtotal += $t; 
            ?>
                <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                    <div>
                        <strong><?= $item['nama'] ?></strong><br>
                        <small><?= $item['qty'] ?>x @ Rp <?= number_format($item['harga'], 0, ',', '.') ?></small>
                    </div>
                    <div>Rp <?= number_format($t, 0, ',', '.') ?></div>
                </div>
            <?php 
                endforeach;
            else:
                echo "<p style='color: #888; text-align: center;'>Keranjang Kosong</p>";
            endif;
            ?>
        </div>

        <hr style="margin: 15px 0; border: 0; border-top: 1px solid var(--border-color);">
        <h3 style="font-size: 18px;">Total: Rp <?= number_format($subtotal, 0, ',', '.') ?></h3>

        <form method="POST" style="margin-top: 15px;">
            <input type="hidden" name="total" value="<?= $subtotal ?>">
            <div class="form-group">
                <label>Nominal Uang Bayar:</label>
                <input type="number" name="paid" class="form-control" placeholder="Masukkan nominal" required>
            </div>
            <button type="submit" name="checkout" class="btn-pink" style="width: 100%; padding: 12px;">BAYAR & CETAK STRUK</button>
        </form>
    </div>
</div>

<?php if ($receipt_data): ?>
<div id="printable-receipt" style="background: white; padding: 15px; border: 1px solid #ddd; margin: 20px;">
    <h3 style="text-align: center;">L I B R A R E</h3>
    <p style="text-align: center; font-size: 11px;"></p>
    <p>--------------------------------</p>
    <?php foreach ($receipt_data['items'] as $it): ?>
        <div><?= $it['nama'] ?> (<?= $it['qty'] ?>x)</div>
        <div style="text-align: right;">Rp <?= number_format($it['qty'] * $it['harga'], 0, ',', '.') ?></div>
    <?php endforeach; ?>
    <p>--------------------------------</p>
    <div>TOTAL : Rp <?= number_format($receipt_data['total'], 0, ',', '.') ?></div>
    <div>BAYAR : Rp <?= number_format($receipt_data['paid'], 0, ',', '.') ?></div>
    <div>KEMBALI: Rp <?= number_format($receipt_data['change'], 0, ',', '.') ?></div>
    <p>--------------------------------</p>
    <button onclick="cetakStruk()" class="btn-pink" style="width: 100%; margin-top: 10px;">CETAK STRUK</button>
</div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
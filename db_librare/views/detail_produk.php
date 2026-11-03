<?php
require_once '../includes/header.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$query = mysqli_query($koneksi, "SELECT p.*, c.nama_kategori FROM products p LEFT JOIN categories c ON p.category_id=c.id WHERE p.id=$id");

// Jika produk tidak ditemukan di database
if (!$query || mysqli_num_rows($query) == 0) {
    echo "<div class='content-area' style='padding: 60px; text-align: center;'>
            <h2>Produk Tidak Ditemukan</h2>
            <p style='color: #666; margin-top: 10px;'>Produk yang Anda cari tidak tersedia atau telah dihapus.</p>
            <a href='beranda.php' class='btn-pink' style='margin-top: 20px; display: inline-block; text-decoration: none;'>← Kembali ke Toko</a>
          </div>";
    require_once '../includes/footer.php';
    exit;
}

$prod = mysqli_fetch_assoc($query);
$p_id = $prod['id'];

// Logika Jalur Foto Produk (Aman jika gambar fisik belum dimasukkan)
$nama_foto = !empty($prod['foto']) ? $prod['foto'] : 'default.jpg';
$foto_path = "../assets/images/products/" . $nama_foto;

if (!file_exists(__DIR__ . "/../assets/images/products/" . $nama_foto) || empty($prod['foto'])) {
    $foto_path = "https://via.placeholder.com/600x600/F0F0F0/000000?text=" . urlencode($prod['nama_kaos']);
}

$is_ready = ($prod['status_stok'] == 'READY' && $prod['stok'] > 0);

// Logika Tambah Ke Keranjang
if (isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    $selected_size = isset($_POST['selected_size']) ? $_POST['selected_size'] : 'ALL SIZE';
    
    $cart_id = $p_id . '_' . $selected_size;
    $current_qty = isset($_SESSION['cart'][$cart_id]['qty']) ? $_SESSION['cart'][$cart_id]['qty'] : 0;
    
    $_SESSION['cart'][$cart_id] = [
        'id'     => $p_id,
        'nama'   => $prod['nama_kaos'],
        'harga'  => $prod['harga'],
        'ukuran' => $selected_size,
        'foto'   => $prod['foto'],
        'qty'    => $current_qty + 1
    ];

    echo "<script>alert('Produk berhasil ditambahkan ke keranjang!'); window.location='cart.php';</script>";
    exit;
}
?>

<!-- Header Banner Toko -->
<div style="background-color: #111; color: white; padding: 50px 20px; text-align: center; border-bottom: 1px solid #222;">
    <h1 style="font-size: 32px; font-weight: 900; letter-spacing: 3px; margin-bottom: 5px;">TOKO</h1>
    <p style="font-size: 12px; color: #888; letter-spacing: 2px;">BELANJA SEMUA</p>
</div>

<!-- Layout Detail Produk -->
<div class="content-area" style="flex-direction: column; align-items: center; padding: 40px 20px;">
    
    <div style="width: 100%; max-width: 600px; display: flex; flex-direction: column; align-items: center;">
        
        <!-- Gambar Produk -->
        <div style="width: 100%; max-width: 480px; height: 480px; background-color: #F8F8F8; border-radius: 4px; overflow: hidden; display: flex; justify-content: center; align-items: center; margin-bottom: 40px;">
            <img src="<?= $foto_path ?>" alt="<?= htmlspecialchars($prod['nama_kaos']) ?>" style="width: 100%; height: 100%; object-fit: contain;">
        </div>

        <!-- Detail Informasi & Opsi Ukuran -->
        <div style="width: 100%; text-align: left;">
            
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                <span style="height: 1px; width: 40px; background-color: #888;"></span>
                <span style="font-size: 11px; font-weight: 700; color: #777; letter-spacing: 1.5px; text-transform: uppercase;">
                    <?= strtoupper($prod['nama_kategori'] ?? 'STREETWEAR') ?>, KOLEKSI BARU, BELANJA SEMUA
                </span>
            </div>

            <!-- Nama Artikel / Produk -->
            <h1 style="font-size: 28px; font-weight: 900; letter-spacing: 1.5px; color: #000; margin-bottom: 10px; text-transform: UPPERCASE;">
                <?= htmlspecialchars($prod['nama_kaos']) ?>
            </h1>

            <!-- Harga Produk -->
            <p style="font-size: 16px; font-weight: 700; color: #000; margin-bottom: 30px;">
                Rp <?= number_format($prod['harga'], 0, ',', '.') ?>
            </p>

            <form method="POST" action="">
                <!-- Pilihan Ukuran -->
                <p style="font-size: 12px; font-weight: 800; letter-spacing: 1.5px; color: #000; margin-bottom: 12px;">
                    UKURAN
                </p>

                <div style="display: flex; gap: 10px; margin-bottom: 30px; flex-wrap: wrap;">
                    <?php
                    $q_var = mysqli_query($koneksi, "SELECT * FROM product_variants WHERE product_id=$p_id");
                    $has_variant = false;

                    if ($q_var && mysqli_num_rows($q_var) > 0):
                        $first = true;
                        while ($v = mysqli_fetch_assoc($q_var)):
                            $has_variant = true;
                    ?>
                        <label style="cursor: pointer;">
                            <input type="radio" name="selected_size" value="<?= $v['ukuran'] ?>" <?= $first ? 'checked' : '' ?> style="display: none;" class="size-radio">
                            <div class="size-box <?= $first ? 'active' : '' ?>">
                                <?= strtoupper($v['ukuran']) ?>
                            </div>
                        </label>
                    <?php
                            $first = false;
                        endwhile;
                    endif;

                    if (!$has_variant):
                    ?>
                        <label style="cursor: pointer;">
                            <input type="radio" name="selected_size" value="SEMUA UKURAN" checked style="display: none;" class="size-radio">
                            <div class="size-box active">
                                SEMUA UKURAN
                            </div>
                        </label>
                    <?php endif; ?>
                </div>

                <!-- Deskripsi Produk -->
                <div style="font-size: 13px; color: #555555; line-height: 1.7; margin-bottom: 35px;">
                    <?= !empty($prod['deskripsi']) ? nl2br(htmlspecialchars($prod['deskripsi'])) : '100% Cotton, tali pengatur di bagian belakang, logo bordir di bagian depan, buatan Indonesia.' ?>
                </div>

                <!-- Tombol Keranjang -->
                <?php if ($is_ready): ?>
                    <button type="submit" name="add_to_cart" style="width: 100%; background: #000000; color: #FFFFFF; border: none; padding: 18px 0; font-size: 12px; font-weight: 900; letter-spacing: 2px; text-transform: uppercase; cursor: pointer; border-radius: 2px;">
                        TAMBAHKAN KE KERANJANG
                    </button>
                <?php else: ?>
                    <button type="button" disabled style="width: 100%; background: #666666; color: #FFFFFF; border: none; padding: 18px 0; font-size: 12px; font-weight: 900; letter-spacing: 2px; text-transform: uppercase; cursor: not-allowed; border-radius: 2px;">
                        OUT OF STOCK
                    </button>
                <?php endif; ?>
            </form>

        </div>

    </div>

</div>

<style>
    .size-box {
        border: 1px solid #000000;
        background: #FFFFFF;
        color: #000000;
        padding: 12px 24px;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 1px;
        text-align: center;
        transition: all 0.2s ease;
    }

    .size-radio:checked + .size-box,
    .size-box.active {
        background: #000000 !important;
        color: #FFFFFF !important;
    }
</style>

<script>
    document.querySelectorAll('.size-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.size-box').forEach(box => box.classList.remove('active'));
            if (this.checked) {
                this.nextElementSibling.classList.add('active');
            }
        });
    });
</script>

<?php require_once '../includes/footer.php'; ?>
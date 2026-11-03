<?php require_once '../includes/header.php'; ?>

<!-- Header Hero Banner Keranjang -->
<div style="background-color: #111; color: white; padding: 50px 20px; text-align: center; border-bottom: 1px solid #222;">
    <h1 style="font-size: 36px; font-weight: 900; letter-spacing: 3px; margin-bottom: 10px;">KERANJANG ANDA</h1>
    <p style="font-size: 13px; color: #AAA; letter-spacing: 1px;">Periksa kembali barang-barang Anda sebelum melanjutkan ke pembayaran.</p>
</div>

<div class="content-area" style="flex-direction: column; align-items: center; padding: 40px 20px;">
    
    <?php 
    // Logika Hapus Barang dari Keranjang
    if (isset($_GET['remove'])) {
        $remove_id = $_GET['remove'];
        unset($_SESSION['cart'][$remove_id]);
        echo "<script>window.location='cart.php';</script>";
        exit;
    }

    $subtotal = 0;
    if (!empty($_SESSION['cart'])): 
        
        // 1. MEMBUAT FORMAT TEXT PESAN WHATSAPP OTOMATIS
        $wa_text = "Halo Admin LIBRARE, saya ingin menyelesaikan pemesanan berikut:\n\n";
        $wa_text .= "*RINCIAN PESANAN:*\n";
        $no = 1;

        foreach ($_SESSION['cart'] as $item) {
            $item_total = $item['harga'] * $item['qty'];
            $wa_text .= $no . ". " . $item['nama'] . " (Ukuran: " . $item['ukuran'] . ") x" . $item['qty'] . " = Rp " . number_format($item_total, 0, ',', '.') . "\n";
            $no++;
        }

        $wa_text .= "\n*TOTAL PEMBAYARAN:* Rp " . number_format($subtotal, 0, ',', '.') . "\n\n";
        $wa_text .= "Mohon diinformasikan nomor rekening / metode pembayaran dan total beserta ongkos kirimnya. Terima kasih!";

        // Encode teks ke URL WhatsApp (Ganti nomor 6281234567890 dengan nomor admin kamu)
        $no_admin = "6281939218778";
        $link_wa = "https://wa.me/" . $no_admin . "?text=" . urlencode($wa_text);
    ?>
        <!-- DAFTAR BARANG DI DALAM KERANJANG -->
        <div style="width: 100%; max-width: 600px; margin-bottom: 30px;">
            <?php 
            foreach ($_SESSION['cart'] as $cart_key => $item): 
                $item_total = $item['harga'] * $item['qty'];
                $subtotal += $item_total;
                
                $foto_path = "../assets/images/products/" . $item['foto'];
                if (!file_exists($foto_path) || empty($item['foto'])) {
                    $foto_path = "https://via.placeholder.com/100?text=LIBRARE";
                }
            ?>
                <div style="display: flex; gap: 15px; align-items: center; background: #FFFFFF; border: 1px solid #E5E5E5; padding: 15px; border-radius: 4px; margin-bottom: 10px;">
                    <img src="<?php echo $foto_path; ?>" alt="<?php echo htmlspecialchars($item['nama']); ?>" style="width: 70px; height: 70px; object-fit: cover; background: #f8f8f8; border-radius: 4px;">
                    
                    <div style="flex: 1;">
                        <h4 style="font-size: 14px; font-weight: 800; text-transform: uppercase; margin-bottom: 4px; color: #000;"><?php echo htmlspecialchars($item['nama']); ?></h4>
                        <p style="font-size: 12px; color: #666; margin-bottom: 4px;">Ukuran: <strong><?php echo htmlspecialchars($item['ukuran']); ?></strong> | Qty: <strong><?php echo $item['qty']; ?></strong></p>
                        <p style="font-size: 13px; font-weight: 700; color: #000;">Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></p>
                    </div>

                    <a href="cart.php?remove=<?php echo urlencode($cart_key); ?>" style="color: red; text-decoration: none; font-size: 18px; font-weight: bold; padding: 5px 10px;" title="Hapus Barang">&times;</a>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- CARD RINGKASAN PESANAN DENGAN TOMBOL WHATSAPP OTOMATIS -->
        <div style="width: 100%; max-width: 600px; background: #FFFFFF; border: 1px solid #E5E5E5; border-radius: 4px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
            
            <div style="padding: 25px 30px; border-bottom: 1px solid #F0F0F0;">
                <h3 style="font-size: 14px; font-weight: 800; letter-spacing: 1.5px; margin-bottom: 8px; color: #000;">RINGKASAN PESANAN</h3>
                <p style="font-size: 12px; color: #777; line-height: 1.4;">Ongkos kirim dan pajak akan dihitung saat pembayaran.</p>
            </div>

            <div style="padding: 25px 30px; border-bottom: 1px solid #F0F0F0;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 18px; font-size: 13px;">
                    <span style="color: #444;">Subtotal</span>
                    <span style="font-weight: 700; color: #000;">Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></span>
                </div>

                <div style="display: flex; justify-content: space-between; margin-bottom: 18px; font-size: 13px;">
                    <div>
                        <span style="color: #444; display: block;">Pengiriman</span>
                        <small style="color: #888; font-size: 11px;">Dihitung saat pembayaran</small>
                    </div>
                    <span style="color: #666;">Tidak Termasuk</span>
                </div>

                <div style="display: flex; justify-content: space-between; font-size: 13px;">
                    <div>
                        <span style="color: #444; display: block;">Pajak</span>
                        <small style="color: #888; font-size: 11px;">Berdasarkan alamat pengiriman Anda</small>
                    </div>
                    <span style="color: #666;">Tidak Termasuk</span>
                </div>
            </div>

            <div style="padding: 20px 30px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #F0F0F0;">
                <span style="font-size: 14px; font-weight: 900; letter-spacing: 1px; color: #000;">TOTAL</span>
                <span style="font-size: 14px; font-weight: 900; color: #000;">RP <?php echo number_format($subtotal, 0, ',', '.'); ?></span>
            </div>

            <!-- Tombol Mengarahkan ke WhatsApp Beserta Teks Rincian Otomatis -->
            <a href="<?php echo $link_wa; ?>" target="_blank" style="display: block; width: 100%; background: #000000; color: #FFFFFF; text-align: center; padding: 18px 0; font-size: 12px; font-weight: 800; letter-spacing: 2px; text-decoration: none; text-transform: uppercase;">
                LANJUTKAN KE PEMBAYARAN VIA WHATSAPP
            </a>
        </div>

    <?php else: ?>
        <!-- TAMPILAN JIKA KERANJANG KOSONG -->
        <div style="text-align: center; margin: 40px 0 60px 0;">
            <p style="font-size: 14px; color: #666666; margin-bottom: 20px;">Keranjang belanja Anda saat ini kosong.</p>
            <a href="beranda.php" class="btn-pink" style="padding: 12px 25px; text-decoration: none;">Mulai Belanja</a>
        </div>
    <?php endif; ?>

</div>

<?php require_once '../includes/footer.php'; ?>
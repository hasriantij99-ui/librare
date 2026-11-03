<?php require_once '../includes/header.php'; ?>

<!-- Popup Slogan Welcome -->
<div id="welcomeModal" class="modal-overlay">
    <div class="modal-content">
        <span class="modal-close" onclick="closeWelcomeModal()">&times;</span>
        <h3 style="font-size: 13px; letter-spacing: 2px; margin-bottom: 10px;">HI! YOU, WELCOME TO</h3>
        <h1 style="font-size: 36px; font-weight: 900; letter-spacing: 3px; margin-bottom: 20px;">L I B R A R E</h1>
        <button onclick="closeWelcomeModal()" class="btn-pink" style="background: black; padding: 12px 30px; font-weight: 800;">
            SHOP NOW
        </button>
    </div>
</div>

<!-- Header Banner Koleksi -->
<div style="background-color: #111; color: white; padding: 40px 20px; text-align: center; border-bottom: 1px solid #222;">
    <p style="font-size: 12px; font-weight: 700; letter-spacing: 2px; color: #888;">COLLECTION</p>
    <h1 style="font-size: 32px; font-weight: 900; letter-spacing: 3px; margin-top: 5px;">
        <?= isset($_GET['cat']) ? strtoupper($_GET['cat']) : 'ALL PRODUCTS & COLLECTIONS' ?>
    </h1>
</div>

<!-- Grid Katalog Produk -->
<div class="content-area" style="flex-direction: column;">
    <div class="product-grid">
        <?php
        $where_clause = "";
        if (isset($_GET['cat'])) {
            $cat_name = mysqli_real_escape_string($koneksi, $_GET['cat']);
            $where_clause = " WHERE c.nama_kategori = '$cat_name'";
        } else if (isset($_GET['col'])) {
            $col_name = mysqli_real_escape_string($koneksi, $_GET['col']);
            $where_clause = " WHERE p.koleksi = '$col_name'";
        }

        $query_prod = "SELECT p.*, c.nama_kategori FROM products p LEFT JOIN categories c ON p.category_id=c.id " . $where_clause;
        $products = mysqli_query($koneksi, $query_prod);

        if (mysqli_num_rows($products) > 0):
            while ($row = mysqli_fetch_assoc($products)):
                $p_id = $row['id'];
                $foto_path = "../assets/images/products/" . $row['foto'];
                if (!file_exists($foto_path) || empty($row['foto'])) {
                    $foto_path = "https://via.placeholder.com/400x500/F0F0F0/000000?text=" . urlencode($row['nama_kaos']);
                }
                
                $is_ready = ($row['status_stok'] == 'READY' && $row['stok'] > 0);
        ?>
        <a href="detail_produk.php?id=<?= $p_id ?>" class="product-card">
            <div class="product-img-wrapper">
                <span class="badge-stock <?= $is_ready ? 'ready' : '' ?>">
                    <?= $is_ready ? 'READY' : 'OUT OF STOCK' ?>
                </span>
                <img src="<?= $foto_path ?>" alt="<?= $row['nama_kaos'] ?>" class="product-img">
            </div>
            
            <p style="font-size: 10px; color: #888; text-transform: uppercase; font-weight: bold;"><?= $row['nama_kategori'] ?> | <?= $row['koleksi'] ?></p>
            <div class="product-title"><?= strtoupper($row['nama_kaos']) ?></div>
            <div class="product-price" style="margin-bottom: 8px;">Rp <?= number_format($row['harga'], 0, ',', '.') ?></div>

            <!-- Menampilkan Ukuran Produk yang Tersedia dari Database -->
            <div class="sub-category-list active" style="flex-direction: row; justify-content: center; gap: 5px; margin: 0;">
                <?php
                $q_var = mysqli_query($koneksi, "SELECT * FROM product_variants WHERE product_id=$p_id");
                if (mysqli_num_rows($q_var) > 0):
                    while ($v = mysqli_fetch_assoc($q_var)):
                ?>
                    <span style="border: 1px solid #DDD; padding: 2px 6px; font-size: 10px; font-weight: bold; background: #F9F9F9; border-radius: 3px;">
                        <?= $v['ukuran'] ?> (<?= $v['stok_ukuran'] ?>)
                    </span>
                <?php 
                    endwhile;
                else:
                ?>
                    <span style="border: 1px solid #DDD; padding: 2px 6px; font-size: 10px; background: #F9F9F9; border-radius: 3px;">ALL SIZE</span>
                <?php endif; ?>
            </div>
        </a>
        <?php 
            endwhile;
        else:
            echo "<p style='grid-column: 1/-1; text-align: center; color: #888; padding: 40px;'>Belum ada produk untuk kategori ini.</p>";
        endif;
        ?>
    </div>


<!-- Floating WhatsApp Widget -->
<div class="wa-floating-btn" onclick="toggleWaChat()">
    <span class="wa-badge">4</span>
    <svg width="30" height="30" viewBox="0 0 24 24" fill="white">
        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
    </svg>
</div>

<!-- Kartu WhatsApp Pop-up -->
<div id="waChatBox" class="wa-chat-box">
    <div class="wa-chat-header">
        <div>
            <strong style="font-size: 13px; display: flex; align-items: center; gap: 6px;">
                <span style="height: 8px; width: 8px; background-color: #25D366; border-radius: 50%; display: inline-block;"></span>
                Tim Pendukung LIBRARE
            </strong>
            <small style="color: #888; font-size: 10px;">👋 Online 24/7 • Membalas Cepat</small>
        </div>
        <span onclick="toggleWaChat()" style="cursor: pointer; font-size: 16px; color: #888;">&times;</span>
    </div>

    <div class="wa-chat-body">
        <div class="wa-msg-bubble">👋 Hai! Selamat datang di Toko LIBRARE</div>
        <div class="wa-msg-bubble">
            🚀 <strong>Pengiriman cepat tersedia!</strong><br>
            ⚡ Instan (hari yang sama) | 🇲🇨 Pengiriman nasional
        </div>
        <a href="https://wa.me/6281939218778?s=ttext=Halo%20Admin%20LIBRARE,%20saya%20tertarik%20dengan%20produk%20Anda" target="_blank" class="btn-wa-action">
            ✈️ Mulai Mengobrol di WhatsApp
        </a>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
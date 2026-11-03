<?php require_once __DIR__ . '/../config/koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=1440, initial-scale=1.0">
    <title>LIBRARE</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= time(); ?>">
</head>
<body>
    <div class="desktop-container">
        <header>
            <a href="beranda.php" class="brand-title">LIBRARE</a>
            
            <div class="header-icons">
                <!-- 1. Tombol Pencarian Produk -->
                <button type="button" class="icon-btn" onclick="toggleSearchForm()" style="background:none; border:none; font-size:16px; cursor:pointer;" title="Cari Produk">🔍</button>
                
                <!-- 2. Dropdown Pilih Mata Uang & Negara -->
                <div class="currency-dropdown-wrapper" style="position: relative; display: inline-block;">
                    <button type="button" class="currency-btn" onclick="toggleCurrencyMenu()" style="background:none; border:none; font-weight:bold; cursor:pointer; font-size:12px;">
                        <span id="currentCurrency">🇮🇩 ID IDR</span> ▾
                    </button>
                    
                    <div id="currencyMenu" class="currency-menu" style="display:none; position:absolute; top:30px; right:0; background:#fff; border:1px solid #ccc; box-shadow:0 4px 10px rgba(0,0,0,0.15); border-radius:4px; width:170px; z-index:9999; overflow:hidden;">
                        <div onclick="changeCurrency('IDR', '🇮🇩 ID IDR', 1)" style="padding:10px; font-size:12px; cursor:pointer; border-bottom:1px solid #eee; display:flex; justify-content:space-between;">
                            <span>🇮🇩 Indonesia</span> <strong>IDR</strong>
                        </div>
                        <div onclick="changeCurrency('USD', '🇺🇸 US USD', 0.000063)" style="padding:10px; font-size:12px; cursor:pointer; border-bottom:1px solid #eee; display:flex; justify-content:space-between;">
                            <span>🇺🇸 United States</span> <strong>USD</strong>
                        </div>
                        <div onclick="changeCurrency('SGD', '🇸🇬 SG SGD', 0.000084)" style="padding:10px; font-size:12px; cursor:pointer; border-bottom:1px solid #eee; display:flex; justify-content:space-between;">
                            <span>🇸🇬 Singapore</span> <strong>SGD</strong>
                        </div>
                        <div onclick="changeCurrency('MYR', '🇲🇾 MY MYR', 0.00028)" style="padding:10px; font-size:12px; cursor:pointer; border-bottom:1px solid #eee; display:flex; justify-content:space-between;">
                            <span>🇲🇾 Malaysia</span> <strong>MYR</strong>
                        </div>
                        <div onclick="changeCurrency('EUR', '🇪🇺 EU EUR', 0.000058)" style="padding:10px; font-size:12px; cursor:pointer; display:flex; justify-content:space-between;">
                            <span>🇪🇺 Europe</span> <strong>EUR</strong>
                        </div>
                    </div>
                </div>

                <!-- Tombol Keranjang & Login Staff -->
                <a href="cart.php" style="text-decoration: none; color: black; font-weight: bold; font-size: 16px; margin: 0 5px;" title="Keranjang Belanja">🛍️</a>
                <a href="index.php" style="text-decoration: none; color: black; font-weight: bold; font-size: 12px;">Staff Login</a>
                <button class="nav-toggle-btn" onclick="toggleMenu()">☰</button>
            </div>
        </header>

        <!-- Form Overlay Pencarian Pop-up -->
        <div id="searchBarOverlay" style="display:none; background:#000; padding:15px 30px; position:relative; z-index:998;">
            <form action="beranda.php" method="GET" style="display:flex; gap:10px; max-width:800px; margin:0 auto;">
                <input type="text" name="search" placeholder="Ketik nama baju, hoodie, topi..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" style="flex:1; padding:10px 15px; border:none; border-radius:4px; font-size:14px;" required>
                <button type="submit" class="btn-pink" style="padding:10px 20px; font-weight:bold; cursor:pointer;">CARI</button>
                <button type="button" class="btn-secondary" onclick="toggleSearchForm()" style="padding:10px 15px; cursor:pointer;">BATAL</button>
            </form>
        </div>

        <!-- Menu Navigation Overlay Garis Tiga -->
        <div id="dropdownMenu" class="menu-overlay">
            <div class="nav-link-item" onclick="toggleShopSub()">SHOP ▾</div>
            <div id="shopSubCategory" class="sub-category-list">
                <p style="color: #888; font-size: 11px; margin-top: 5px;">CATEGORY</p>
                <a href="beranda.php?cat=BOTTOMS">BOTTOMS</a>
                <a href="beranda.php?cat=T-SHIRTS">T-SHIRTS</a>
                <a href="beranda.php?cat=SWEATS">SWEATS</a>
                <a href="beranda.php?cat=OUTERWEAR">OUTERWEAR</a>
                <a href="beranda.php?cat=PANTS">PANTS</a>
                <a href="beranda.php?cat=ACCESSORIES">ACCESSORIES</a>
                <a href="beranda.php?cat=SHIRTS">SHIRTS</a>
                <a href="beranda.php?cat=PAKET BAJU CELANA">PAKET BAJU CELANA</a>
                <a href="beranda.php">SHOP ALL</a>

                <p style="color: #888; font-size: 11px; margin-top: 15px;">COLLECTION</p>
                <a href="beranda.php?col=BEYOND MEASURE">BEYOND MEASURE</a>
                <a href="beranda.php?col=DELIVERY SHEET">DELIVERY SHEET</a>
                <a href="beranda.php?col=SURAT JALAN">SURAT JALAN</a>
                <a href="beranda.php?col=BORN TO BLOOM">BORN TO BLOOM</a>
            </div>

            <a href="lookbook.php" class="nav-link-item">LOOKBOOK</a>
            <a href="librare_section.php" class="nav-link-item">LIBRARE SECTION</a>
            <a href="about_us.php" class="nav-link-item">ABOUT US</a>
            <a href="contact.php" class="nav-link-item">CONTACT</a>
        </div>
<?php
require_once '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$selected_month = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');

if (isset($_POST['simpan'])) {
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $nominal  = intval($_POST['nominal']);
    $ket      = mysqli_real_escape_string($koneksi, $_POST['keterangan']);
    $user_id  = $_SESSION['user_id'];
    $tgl_input = !empty($_POST['tanggal']) ? $_POST['tanggal'] : date('Y-m-d');
    $periode   = date('Y-m', strtotime($tgl_input));

    mysqli_query($koneksi, "INSERT INTO expenses (user_id, kategori, nominal, keterangan, periode_bulan, created_at) 
                    VALUES ($user_id, '$kategori', $nominal, '$ket', '$periode', '$tgl_input " . date('H:i:s') . "')");
    header('Location: pengeluaran.php?bulan=' . $periode);
    exit;
}

if (isset($_POST['export_txt'])) {
    $expenses = mysqli_query($koneksi, "SELECT * FROM expenses WHERE periode_bulan='$selected_month' ORDER BY created_at DESC");
    $txt = "LAPORAN PENGELUARAN LIBRARE - BULAN " . date('F Y', strtotime($selected_month . '-01')) . "\n\n";
    while ($e = mysqli_fetch_assoc($expenses)) {
        $txt .= "{$e['created_at']} | {$e['kategori']} | Rp {$e['nominal']} | {$e['keterangan']}\n";
    }
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="Laporan_Pengeluaran_' . $selected_month . '.txt"');
    echo $txt;
    exit;
}
?>

<div class="content-area" style="gap: 20px;">
    <div style="flex: 1; max-width: 200px; background: white; padding: 20px; border-radius: 8px; border: 1px solid var(--border-color);">
        <?php include '../includes/sidebar.php'; ?>
    </div>

    <div style="flex: 4; display: flex; gap: 20px;">
        <div class="form-card" style="flex: 1; margin: 0; max-width: 360px;">
            <h3>INPUT PENGELUARAN</h3>
            <form method="POST" style="margin-top: 15px;">
                <div class="form-group">
                    <label>Tanggal Transaksi:</label>
                    <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="form-group">
                    <label>Kategori Pengeluaran:</label>
                    <select name="kategori" class="form-control" required>
                        <option value="Operasional">Operasional</option>
                        <option value="Sewa Tempat">Sewa Tempat</option>
                        <option value="Kebersihan">Kebersihan</option>
                        <option value="Listrik & Air">Listrik & Air</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nominal (Rp):</label>
                    <input type="number" name="nominal" class="form-control" placeholder="150000" min="1" required>
                </div>
                <div class="form-group">
                    <label>Keterangan:</label>
                    <textarea name="keterangan" class="form-control" rows="3" required></textarea>
                </div>
                <button type="submit" name="simpan" class="btn-pink" style="width: 100%;">SIMPAN PENGELUARAN</button>
            </form>
        </div>

        <div style="flex: 2; background: white; padding: 20px; border-radius: 8px; border: 1px solid var(--border-color);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3>REKAP PENGELUARAN (<?= date('F Y', strtotime($selected_month . '-01')) ?>)</h3>
                <div style="display: flex; gap: 10px;">
                    <form method="GET">
                        <input type="month" name="bulan" value="<?= $selected_month ?>" class="form-control" style="padding: 5px; width: auto;" onchange="this.form.submit()">
                    </form>
                    <button onclick="window.print()" class="btn-pink" style="padding: 6px 12px; font-size: 12px;">🖨️ Cetak</button>
                    <form method="POST">
                        <button type="submit" name="export_txt" class="btn-secondary" style="padding: 6px 12px; font-size: 12px;">💾 Text File</button>
                    </form>
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Keterangan</th>
                        <th>Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $expenses = mysqli_query($koneksi, "SELECT * FROM expenses WHERE periode_bulan='$selected_month' ORDER BY created_at DESC");
                    while ($e = mysqli_fetch_assoc($expenses)):
                    ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($e['created_at'])) ?></td>
                        <td><strong><?= $e['kategori'] ?></strong></td>
                        <td><?= $e['keterangan'] ?></td>
                        <td style="color: red; font-weight: bold;">- Rp <?= number_format($e['nominal'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
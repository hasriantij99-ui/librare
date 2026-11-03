<?php
require_once '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header('Location: index.php');
    exit;
}

$selected_month = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');

$res_in = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(total_bayar) as total FROM transactions WHERE DATE_FORMAT(created_at, '%Y-%m') = '$selected_month'"));
$res_out = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(nominal) as total FROM expenses WHERE periode_bulan = '$selected_month'"));

$in = $res_in['total'] ?? 0;
$out = $res_out['total'] ?? 0;
$laba_bersih = $in - $out;
?>

<div class="content-area" style="gap: 20px;">
    <div style="flex: 1; max-width: 200px; background: white; padding: 20px; border-radius: 8px; border: 1px solid var(--border-color);">
        <?php include '../includes/sidebar.php'; ?>
    </div>

    <div style="flex: 4; display: flex; flex-direction: column; gap: 20px;">
        <div style="background: white; padding: 15px 20px; border-radius: 8px; border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h2>RINGKASAN KEUANGAN OWNER</h2>
            <form method="GET" style="display: flex; align-items: center; gap: 10px;">
                <label style="font-weight: 600;">Periode Bulan:</label>
                <input type="month" name="bulan" value="<?= $selected_month ?>" class="form-control" style="padding: 6px 10px; width: auto;" onchange="this.form.submit()">
            </form>
        </div>

        <div class="metrics">
            <div class="metric-card">
                <p style="color: #666; font-size: 14px;">TOTAL PEMASUKAN</p>
                <h3 style="font-size: 24px; margin-top: 5px; color: green;">Rp <?= number_format($in, 0, ',', '.') ?></h3>
            </div>
            <div class="metric-card">
                <p style="color: #666; font-size: 14px;">TOTAL PENGELUARAN</p>
                <h3 style="font-size: 24px; margin-top: 5px; color: red;">Rp <?= number_format($out, 0, ',', '.') ?></h3>
            </div>
            <div class="metric-card highlight">
                <p style="font-size: 14px;">LABA BERSIH</p>
                <h3 style="font-size: 24px; margin-top: 5px; color: <?= $laba_bersih >= 0 ? 'var(--accent-pink)' : 'red' ?>;">
                    Rp <?= number_format($laba_bersih, 0, ',', '.') ?>
                </h3>
            </div>
        </div>

        <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid var(--border-color);">
            <h3>RIWAYAT ARUS KAS (<?= date('F Y', strtotime($selected_month . '-01')) ?>)</h3>
            <table>
                <thead>
                    <tr>
                        <th>Tanggal & Waktu</th>
                        <th>Keterangan Transaksi</th>
                        <th>Jenis Arus Kas</th>
                        <th>Nominal (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query_kas = mysqli_query($koneksi, "
                        SELECT created_at as tanggal, 'Penjualan POS Kasir' as keterangan, 'Pemasukan' as jenis, total_bayar as nominal 
                        FROM transactions 
                        WHERE DATE_FORMAT(created_at, '%Y-%m') = '$selected_month'
                        UNION ALL
                        SELECT created_at as tanggal, CONCAT(kategori, ' - ', keterangan) as keterangan, 'Pengeluaran' as jenis, nominal 
                        FROM expenses 
                        WHERE periode_bulan = '$selected_month'
                        ORDER BY tanggal DESC
                    ");

                    if (mysqli_num_rows($query_kas) > 0):
                        while ($row = mysqli_fetch_assoc($query_kas)):
                    ?>
                    <tr>
                        <td><?= $row['tanggal'] ?></td>
                        <td><?= $row['keterangan'] ?></td>
                        <td><strong><?= $row['jenis'] ?></strong></td>
                        <td style="color: <?= $row['jenis'] == 'Pemasukan' ? 'green' : 'red' ?>; font-weight: bold;">
                            <?= $row['jenis'] == 'Pemasukan' ? '+' : '-' ?> Rp <?= number_format($row['nominal'], 0, ',', '.') ?>
                        </td>
                    </tr>
                    <?php 
                        endwhile;
                    else:
                        echo "<tr><td colspan='4' style='text-align: center;'>Belum ada riwayat arus kas pada periode bulan ini.</td></tr>";
                    endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
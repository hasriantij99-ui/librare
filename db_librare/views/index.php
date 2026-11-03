<?php
require_once '../config/koneksi.php';

$error = '';
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);
        if ($password == $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'kasir') {
                header('Location: pos.php');
            } else {
                header('Location: owner_dashboard.php');
            }
            exit;
        }
    }
    $error = 'Username atau Password salah!';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>LIBRARE - Login System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="desktop-container" style="justify-content: center; align-items: center;">
        <div class="form-card" style="text-align: center; max-width: 400px;">
            <div style="background: var(--primary-black); color: white; padding: 15px; font-weight: bold; font-size: 24px;">L I B R A R E</div>
            <p style="margin: 15px 0; font-weight: 600;">Sistem POS & Management Toko</p>
            
            <?php if ($error): ?>
                <p style="color: red; margin-bottom: 10px; font-size: 14px;"><?= $error ?></p>
            <?php endif; ?>

            <form method="POST" style="text-align: left;">
                <div class="form-group">
                    <label>Username :</label>
                    <input type="text" name="username" class="form-control" placeholder="owner / kasir" required>
                </div>
                <div class="form-group">
                    <label>Password :</label>
                    <input type="password" name="password" class="form-control" placeholder="admin123" required>
                </div>
                <button type="submit" name="login" class="btn-pink" style="width: 100%; padding: 12px;">MASUK KE SISTEM</button>
            </form>
            <div style="margin-top: 15px;">
                <a href="beranda.php" style="color: #666; font-size: 13px; text-decoration: none;">← Kembali ke Beranda Pelanggan</a>
            </div>
        </div>
    </div>
</body>
</html>
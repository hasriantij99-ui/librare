<?php require_once '../includes/header.php'; ?>

<div style="background-color: #111; color: white; padding: 40px 20px; text-align: center;">
    <p style="font-size: 12px; font-weight: 700; letter-spacing: 2px; color: #888;">GET IN TOUCH</p>
    <h1 style="font-size: 32px; font-weight: 900; letter-spacing: 3px; margin-top: 5px;">CONTACT US</h1>
</div>

<div class="content-area" style="gap: 40px;">
    <div style="flex: 1; background: white; padding: 30px; border-radius: 8px; border: 1px solid var(--border-color);">
        <h3>INFORMASI KONTAK</h3>
        <p style="font-size: 14px; color: #555; margin-top: 15px; line-height: 1.8;">
            📍 <strong>Lokasi Toko:</strong> jl.Angrek<br>
            📞 <strong>WhatsApp CS:</strong> +62 819-3921-8778<br>
            📧 <strong>Email:</strong> support@librare.store<br>
            📸 <strong>Instagram:</strong> @librare.store
        </p>
    </div>

    <div style="flex: 1; background: white; padding: 30px; border-radius: 8px; border: 1px solid var(--border-color);">
        <h3>KIRIM PESAN</h3>
        <form method="POST" action="" style="margin-top: 15px;">
            <div class="form-group">
                <label>Nama Anda:</label>
                <input type="text" class="form-control" placeholder="Nama lengkap..." required>
            </div>
            <div class="form-group">
                <label>Pesan / Pertanyaan:</label>
                <textarea class="form-control" rows="4" placeholder="Tuliskan pesan..." required></textarea>
            </div>
            <button type="button" onclick="alert('Pesan Anda berhasil dikirim!')" class="btn-pink" style="width: 100%;">KIRIM PESAN</button>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
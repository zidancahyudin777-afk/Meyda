<?php
include '../includes/koneksi.php';
session_start();

if(isset($_SESSION['user_id'])) {
    header("Location: ../user/beranda.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = sanitize($_POST['nama']);
    $email = sanitize($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $telepon = sanitize($_POST['telepon']);
    $alamat = sanitize($_POST['alamat']);

    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    
    if($_POST['password'] !== $_POST['konfirmasi_password']){
        $error = "Konfirmasi password tidak sesuai.";
    } elseif($check->get_result()->num_rows > 0){
        $error = "Email sudah terdaftar.";
    } else {
        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, 'user')");
            $stmt->bind_param("sss", $nama, $email, $password);
            $stmt->execute();
            $user_id = $conn->insert_id;

            $stmt2 = $conn->prepare("INSERT INTO pelanggan (user_id, alamat, telepon) VALUES (?, ?, ?)");
            $stmt2->bind_param("iss", $user_id, $alamat, $telepon);
            $stmt2->execute();

            $conn->commit();
            header("Location: masuk.php");
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Terjadi kesalahan sistem.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - MeyDa Collection</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body style="display:flex; align-items:center; justify-content:center; min-height:100vh; padding: 2rem 0;">

    <div class="card slide-up" style="width:100%; max-width:500px; padding:2.5rem; border:none; box-shadow:var(--shadow-lg);">
        <div style="text-align:center; margin-bottom:2rem;">
            <h2 style="color:var(--primary); font-weight:800;">Buat Akun</h2>
            <p style="color:var(--text-muted);">Bergabung dengan MeyDa Collection</p>
        </div>

        <?php if($error): ?>
            <div style="background:#FEE2E2; color:#B91C1C; padding:0.75rem; border-radius:0.5rem; font-size:0.9rem; margin-bottom:1.5rem; text-align:center;">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="grid grid-cols-2" style="gap:1rem;">
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Konfirmasi Password</label>
                    <input type="password" name="konfirmasi_password" class="form-control" required>
                </div>
            </div>
            <div class="grid grid-cols-2" style="gap:1rem;">
                <div class="form-group">
                    <label>No. Telepon</label>
                    <input type="text" name="telepon" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Alamat Singkat</label>
                    <input type="text" name="alamat" class="form-control" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%; margin-top:0.5rem;">Daftar Sekarang</button>
        </form>

        <p style="margin-top:2rem; text-align:center; font-size:0.9rem; color:var(--text-muted);">
            Sudah punya akun? <a href="masuk.php" style="color:var(--primary); font-weight:600;">Login disini</a>
        </p>
    </div>

</body>
</html>

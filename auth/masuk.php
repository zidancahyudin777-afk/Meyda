<?php
include '../includes/koneksi.php';
session_start();

if(isset($_SESSION['user_id'])) {
    if($_SESSION['role'] == 'admin') header("Location: ../admin/beranda.php");
    else header("Location: ../user/beranda.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, nama, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['role'] = $user['role'];
            
            if($user['role'] == 'admin') header("Location: ../admin/beranda.php");
            else header("Location: ../user/beranda.php");
            exit();
        } else {
            $error = "Password yang anda masukkan salah.";
        }
    } else {
        $error = "Email tidak terdaftar.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - MeyDa Collection</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body style="display:flex; align-items:center; justify-content:center; min-height:100vh;">

    <div class="card slide-up" style="width:100%; max-width:400px; padding:2.5rem; border:none; box-shadow:var(--shadow-lg);">
        <div style="text-align:center; margin-bottom:2rem;">
            <h2 style="color:var(--primary); font-weight:800;">MeyDa.</h2>
            <p style="color:var(--text-muted);">Selamat datang kembali</p>
        </div>

        <?php if($error): ?>
            <div style="background:#FEE2E2; color:#B91C1C; padding:0.75rem; border-radius:0.5rem; font-size:0.9rem; margin-bottom:1.5rem; text-align:center;">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="nama@email.com" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%; margin-top:0.5rem;">Masuk Sekarang</button>
        </form>

        <p style="margin-top:2rem; text-align:center; font-size:0.9rem; color:var(--text-muted);">
            Belum punya akun? <a href="daftar.php" style="color:var(--primary); font-weight:600;">Daftar disini</a>
        </p>
        <p style="margin-top:0.5rem; text-align:center; font-size:0.9rem;">
             <a href="../index.php" style="color:var(--text-muted);">Kembali ke Beranda</a>
        </p>
    </div>

</body>
</html>

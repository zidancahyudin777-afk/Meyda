<?php
if(session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MeyDa Collection</title>
    <link rel="stylesheet" href="/meyda/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar glass">
        <div class="container">
            <a href="/meyda/" class="navbar-brand">
                 MeyDa.
            </a>
            <ul class="nav-links">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li style="color:var(--primary); font-weight:600; margin-right:1rem;">
                        Halo, <?= htmlspecialchars($_SESSION['nama']) ?> 👋
                    </li>
                    <?php if($_SESSION['role'] == 'admin'): ?>
                        <li><a href="/meyda/admin/beranda.php">Dashboard</a></li>
                        <li><a href="/meyda/admin/kelola_produk.php">Produk</a></li>
                        <li><a href="/meyda/admin/kelola_kategori.php">Kategori</a></li>
                        <li><a href="/meyda/admin/kelola_pesanan.php">Transaksi</a></li>
                    <?php else: ?>
                        <li><a href="/meyda/user/beranda.php">Jelajahi</a></li>
                        <li><a href="/meyda/user/keranjang.php">Keranjang</a></li>
                        <li><a href="/meyda/user/riwayat.php">Pesanan Saya</a></li>
                    <?php endif; ?>
                    <li><a href="/meyda/auth/keluar.php" class="btn btn-danger btn-sm" style="color:white; border:none;">Keluar</a></li>
                <?php else: ?>
                    <li><a href="/meyda/auth/masuk.php" class="btn btn-secondary btn-sm">Masuk</a></li>
                    <li><a href="/meyda/auth/daftar.php" class="btn btn-primary btn-sm" style="color:white;">Daftar</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    <div class="main-content container fade-in">

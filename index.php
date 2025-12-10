<?php
session_start();
// Jika sudah login, langsung ke beranda user
if(isset($_SESSION['user_id'])){
    if($_SESSION['role'] == 'admin') header("Location: admin/beranda.php");
    else header("Location: user/beranda.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MeyDa Collection</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <nav class="navbar glass">
        <div class="container">
            <a href="#" class="navbar-brand">MeyDa.</a>
            <ul class="nav-links">
                <li><a href="auth/masuk.php">Masuk</a></li>
                <li><a href="auth/daftar.php" class="btn btn-primary btn-sm" style="color:white;">Daftar</a></li>
            </ul>
        </div>
    </nav>

    <div class="hero-section" style="background: linear-gradient(rgba(255,255,255,0.85), rgba(255,255,255,0.95)), url('assets/images/store_hero.jpg'); background-size: cover; background-position: center;">
        <div class="container slide-up">
            <span style="color:var(--primary); font-weight:600; text-transform:uppercase; letter-spacing:2px; font-size:0.9rem;">Fashion Terkini</span>
            <h1 style="font-size: 3.5rem; margin: 1rem 0; line-height: 1.1;">
                Tampil Elegan dengan<br>
                <span style="color:var(--primary);">MeyDa Collection</span>
            </h1>
            <p style="color:var(--text-muted); font-size:1.2rem; max-width:600px; margin:0 auto 2rem;">
                Temukan koleksi pakaian terbaik dengan desain modern dan bahan berkualitas premium. Daftar sekarang untuk mulai berbelanja.
            </p>
            <div style="display:flex; gap:1rem; justify-content:center;">
                <a href="auth/daftar.php" class="btn btn-primary" style="padding: 1rem 2.5rem; font-size:1.1rem;">
                    Belanja Sekarang <i class="fas fa-arrow-right"></i>
                </a>
                <a href="auth/masuk.php" class="btn btn-secondary" style="padding: 1rem 2.5rem; font-size:1.1rem;">
                    Masuk Akun
                </a>
            </div>
        </div>
    </div>

    <script>
        // Navbar Effect on Scroll
        window.addEventListener('scroll', function() {
            const nav = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                nav.classList.add('glass');
                nav.style.background = 'rgba(255, 255, 255, 0.9)';
            } else {
                nav.style.background = 'transparent';
            }
        });
    </script>
</body>
</html>

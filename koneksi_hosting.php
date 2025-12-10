<?php
// FILE INI KHUSUS UNTUK HOSTING
// Silakan upload file ini ke folder 'includes' di hosting (via FileZilla)
// Lalu ganti namanya menjadi 'koneksi.php' di sana.

$host = 'mysql-meyda-project.alwaysdata.net'; 
$user = 'meyda-project';            
$pass = 'zidancah27';       
$db   = 'meyda-project_db'; // Coba 'meyda-project_db' atau 'meyda-project_meyda_db'

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi Hosting Gagal: " . $conn->connect_error);
}

// Function to sanitize input
function sanitize($data) {
    global $conn;
    return htmlspecialchars(strip_tags(trim($conn->real_escape_string($data))));
}

// Format Rupiah
function formatRupiah($angka){
    return "Rp " . number_format($angka,0,',','.');
}
?>

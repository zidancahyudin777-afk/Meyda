<?php
$host = 'localhost';
$user = 'root';
$pass = '';  Default XAMPP password
$db   = 'meyda_collection';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

 Function to sanitize input
function sanitize($data) {
    global $conn;
    return htmlspecialchars(strip_tags(trim($conn->real_escape_string($data))));
}

 Format Rupiah
function formatRupiah($angka){
    return "Rp " . number_format($angka,0,',','.');
}
?>

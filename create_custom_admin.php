<?php
include 'includes/koneksi.php';

$email = 'zidan@admin.com';
$password = 'zidan123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$nama = 'Zidan Admin';

// Check if user exists
$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$result = $check->get_result();

if($result->num_rows > 0){
    // Update existing
    $stmt = $conn->prepare("UPDATE users SET password = ?, role = 'admin', nama = ? WHERE email = ?");
    $stmt->bind_param("sss", $hashed_password, $nama, $email);
    if($stmt->execute()){
        echo "Akun admin $email berhasil diperbarui.";
    } else {
        echo "Gagal memperbarui akun: " . $conn->error;
    }
} else {
    // Create new
    $stmt = $conn->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, 'admin')");
    $stmt->bind_param("sss", $nama, $email, $hashed_password);
    if($stmt->execute()){
        echo "Akun admin $email berhasil dibuat.";
    } else {
        echo "Gagal membuat akun: " . $conn->error;
    }
}
?>

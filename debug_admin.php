<?php
include 'includes/koneksi.php';

$email = 'admin@meyda.test';
$new_pass = 'AdminDemo123!';
$hash = password_hash($new_pass, PASSWORD_DEFAULT);

// Check if admin exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if($res->num_rows > 0){
    // Update existing
    $stmt = $conn->prepare("UPDATE users SET password = ?, role = 'admin' WHERE email = ?");
    $stmt->bind_param("ss", $hash, $email);
    $stmt->execute();
    echo "Admin password updated successfully. Role set to admin.";
} else {
    // Create new
    $nama = "Administrator";
    $stmt = $conn->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, 'admin')");
    $stmt->bind_param("sss", $nama, $email, $hash);
    $stmt->execute();
    echo "Admin account created successfully.";
}
?>

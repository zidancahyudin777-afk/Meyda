<?php
include '..includeskoneksi.php';
header('Content-Type: applicationjson');

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $data = json_decode(file_get_contents("php:input"), true);
    
    if(isset($data['id']) && isset($data['stok'])){
        $id = (int)$data['id'];
        $stok = (int)$data['stok'];
        
        $stmt = $conn->prepare("UPDATE produk SET stok = ? WHERE id = ?");
        $stmt->bind_param("ii", $stok, $id);
        
        if($stmt->execute()){
            echo json_encode(['status' => 'success', 'message' => 'Stok berhasil diupdate']);
        } else {
             echo json_encode(['status' => 'error', 'message' => 'Gagal update database']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
?>

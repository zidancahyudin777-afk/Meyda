<?php
include '../includes/koneksi.php';
include '../includes/cek_akses.php';
requireUser();

if(empty($_SESSION['cart'])){
    header("Location: beranda.php");
    exit();
}

$uid = $_SESSION['user_id'];
$user_info = $conn->query("SELECT u.*, p.alamat, p.telepon FROM users u JOIN pelanggan p ON u.id = p.user_id WHERE u.id = $uid")->fetch_assoc();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $alamat = sanitize($_POST['alamat']);
    $total_bayar = 0;
    
    foreach($_SESSION['cart'] as $id => $qty){
        $p = $conn->query("SELECT harga, stok FROM produk WHERE id=$id")->fetch_assoc();
        if($p['stok'] < $qty){
            echo "<script>alert('Stok tidak mencukupi untuk salah satu produk!'); window.location='keranjang.php';</script>";
            exit();
        }
        $total_bayar += $p['harga'] * $qty;
    }

    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("INSERT INTO transaksi (user_id, total_bayar, alamat_pengiriman) VALUES (?, ?, ?)");
        $stmt->bind_param("ids", $uid, $total_bayar, $alamat);
        $stmt->execute();
        $trx_id = $conn->insert_id;

        foreach($_SESSION['cart'] as $id => $qty){
            $p = $conn->query("SELECT harga FROM produk WHERE id=$id")->fetch_assoc();
            $subtotal = $p['harga'] * $qty;
            
            $dstmt = $conn->prepare("INSERT INTO detail_transaksi (transaksi_id, produk_id, qty, subtotal) VALUES (?, ?, ?, ?)");
            $dstmt->bind_param("iiid", $trx_id, $id, $qty, $subtotal);
            $dstmt->execute();

            $conn->query("UPDATE produk SET stok = stok - $qty WHERE id = $id");
        }

        $conn->commit();
        unset($_SESSION['cart']);
        header("Location: riwayat.php?success=1");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        die("Error: " . $e->getMessage());
    }
}

include '../includes/header.php';
?>

<div class="grid grid-cols-2 slide-up" style="gap:3rem; align-items:start;">
    
    <!-- Bagian Kiri: Form Pengiriman -->
    <div>
        <h2 style="margin-bottom:1.5rem;">Informasi Pengiriman</h2>
        <div class="card" style="box-shadow:none; border:1px solid #E2E8F0;">
            <form method="POST" id="checkoutForm">
                <div class="form-group">
                    <label>Penerima</label>
                    <div style="position:relative;">
                        <i class="fas fa-user" style="position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:var(--text-muted);"></i>
                        <input type="text" value="<?= htmlspecialchars($user_info['nama']) ?>" class="form-control" readonly style="background:#F1F5F9; padding-left:2.5rem;">
                    </div>
                </div>
                <div class="form-group">
                    <label>Nomor Telepon</label>
                    <div style="position:relative;">
                        <i class="fas fa-phone" style="position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:var(--text-muted);"></i>
                        <input type="text" value="<?= htmlspecialchars($user_info['telepon']) ?>" class="form-control" readonly style="background:#F1F5F9; padding-left:2.5rem;">
                    </div>
                </div>
                <div class="form-group">
                    <label>Alamat Pengiriman</label>
                    <textarea name="alamat" class="form-control" rows="4" required style="line-height:1.6;"><?= htmlspecialchars($user_info['alamat']) ?></textarea>
                    <p style="font-size:0.85rem; color:var(--text-muted); margin-top:0.5rem;">Pastikan alamat lengkap untuk memudahkan kurir.</p>
                </div>
            </form>
        </div>
    </div>

    <!-- Bagian Kanan: Ringkasan -->
    <div>
        <h2 style="margin-bottom:1.5rem;">Ringkasan Order</h2>
        <div class="card" style="position:sticky; top:6rem; background:var(--white);">
            <div style="max-height:300px; overflow-y:auto; margin-bottom:1.5rem; padding-right:0.5rem;">
                <?php 
                $total = 0;
                foreach($_SESSION['cart'] as $id => $qty):
                    $p = $conn->query("SELECT * FROM produk WHERE id=$id")->fetch_assoc();
                    $subtotal = $p['harga'] * $qty;
                    $total += $subtotal;
                ?>
                <div style="display:flex; justify-content:space-between; margin-bottom:1rem; padding-bottom:1rem; border-bottom:1px solid #F1F5F9;">
                    <div>
                        <strong><?= htmlspecialchars($p['nama_produk']) ?></strong>
                        <div style="font-size:0.9rem; color:var(--text-muted);"><?= $qty ?> x <?= formatRupiah($p['harga']) ?></div>
                    </div>
                    <div style="font-weight:600;"><?= formatRupiah($subtotal) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div style="border-top:2px dashed #E2E8F0; padding-top:1.5rem; margin-bottom:1.5rem;">
                <div style="display:flex; justify-content:space-between; font-size:1.2rem; font-weight:800; color:var(--primary);">
                    <span>Total Bayar</span>
                    <span><?= formatRupiah($total) ?></span>
                </div>
            </div>

            <button type="submit" form="checkoutForm" class="btn btn-primary" style="width:100%; padding:1rem; font-size:1.1rem;">
                Konfirmasi Pesanan <i class="fas fa-check-circle"></i>
            </button>
            <p style="text-align:center; margin-top:1rem; font-size:0.85rem; color:var(--text-muted);">
                <i class="fas fa-lock"></i> Pembayaran Aman Terjamin
            </p>
        </div>
    </div>

</div>

<?php include '../includes/footer.php'; ?>

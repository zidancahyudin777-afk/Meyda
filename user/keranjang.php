<?php
include '../includes/koneksi.php';
session_start();

// Handle Cart Actions
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

    if($_POST['action'] == 'add'){
        $id = (int)$_POST['product_id'];
        if(isset($_SESSION['cart'][$id])) $_SESSION['cart'][$id]++;
        else $_SESSION['cart'][$id] = 1;
        header("Location: keranjang.php");
        exit();
    } elseif($_POST['action'] == 'update'){
        foreach($_POST['qty'] as $id => $qty){
            if($qty > 0) $_SESSION['cart'][$id] = $qty;
            else unset($_SESSION['cart'][$id]);
        }
    } elseif($_POST['action'] == 'delete'){
        unset($_SESSION['cart'][(int)$_POST['id']]);
    }
}

include '../includes/header.php';
?>

<h2 class="slide-up">Keranjang Belanja</h2>

<?php if(empty($_SESSION['cart'])): ?>
    <div class="card slide-up" style="text-align:center; padding:5rem 2rem;">
        <i class="fas fa-shopping-bag" style="font-size:4rem; color:#E2E8F0; margin-bottom:1rem;"></i>
        <h3 style="color:var(--text-muted);">Keranjangmu kosong</h3>
        <p style="margin-bottom:2rem;">Yuk isi dengan pakaian impianmu!</p>
        <a href="beranda.php" class="btn btn-primary">Mulai Belanja</a>
    </div>
<?php else: ?>
    <div class="card slide-up" style="padding:0; overflow:hidden;">
        <form method="POST">
            <input type="hidden" name="action" value="update">
            <table style="width:100%; border-collapse:collapse;">
                <thead style="background:#F8FAFC; border-bottom:1px solid #E2E8F0;">
                    <tr style="text-align:left;">
                        <th style="padding:1.5rem;">Produk</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total = 0;
                    foreach($_SESSION['cart'] as $id => $qty):
                        $p = $conn->query("SELECT * FROM produk WHERE id=$id")->fetch_assoc();
                        if(!$p) continue;
                        $subtotal = $p['harga'] * $qty;
                        $total += $subtotal;
                    ?>
                    <tr style="border-bottom:1px solid #F1F5F9;">
                        <td style="padding:1.5rem; display:flex; align-items:center; gap:1rem;">
                            <img src="../assets/images/<?= htmlspecialchars($p['gambar']) ?>" style="width:60px; height:60px; object-fit:cover; border-radius:0.5rem;">
                            <div>
                                <strong style="display:block;"><?= htmlspecialchars($p['nama_produk']) ?></strong>
                                <span style="font-size:0.85rem; color:var(--text-muted);">ID: #<?= $p['id'] ?></span>
                            </div>
                        </td>
                        <td><?= formatRupiah($p['harga']) ?></td>
                        <td>
                            <input type="number" name="qty[<?= $id ?>]" value="<?= $qty ?>" min="1" class="form-control" style="width:80px;">
                        </td>
                        <td style="font-weight:600; color:var(--primary);"><?= formatRupiah($subtotal) ?></td>
                        <td>
                            <button type="button" onclick="document.getElementById('delForm<?= $id ?>').submit()" class="btn btn-danger btn-sm" style="border-radius:0.5rem;"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div style="background:#F8FAFC; padding:2rem; text-align:right;">
                <div style="display:inline-block; text-align:right; margin-bottom:1.5rem;">
                    <span style="color:var(--text-muted); margin-right:1rem;">Total Pembayaran</span>
                    <h2 style="color:var(--primary); display:inline;"><?= formatRupiah($total) ?></h2>
                </div>
                <div style="display:flex; gap:1rem; justify-content:flex-end;">
                    <button type="submit" class="btn btn-secondary">Update Keranjang</button>
                    <a href="kasir.php" class="btn btn-primary">
                        Checkout Sekarang <i class="fas fa-arrow-right" style="margin-left:0.5rem;"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
    
    <?php foreach($_SESSION['cart'] as $id => $qty): ?>
    <form method="POST" id="delForm<?= $id ?>" style="display:none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" value="<?= $id ?>">
    </form>
    <?php endforeach; ?>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>

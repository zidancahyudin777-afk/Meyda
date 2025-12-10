<?php
include '../includes/koneksi.php';
include '../includes/cek_akses.php';
requireAdmin();

// Handle Status Update
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_status'){
    $id = (int)$_POST['id'];
    $status = sanitize($_POST['status']);
    if(in_array($status, ['pending', 'dikirim', 'selesai', 'dibatalkan'])){
        $conn->query("UPDATE transaksi SET status = '$status' WHERE id = $id");
    }
    header("Location: kelola_pesanan.php");
    exit();
}

include '../includes/header.php';

$filter_tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';
$where = "";
if($filter_tanggal){
    $where = "WHERE DATE(t.tanggal) = '$filter_tanggal'";
}

$query = "SELECT t.*, u.nama as nama_pelanggan FROM transaksi t JOIN users u ON t.user_id = u.id $where ORDER BY t.tanggal DESC";
$transaksi = $conn->query($query);
?>

<div class="slide-up">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
        <div>
            <h2 style="font-weight:800; margin-bottom:0.25rem;">Daftar Pesanan</h2>
            <p style="color:var(--text-muted);">Pantau status transaksi pelanggan</p>
        </div>
    </div>

    <div class="card" style="margin-bottom:2rem; border:1px solid #E2E8F0; padding:1.5rem;">
        <form method="GET" style="display:flex; gap:1rem; align-items:flex-end;">
            <div class="form-group" style="margin-bottom:0; flex:1;">
                <label>Filter Tanggal</label>
                <input type="date" name="tanggal" class="form-control" value="<?= $filter_tanggal ?>">
            </div>
            <button class="btn btn-primary">Terapkan Filter</button>
            <a href="kelola_pesanan.php" class="btn btn-secondary">Reset</a>
        </form>
    </div>

    <div class="grid grid-cols-1" style="gap:1.5rem;">
        <?php while($row = $transaksi->fetch_assoc()): ?>
        <div class="card" style="border:1px solid #E2E8F0; padding:0; overflow:hidden;">
            <div style="background:#F8FAFC; padding:1rem 1.5rem; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #E2E8F0;">
                <div>
                    <span style="font-weight:700;">#TRX-<?= $row['id'] ?></span>
                    <span style="color:var(--text-muted); margin:0 0.5rem;">•</span>
                    <span style="color:var(--text-muted);"><i class="far fa-user"></i> <?= htmlspecialchars($row['nama_pelanggan']) ?></span>
                </div>
                <div style="font-size:0.9rem; color:var(--text-muted);">
                    <i class="far fa-clock"></i> <?= date('d M Y H:i', strtotime($row['tanggal'])) ?>
                </div>
            </div>
            <div style="padding:1.5rem; display:flex; justify-content:space-between; align-items:flex-start;">
                <div style="flex:1;">
                    <h5 style="margin-bottom:0.5rem; color:var(--text-muted); font-size:0.85rem; text-transform:uppercase;">Item Dipesan</h5>
                    <ul style="list-style:none; padding:0;">
                         <?php
                        $tid = $row['id'];
                        $details = $conn->query("SELECT dt.*, p.nama_produk FROM detail_transaksi dt JOIN produk p ON dt.produk_id = p.id WHERE dt.transaksi_id = $tid");
                        while($d = $details->fetch_assoc()):
                        ?>
                        <li style="margin-bottom:0.25rem;">
                            <strong><?= $d['qty'] ?>x</strong> <?= $d['nama_produk'] ?>
                            <span style="color:var(--text-muted);">@ <?= formatRupiah($d['subtotal']/$d['qty']) ?></span>
                        </li>
                        <?php endwhile; ?>
                    </ul>
                    <div style="margin-top:1rem;">
                        <span style="font-size:0.85rem; color:var(--text-muted);">Alamat:</span><br>
                        <?= htmlspecialchars($row['alamat_pengiriman']) ?>
                    </div>
                </div>
                
                <div style="text-align:right;">
                    <div style="margin-bottom:1rem;">
                        <span style="padding:0.25rem 0.75rem; border-radius:1rem; font-size:0.85rem; font-weight:600; background:<?= $row['status'] == 'selesai' ? '#D1FAE5; color:#065F46' : ($row['status'] == 'pending' ? '#FEF3C7; color:#92400E' : ($row['status'] == 'dikirim' ? '#DBEAFE; color:#1E40AF' : '#FEE2E2; color:#B91C1C')) ?>">
                            <?= strtoupper($row['status']) ?>
                        </span>
                    </div>
                    <h3 style="color:var(--primary); font-weight:800; margin-bottom:1rem;"><?= formatRupiah($row['total_bayar']) ?></h3>
                    
                    <form method="POST">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
                            <?php if($row['status'] == 'pending'): ?>
                                <button name="status" value="dikirim" class="btn btn-sm btn-primary">Kirim Pesanan</button>
                                <button name="status" value="dibatalkan" class="btn btn-sm btn-danger">Tolak</button>
                            <?php elseif($row['status'] == 'dikirim'): ?>
                                <button name="status" value="selesai" class="btn btn-sm btn-success">Selesai</button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

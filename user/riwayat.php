<?php
include '../includes/koneksi.php';
include '../includes/cek_akses.php';
requireUser();
include '../includes/header.php';
$uid = $_SESSION['user_id'];
$transaksi = $conn->query("SELECT * FROM transaksi WHERE user_id = $uid ORDER BY tanggal DESC");
?>

<?php if(isset($_GET['success'])): ?>
<!-- Elegant Success Banner -->
<div class="card slide-up" style="background:var(--primary); color:white; text-align:center; padding:2rem; margin-bottom:2rem; border:none; box-shadow:0 10px 25px -5px rgba(37, 99, 235, 0.4);">
    <div style="width:60px; height:60px; background:white; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1rem;">
        <i class="fas fa-check" style="font-size:1.5rem; color:var(--primary);"></i>
    </div>
    <h3 style="color:white; font-weight:700;">Pesanan Berhasil Dibuat</h3>
    <p style="color:rgba(255,255,255,0.9);">Terima kasih telah berbelanja di MeyDa Collection. Kami akan segera memproses pesanan Anda.</p>
</div>
<?php endif; ?>

<h2 class="slide-up">Riwayat Pesanan Saya</h2>

<div class="grid grid-cols-1 slide-up">
    <?php while($row = $transaksi->fetch_assoc()): ?>
    <div class="card" style="border-left:4px solid var(--primary);">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1rem; padding-bottom:1rem; border-bottom:1px solid #F1F5F9;">
            <div>
                <span style="font-size:0.85rem; color:var(--text-muted); text-transform:uppercase;">Order ID</span>
                <div style="font-weight:700; font-size:1.1rem;">#TRX-<?= $row['id'] ?></div>
                <div style="font-size:0.9rem; color:var(--text-muted); margin-top:0.25rem;">
                    <i class="far fa-calendar"></i> <?= date('d M Y, H:i', strtotime($row['tanggal'])) ?>
                </div>
            </div>
            <div style="text-align:right;">
                <span style="padding:0.25rem 0.75rem; border-radius:1rem; font-size:0.8rem; font-weight:600; background:<?= $row['status'] == 'selesai' ? '#D1FAE5; color:#065F46' : ($row['status'] == 'pending' ? '#FEF3C7; color:#92400E' : '#FEE2E2; color:#B91C1C') ?>">
                    <?= strtoupper($row['status']) ?>
                </span>
                <div style="font-weight:700; color:var(--primary); font-size:1.2rem; margin-top:0.5rem;">
                    <?= formatRupiah($row['total_bayar']) ?>
                </div>
            </div>
        </div>
        <div>
            <h5 style="margin-bottom:0.5rem; color:var(--text-muted);">Detail Item:</h5>
             <ul style="list-style:none; padding:0;">
                <?php
                $tid = $row['id'];
                $details = $conn->query("SELECT p.nama_produk, dt.qty, p.gambar FROM detail_transaksi dt JOIN produk p ON dt.produk_id = p.id WHERE dt.transaksi_id = $tid");
                while($d = $details->fetch_assoc()):
                ?>
                <li style="display:flex; align-items:center; gap:1rem; margin-bottom:0.75rem;">
                    <img src="../assets/images/<?= htmlspecialchars($d['gambar']) ?>" style="width:40px; height:40px; border-radius:0.3rem; object-fit:cover;">
                    <div>
                        <div style="font-weight:500;"><?= $d['nama_produk'] ?></div>
                        <div style="font-size:0.85rem; color:var(--text-muted);">Jumlah: <?= $d['qty'] ?> pcs</div>
                    </div>
                </li>
                <?php endwhile; ?>
             </ul>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<?php include '../includes/footer.php'; ?>

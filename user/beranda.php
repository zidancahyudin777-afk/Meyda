<?php
include '../includes/koneksi.php';
include '../includes/header.php';

$where = "1=1";
if(isset($_GET['kategori']) && $_GET['kategori'] != ''){
    $k = (int)$_GET['kategori'];
    $where .= " AND kategori_id = $k";
}
if(isset($_GET['search']) && $_GET['search'] != ''){
    $s = $conn->real_escape_string($_GET['search']);
    $where .= " AND nama_produk LIKE '%$s%'";
}

$produk = $conn->query("SELECT * FROM produk WHERE $where ORDER BY id DESC");
$kategoris = $conn->query("SELECT * FROM kategori_produk");
?>

<div style="margin-bottom:3rem; text-align:center;" class="slide-up">
    <h2>Koleksi Terbaru</h2>
    <p style="color:var(--text-muted);">Temukan gaya terbaikmu hari ini</p>
</div>

<div class="card glass slide-up" style="margin-bottom:2rem; padding:1.5rem; border:1px solid rgba(255,255,255,0.6);">
    <form method="GET" style="display:flex; gap:1rem; flex-wrap:wrap;">
        <div style="flex:1; position:relative;">
            <i class="fas fa-search" style="position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:var(--text-muted);"></i>
            <input type="text" name="search" class="form-control" placeholder="Cari baju, gamis..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" style="padding-left:2.5rem; background:white;">
        </div>
        <select name="kategori" class="form-control" style="width:200px; background:white; cursor:pointer;">
            <option value="">Semua Kategori</option>
            <?php while($k = $kategoris->fetch_assoc()): ?>
                <option value="<?= $k['id'] ?>" <?= (isset($_GET['kategori']) && $_GET['kategori'] == $k['id']) ? 'selected' : '' ?>><?= $k['nama_kategori'] ?></option>
            <?php endwhile; ?>
        </select>
        <button class="btn btn-primary" style="padding:0 2rem;">Cari</button>
    </form>
</div>

<div class="grid grid-cols-4 product-grid slide-up" style="animation-delay: 0.2s;">
    <?php if($produk->num_rows > 0): ?>
        <?php while($p = $produk->fetch_assoc()): ?>
        <div class="card product-card" style="padding:0; border:none; overflow:hidden;">
            <div style="position:relative;">
                <img src="../assets/images/<?= htmlspecialchars($p['gambar']) ?>" alt="<?= htmlspecialchars($p['nama_produk']) ?>">
                <?php if($p['stok'] == 0): ?>
                    <div style="position:absolute; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); display:flex; align-items:center; justify-content:center; color:white; font-weight:bold;">HABIS</div>
                <?php endif; ?>
            </div>
            <div class="product-info">
                <span style="font-size:0.8rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px;">MeyDa Collection</span>
                <h4 style="margin:0.25rem 0 0.5rem; font-size:1.1rem;"><?= htmlspecialchars($p['nama_produk']) ?></h4>
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <p style="color:var(--primary); font-weight:700; font-size:1.2rem; margin:0;"><?= formatRupiah($p['harga']) ?></p>
                    <?php if($p['stok'] > 0): ?>
                    <form action="keranjang.php" method="POST">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                        <button class="btn btn-primary btn-sm" style="border-radius:50%; width:40px; height:40px; padding:0; display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-plus"></i>
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div style="grid-column: 1 / -1; text-align:center; padding:4rem;">
            <i class="fas fa-box-open" style="font-size:3rem; color:var(--text-muted); margin-bottom:1rem;"></i>
            <p>Produk tidak ditemukan.</p>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>

<?php
include '../includes/koneksi.php';
include '../includes/cek_akses.php';
requireAdmin();

$msg = '';
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if($_POST['action'] == 'add'){
        $nama = sanitize($_POST['nama_kategori']);
        if($conn->query("INSERT INTO kategori_produk (nama_kategori) VALUES ('$nama')")){
            $msg = "Kategori berhasil ditambahkan.";
        } else {
            $msg = "Gagal menambah kategori.";
        }
    } elseif($_POST['action'] == 'delete'){
        $id = (int)$_POST['id'];
        // Cek jika kategori dipakai
        $check = $conn->query("SELECT id FROM produk WHERE kategori_id = $id");
        if($check->num_rows > 0){
             $msg = "Gagal: Kategori sedang digunakan oleh produk lain.";
        } else {
            $conn->query("DELETE FROM kategori_produk WHERE id=$id");
            $msg = "Kategori dihapus.";
        }
    }
}

include '../includes/header.php';
$kategoris = $conn->query("SELECT * FROM kategori_produk");
?>

<div class="slide-up">
    <div style="margin-bottom:2rem;">
        <h2 style="font-weight:800;">Kelola Kategori</h2>
        <p style="color:var(--text-muted);">Tambah atau hapus kategori produk</p>
    </div>

    <?php if($msg): ?>
        <div class="card" style="background:#ECFDF5; color:#065F46; padding:1rem; border:1px solid #10B981; margin-bottom:1.5rem;">
            <i class="fas fa-check-circle"></i> <?= $msg ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-2" style="gap:2rem;">
        <!-- Form Tambah -->
        <div class="card" style="height:fit-content;">
            <h4 style="margin-bottom:1rem;">Tambah Kategori Baru</h4>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label>Nama Kategori</label>
                    <input type="text" name="nama_kategori" class="form-control" placeholder="Contoh: Gamis Syari" required>
                </div>
                <button class="btn btn-primary" style="width:100%;">Simpan</button>
            </form>
        </div>

        <!-- List Kategori -->
        <div class="card" style="padding:0; overflow:hidden;">
            <table style="width:100%; border-collapse:collapse;">
                <thead style="background:#F8FAFC; border-bottom:1px solid #E2E8F0;">
                    <tr style="text-align:left;">
                        <th style="padding:1rem;">Nama Kategori</th>
                        <th style="padding:1rem; text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $kategoris->fetch_assoc()): ?>
                    <tr style="border-bottom:1px solid #F1F5F9;">
                        <td style="padding:1rem; font-weight:600;"><?= htmlspecialchars($row['nama_kategori']) ?></td>
                        <td style="padding:1rem; text-align:right;">
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Hapus kategori ini?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<?php
include '../includes/koneksi.php';
include '../includes/cek_akses.php';
requireAdmin();

$msg = '';
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_POST['action'])){
        if($_POST['action'] == 'add' || $_POST['action'] == 'update'){
            $nama = sanitize($_POST['nama_produk']);
            $kategori = (int)$_POST['kategori_id'];
            $harga = (float)$_POST['harga'];
            $stok = (int)$_POST['stok'];
            $deskripsi = sanitize($_POST['deskripsi']);
            
            $gambar_sql = "";
            $param_types = "siisi";
            $params = [$nama, $kategori, $harga, $stok, $deskripsi];

             if(!empty($_FILES['gambar']['name'])){
                $target_dir = "../assets/images/";
                $ext = strtolower(pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION));
                $new_name = uniqid() . '.' . $ext;
                $target_file = $target_dir . $new_name;
                $check = getimagesize($_FILES["gambar"]["tmp_name"]);
                
                if($check !== false && $_FILES["gambar"]["size"] < 2000000 && in_array($ext, ['jpg','jpeg','png'])){
                    if(move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)){
                         if($_POST['action'] == 'add'){
                             $gambar_sql = ", gambar";
                             $param_types .= "s";
                             $params[] = $new_name;
                         } else {
                            $extra_sql = ", gambar = '$new_name'"; // Simple handle
                         }
                    } else {
                        $msg = "Gagal upload gambar.";
                    }
                } else {
                    $msg = "Format/Ukuran gambar tidak sesuai.";
                }
            } else {
                if($_POST['action'] == 'add') {
                     $gambar_sql = ", gambar";
                     $param_types .= "s";
                     $params[] = 'default.jpg';
                }
            }

            if(!$msg){
                if($_POST['action'] == 'add'){
                    $sql = "INSERT INTO produk (nama_produk, kategori_id, harga, stok, deskripsi $gambar_sql) VALUES (?, ?, ?, ?, ? " . ($gambar_sql ? ",?" : "") . ")";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param($param_types, ...$params);
                    if($stmt->execute()) $msg = "Produk berhasil ditambah.";
                    else $msg = "Gagal menambah: " . $conn->error;
                } else {
                    $id = (int)$_POST['id'];
                    $extra_koma = isset($extra_sql) ? $extra_sql : "";
                    $stmt = $conn->prepare("UPDATE produk SET nama_produk=?, kategori_id=?, harga=?, stok=?, deskripsi=? $extra_koma WHERE id=?");
                    $stmt->bind_param("siisi", $nama, $kategori, $harga, $stok, $deskripsi, $id);
                    if($stmt->execute()) $msg = "Produk berhasil diupdate.";
                    else $msg = "Gagal update.";
                }
            }
        } elseif($_POST['action'] == 'delete'){
            $id = (int)$_POST['id'];
            $conn->query("DELETE FROM produk WHERE id=$id");
            $msg = "Produk dihapus.";
        }
    }
}

include '../includes/header.php';

$kategoris = $conn->query("SELECT * FROM kategori_produk");
$produks = $conn->query("SELECT p.*, k.nama_kategori FROM produk p JOIN kategori_produk k ON p.kategori_id = k.id ORDER BY p.id DESC");
?>

<div class="slide-up">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
        <div>
            <h2 style="font-weight:800; margin-bottom:0.25rem;">Inventaris Produk</h2>
            <p style="color:var(--text-muted);">Kelola katalog barang toko anda</p>
        </div>
        <button class="btn btn-primary" onclick="showModal('addModal')"><i class="fas fa-plus"></i> Tambah Produk</button>
    </div>

    <?php if($msg): ?>
        <div class="card" style="background:#ECFDF5; color:#065F46; padding:1rem; border:1px solid #10B981; margin-bottom:1.5rem;">
            <i class="fas fa-check-circle"></i> <?= $msg ?>
        </div>
    <?php endif; ?>

    <div class="card" style="padding:0; overflow:hidden; border:1px solid #E2E8F0;">
        <table style="width:100%; border-collapse:collapse;">
            <thead style="background:#F8FAFC; border-bottom:1px solid #E2E8F0;">
                <tr style="text-align:left; color:var(--text-muted); font-size:0.85rem; text-transform:uppercase; letter-spacing:1px;">
                    <th style="padding:1rem 1.5rem;">Produk info</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th style="text-align:right; padding-right:1.5rem;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $produks->fetch_assoc()): ?>
                <tr style="border-bottom:1px solid #F1F5F9;">
                    <td style="padding:1rem 1.5rem; display:flex; align-items:center; gap:1rem;">
                        <img src="../assets/images/<?= htmlspecialchars($row['gambar']) ?>" style="width:48px; height:48px; object-fit:cover; border-radius:0.5rem;">
                        <div>
                            <div style="font-weight:600;"><?= htmlspecialchars($row['nama_produk']) ?></div>
                            <div style="font-size:0.8rem; color:var(--text-muted);">ID: <?= $row['id'] ?></div>
                        </div>
                    </td>
                    <td><span style="background:#EFF6FF; color:var(--primary); padding:0.25rem 0.75rem; border-radius:1rem; font-size:0.8rem; font-weight:600;"><?= htmlspecialchars($row['nama_kategori']) ?></span></td>
                    <td><?= formatRupiah($row['harga']) ?></td>
                    <td>
                        <div style="font-weight:600; <?= $row['stok'] < 10 ? 'color:#EF4444;' : '' ?>">
                            <?= $row['stok'] ?> Unit
                        </div>
                    </td>
                    <td style="text-align:right; padding-right:1.5rem;">
                        <button class="btn btn-secondary btn-sm" onclick='editProduct(<?= json_encode($row) ?>)' style="margin-right:0.5rem;"><i class="fas fa-edit"></i></button>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Hapus produk ini?')">
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

<!-- Modal Styles Overlay -->
<style>
    .modal-overlay { position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.6); z-index:9999; display:none; align-items:center; justify-content:center; backdrop-filter:blur(4px); }
    .modal-content { background:white; padding:2rem; border-radius:1rem; width:500px; max-width:90%; box-shadow:var(--shadow-lg); animation:slideUp 0.3s forwards; }
</style>

<!-- Add Modal -->
<div id="addModal" class="modal-overlay">
    <div class="modal-content">
        <h3 style="margin-bottom:1.5rem;">Tambah Produk Baru</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add">
            <div class="form-group">
                <label>Nama Produk</label>
                <input type="text" name="nama_produk" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Kategori</label>
                <select name="kategori_id" class="form-control" required>
                    <?php 
                    $kategoris->data_seek(0);
                    while($k = $kategoris->fetch_assoc()): ?>
                        <option value="<?= $k['id'] ?>"><?= $k['nama_kategori'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="grid grid-cols-2" style="gap:1rem;">
                <div class="form-group">
                    <label>Harga (Rp)</label>
                    <input type="number" name="harga" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Stok Awal</label>
                    <input type="number" name="stok" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <label>Gambar Produk</label>
                <input type="file" name="gambar" class="form-control" accept="image/*">
            </div>
            <div class="form-group">
                <label>Deskripsi Singkat</label>
                <textarea name="deskripsi" class="form-control" rows="2"></textarea>
            </div>
            <div style="text-align:right; margin-top:2rem; display:flex; justify-content:flex-end; gap:1rem;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addModal')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Produk</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal-overlay">
    <div class="modal-content">
        <h3 style="margin-bottom:1.5rem;">Edit Produk</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit_id">
            <div class="form-group">
                <label>Nama Produk</label>
                <input type="text" name="nama_produk" id="edit_nama" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Kategori</label>
                <select name="kategori_id" id="edit_kategori" class="form-control" required>
                    <?php 
                    $kategoris->data_seek(0);
                    while($k = $kategoris->fetch_assoc()): ?>
                        <option value="<?= $k['id'] ?>"><?= $k['nama_kategori'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="grid grid-cols-2" style="gap:1rem;">
                <div class="form-group">
                    <label>Harga</label>
                    <input type="number" name="harga" id="edit_harga" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Stok</label>
                    <input type="number" name="stok" id="edit_stok" class="form-control" required>
                </div>
            </div>
             <div class="form-group">
                <label>Ganti Gambar (Opsional)</label>
                <input type="file" name="gambar" class="form-control" accept="image/*">
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" id="edit_deskripsi" class="form-control" rows="2"></textarea>
            </div>
            <div style="text-align:right; margin-top:2rem; display:flex; justify-content:flex-end; gap:1rem;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function showModal(id){ document.getElementById(id).style.display = 'flex'; }
function closeModal(id){ document.getElementById(id).style.display = 'none'; }
function editProduct(data){
    document.getElementById('edit_id').value = data.id;
    document.getElementById('edit_nama').value = data.nama_produk;
    document.getElementById('edit_kategori').value = data.kategori_id;
    document.getElementById('edit_harga').value = parseInt(data.harga);
    document.getElementById('edit_stok').value = data.stok;
    document.getElementById('edit_deskripsi').value = data.deskripsi;
    showModal('editModal');
}
// Close modal on outside click
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', (e) => {
        if(e.target === overlay) overlay.style.display = 'none';
    });
});
</script>

<?php include '../includes/footer.php'; ?>

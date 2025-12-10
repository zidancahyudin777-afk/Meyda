<?php
include '../includes/koneksi.php';
include '../includes/cek_akses.php';
requireAdmin();
include '../includes/header.php';

// Stats
$total_produk = $conn->query("SELECT COUNT(*) as c FROM produk")->fetch_assoc()['c'];
$hari_ini = date('Y-m-d');
$transaksi_hari_ini = $conn->query("SELECT COUNT(*) as c FROM transaksi WHERE DATE(tanggal) = '$hari_ini'")->fetch_assoc()['c'];
$pendapatan_hari_ini = $conn->query("SELECT SUM(total_bayar) as s FROM transaksi WHERE DATE(tanggal) = '$hari_ini'")->fetch_assoc()['s'] ?? 0;

// Chart Data (Last 7 days)
$labels = [];
$data = [];
for($i=6; $i>=0; $i--){
    $d = date('Y-m-d', strtotime("-$i days"));
    $labels[] = date('d M', strtotime($d));
    $val = $conn->query("SELECT SUM(total_bayar) as s FROM transaksi WHERE DATE(tanggal) = '$d'")->fetch_assoc()['s'] ?? 0;
    $data[] = $val;
}
?>

<div class="slide-up">
    <div style="margin-bottom:2rem;">
        <h2 style="font-weight:800;">Dashboard Admin</h2>
        <p style="color:var(--text-muted);">Ringkasan aktivitas toko hari ini</p>
    </div>

    <div class="grid grid-cols-3">
        <div class="card glass" style="border:none; background:linear-gradient(135deg, #2563EB 0%, #1E40AF 100%); color:white;">
            <div style="font-size:0.9rem; opacity:0.8;">Total Produk</div>
            <div style="font-size:2.5rem; font-weight:700; margin:0.5rem 0;"><?= $total_produk ?></div>
            <div style="font-size:0.8rem; opacity:0.8;">Item tersedia di katalog</div>
        </div>
        <div class="card" style="border:1px solid #E2E8F0;">
            <div style="font-size:0.9rem; color:var(--text-muted);">Transaksi Hari Ini</div>
            <div style="font-size:2.5rem; font-weight:700; color:var(--text-main); margin:0.5rem 0;"><?= $transaksi_hari_ini ?></div>
            <div style="font-size:0.8rem; color:#10B981; font-weight:600;"><i class="fas fa-arrow-up"></i> Order baru</div>
        </div>
        <div class="card" style="border:1px solid #E2E8F0;">
            <div style="font-size:0.9rem; color:var(--text-muted);">Pendapatan Hari Ini</div>
            <div style="font-size:2.5rem; font-weight:700; color:var(--primary); margin:0.5rem 0;"><?= formatRupiah($pendapatan_hari_ini) ?></div>
            <div style="font-size:0.8rem; color:var(--text-muted);">Total omset masuk</div>
        </div>
    </div>

    <div class="card" style="margin-top: 2rem; border:1px solid #E2E8F0;">
        <div style="display:flex; justify-content:space-between; margin-bottom:1.5rem;">
            <h3 style="font-size:1.2rem;">Analitik Pendapatan</h3>
            <select class="form-control" style="width:auto; padding:0.25rem 1rem; font-size:0.9rem;">
                <option>7 Hari Terakhir</option>
            </select>
        </div>
        <canvas id="revenueChart" style="max-height:300px;"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('revenueChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            label: 'Pendapatan',
            data: <?= json_encode($data) ?>,
            borderColor: '#2563EB',
            backgroundColor: 'rgba(37, 99, 235, 0.05)',
            borderWidth: 2,
            pointBackgroundColor: '#2563EB',
            pointRadius: 4,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1E293B',
                padding: 12,
                cornerRadius: 8,
                displayColors: false,
                callbacks: {
                    label: function(context) {
                        return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { borderDash: [4, 4], color: '#F1F5F9' },
                ticks: { display: false }
            },
            x: {
                grid: { display: false },
                ticks: { color: '#64748B' }
            }
        }
    }
});
</script>

<?php include '../includes/footer.php'; ?>

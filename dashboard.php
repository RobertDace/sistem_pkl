<?php
// Memanggil header.
include 'templates/header.php';

// Proteksi halaman
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Ambil data dari session
$role = $_SESSION['role'];
$nama_user = $_SESSION['nama_lengkap'];
?>

<div class="row">
    <div class="col-md-3">
        <?php include 'templates/sidebar.php'; ?>
    </div>
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h4>Dashboard</h4>
            </div>
            <div class="card-body">
                <h5 class="card-title">Selamat Datang, <?php echo htmlspecialchars($nama_user); ?>!</h5>
                <p class="card-text">
                    Anda login sebagai: <strong><?php echo htmlspecialchars(ucfirst($role)); ?></strong>.
                </p>
                <hr>

                <?php
                // =======================================================
                // KONTEN DINAMIS BERDASARKAN ROLE
                // =======================================================
                if ($role == 'admin') {
                    echo "<h5>Ringkasan Status PKL Mahasiswa</h5>";
                    echo '<div style="width: 75%; margin: auto;"><canvas id="pklStatusChart"></canvas></div>';
                
                } elseif ($role == 'mahasiswa') {
                    $sql_cek_pkl = "SELECT status FROM pkl WHERE mahasiswa_user_id = ?";
                    $stmt_cek = mysqli_prepare($koneksi, $sql_cek_pkl);
                    mysqli_stmt_bind_param($stmt_cek, "i", $_SESSION['user_id']);
                    mysqli_stmt_execute($stmt_cek);
                    $result_cek = mysqli_stmt_get_result($stmt_cek);
                    $data_pkl = mysqli_fetch_assoc($result_cek);
                    echo "<h5>Status PKL Anda</h5>";
                    if ($data_pkl) {
                        if($data_pkl['status'] == 'disetujui_dosen') {
                            echo '<div class="alert alert-success">Selamat! Pengajuan PKL Anda telah disetujui.</div>';
                            echo '<a href="cetak_surat_pengantar.php" target="_blank" class="btn btn-info"><i class="fas fa-print"></i> Cetak Surat Pengantar</a>';
                        } elseif ($data_pkl['status'] == 'diajukan') {
                            echo '<div class="alert alert-info">Pengajuan PKL Anda sedang menunggu verifikasi dari dosen.</div>';
                        } elseif ($data_pkl['status'] == 'ditolak') {
                            echo '<div class="alert alert-danger">Mohon maaf, pengajuan PKL Anda ditolak. Silakan hubungi dosen pembimbing Anda.</div>';
                        }
                    } else {
                        echo '<div class="alert alert-warning">Anda belum melakukan pengajuan PKL.</div>';
                    }
                } elseif ($role == 'dosen') {
                    echo "<h5>Tugas Anda</h5><p>Silakan periksa menu di samping untuk memverifikasi ajuan, memonitor bimbingan, atau memberi penilaian PKL mahasiswa.</p>";
                } elseif ($role == 'dudi') {
                    echo "<h5>Panel Mitra DUDI</h5><p>Selamat datang, mitra industri. Silakan gunakan menu di samping untuk memberi penilaian kepada mahasiswa yang PKL di perusahaan Anda.</p>";
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php if ($role == 'admin'): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ambil data dari file PHP via Fetch API
        fetch('data_analytics.php')
            .then(response => response.json())
            .then(data => {
                const ctx = document.getElementById('pklStatusChart').getContext('2d');
                const pklStatusChart = new Chart(ctx, {
                    type: 'pie', // Tipe chart: pie, doughnut, bar, line
                    data: data,
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            title: {
                                display: true,
                                text: 'Distribusi Status PKL'
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error fetching chart data:', error));
    });
</script>
<?php endif; ?>


<?php include 'templates/footer.php'; ?>
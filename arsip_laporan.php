<?php
include 'templates/header.php';

// Proteksi halaman, hanya untuk admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Query untuk mengambil data laporan yang sudah diupload
// Kolom la.created_at dan ORDER BY la.created_at sudah dihapus/diubah
$sql = "SELECT 
            la.file_laporan,
            u.nama_lengkap AS nama_mahasiswa,
            m.nim,
            pkl.nama_perusahaan
        FROM laporan_akhir la
        JOIN pkl ON la.pkl_id = pkl.id
        JOIN users u ON pkl.mahasiswa_user_id = u.id
        JOIN mahasiswa m ON pkl.mahasiswa_user_id = m.user_id
        WHERE la.file_laporan IS NOT NULL
        ORDER BY u.nama_lengkap ASC";

$result = mysqli_query($koneksi, $sql);
?>

<div class="row">
    <div class="col-md-3">
        <?php include 'templates/sidebar.php'; ?>
    </div>
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-archive"></i> Arsip Laporan Akhir PKL</h4>
            </div>
            <div class="card-body">
                <p>Berikut adalah daftar semua laporan akhir yang telah diunggah oleh mahasiswa.</p>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Mahasiswa</th>
                                <th>Perusahaan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php $no = 1; ?>
                                <?php while($data = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td>
                                            <?php echo htmlspecialchars($data['nama_mahasiswa']); ?><br>
                                            <small class="text-muted">NIM: <?php echo htmlspecialchars($data['nim']); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($data['nama_perusahaan']); ?></td>
                                        <td>
                                            <a href="uploads/laporan_akhir/<?php echo htmlspecialchars($data['file_laporan']); ?>" class="btn btn-sm btn-success" target="_blank">Download</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada laporan yang diunggah.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
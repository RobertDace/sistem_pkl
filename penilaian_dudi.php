<?php
include 'templates/header.php';

// Proteksi halaman, hanya untuk DUDI
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dudi') {
    header("Location: index.php");
    exit();
}

$nama_perusahaan_dudi = $_SESSION['nama_perusahaan'];

// Query untuk mengambil mahasiswa yang PKL di perusahaan DUDI yang login
$sql = "SELECT 
            pkl.id AS pkl_id, 
            u.nama_lengkap AS nama_mahasiswa, 
            m.nim,
            la.nilai_dudi
        FROM pkl
        JOIN users u ON pkl.mahasiswa_user_id = u.id
        JOIN mahasiswa m ON pkl.mahasiswa_user_id = m.user_id
        LEFT JOIN laporan_akhir la ON pkl.id = la.pkl_id
        WHERE pkl.nama_perusahaan = ? AND pkl.status = 'disetujui_dosen'";

$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "s", $nama_perusahaan_dudi);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<div class="row">
    <div class="col-md-3">
        <?php include 'templates/sidebar.php'; ?>
    </div>
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h4>Penilaian Mahasiswa PKL di <?php echo htmlspecialchars($nama_perusahaan_dudi); ?></h4>
            </div>
            <div class="card-body">
                <p>Silakan input nilai untuk mahasiswa yang melaksanakan PKL di perusahaan Anda.</p>

                <?php if(isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
                    <div class="alert alert-success">Nilai berhasil disimpan.</div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>NIM</th>
                                <th>Nama Mahasiswa</th>
                                <th>Nilai dari Perusahaan (0-100)</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php while($data = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <form action="proses_penilaian_dudi.php" method="POST">
                                            <input type="hidden" name="pkl_id" value="<?php echo $data['pkl_id']; ?>">
                                            <td><?php echo htmlspecialchars($data['nim']); ?></td>
                                            <td><?php echo htmlspecialchars($data['nama_mahasiswa']); ?></td>
                                            <td>
                                                <input type="number" name="nilai_dudi" class="form-control" min="0" max="100" step="0.01" value="<?php echo htmlspecialchars($data['nilai_dudi'] ?? ''); ?>" required>
                                            </td>
                                            <td>
                                                <button type="submit" name="simpan_nilai" class="btn btn-primary btn-sm">Simpan</button>
                                            </td>
                                        </form>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada mahasiswa yang perlu dinilai saat ini.</td>
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
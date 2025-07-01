<?php
include 'templates/header.php';

// Proteksi halaman, hanya untuk dosen
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dosen') {
    header("Location: index.php");
    exit();
}

$dosen_id = $_SESSION['user_id'];

// Query untuk mengambil mahasiswa bimbingan yang statusnya sudah disetujui dosen (dianggap sudah bisa dinilai)
// Kita LEFT JOIN ke laporan_akhir untuk mengecek apakah sudah ada nilai atau belum
$sql = "SELECT 
            pkl.id AS pkl_id, 
            u.nama_lengkap AS nama_mahasiswa, 
            m.nim,
            la.nilai_dosen,
            la.nilai_dudi,
            la.nilai_akhir
        FROM pkl
        JOIN users u ON pkl.mahasiswa_user_id = u.id
        JOIN mahasiswa m ON pkl.mahasiswa_user_id = m.user_id
        LEFT JOIN laporan_akhir la ON pkl.id = la.pkl_id
        WHERE pkl.dosen_pembimbing_user_id = ? AND pkl.status = 'disetujui_dosen'";

$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "i", $dosen_id);
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
                <h4><i class="fas fa-graduation-cap"></i> Penilaian PKL Mahasiswa</h4>
            </div>
            <div class="card-body">
                <p>Silakan input nilai untuk mahasiswa bimbingan Anda yang telah menyelesaikan PKL.</p>
                <?php if(isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
                    <div class="alert alert-success">Nilai berhasil disimpan.</div>
                <?php endif; ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>NIM</th>
                                <th>Nama Mahasiswa</th>
                                <th>Nilai Pembimbing</th>
                                <th>Nilai Mitra DUDI</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php while($data = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <form action="proses_penilaian.php" method="POST">
                                            <input type="hidden" name="pkl_id" value="<?php echo $data['pkl_id']; ?>">
                                            <td><?php echo htmlspecialchars($data['nim']); ?></td>
                                            <td><?php echo htmlspecialchars($data['nama_mahasiswa']); ?></td>
                                            <td>
                                                <input type="number" name="nilai_dosen" class="form-control" min="0" max="100" step="0.01" value="<?php echo htmlspecialchars($data['nilai_dosen'] ?? ''); ?>" required>
                                            </td>
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
                                    <td colspan="5" class="text-center">Tidak ada mahasiswa bimbingan yang aktif untuk dinilai.</td>
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
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
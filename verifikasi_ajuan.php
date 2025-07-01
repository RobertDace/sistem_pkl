<?php
include 'templates/header.php';

// Proteksi halaman, hanya untuk dosen
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dosen') {
    header("Location: index.php");
    exit();
}

$dosen_id = $_SESSION['user_id'];

// Query untuk mengambil data ajuan PKL yang ditujukan kepada dosen yang sedang login
// dan statusnya masih 'diajukan'.
// Kita JOIN dengan tabel users untuk mendapatkan nama mahasiswa dan tabel mahasiswa untuk NIM.
$sql = "SELECT pkl.id, users.nama_lengkap AS nama_mahasiswa, mahasiswa.nim, pkl.nama_perusahaan, pkl.proposal
        FROM pkl
        JOIN users ON pkl.mahasiswa_user_id = users.id
        JOIN mahasiswa ON pkl.mahasiswa_user_id = mahasiswa.user_id
        WHERE pkl.dosen_pembimbing_user_id = ? AND pkl.status = 'diajukan'";

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
                <h4>Verifikasi Ajuan PKL</h4>
            </div>
            <div class="card-body">
                <p>Berikut adalah daftar pengajuan PKL dari mahasiswa yang memilih Anda sebagai dosen pembimbing.</p>

                <?php
                // Tampilkan notifikasi jika ada
                if (isset($_GET['status']) && $_GET['status'] == 'sukses') {
                    echo '<div class="alert alert-success">Status pengajuan berhasil diperbarui.</div>';
                }
                ?>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>NIM</th>
                                <th>Nama Mahasiswa</th>
                                <th>Nama Perusahaan</th>
                                <th>Proposal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php $no = 1; ?>
                                <?php while($data = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo htmlspecialchars($data['nim']); ?></td>
                                        <td><?php echo htmlspecialchars($data['nama_mahasiswa']); ?></td>
                                        <td><?php echo htmlspecialchars($data['nama_perusahaan']); ?></td>
                                        <td>
                                            <a href="uploads/proposal/<?php echo htmlspecialchars($data['proposal']); ?>" class="btn btn-sm btn-info" target="_blank">Lihat</a>
                                        </td>
                                        <td>
                                            <a href="proses_verifikasi.php?id=<?php echo $data['id']; ?>&aksi=setujui" class="btn btn-sm btn-success" onclick="return confirm('Apakah Anda yakin ingin menyetujui pengajuan ini?')">Setujui</a>
                                            <a href="proses_verifikasi.php?id=<?php echo $data['id']; ?>&aksi=tolak" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menolak pengajuan ini?')">Tolak</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada pengajuan yang perlu diverifikasi saat ini.</td>
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
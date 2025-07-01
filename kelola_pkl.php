<?php
include 'templates/header.php';

// Proteksi halaman, hanya untuk admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Query untuk mengambil semua data PKL dengan join ke tabel lain untuk info lengkap
$sql = "SELECT 
            pkl.id, 
            u_mhs.nama_lengkap AS nama_mahasiswa, 
            m.nim,
            pkl.nama_perusahaan,
            u_dosen.nama_lengkap AS nama_dosen,
            pkl.tanggal_mulai,
            pkl.tanggal_selesai,
            pkl.status
        FROM pkl
        JOIN users u_mhs ON pkl.mahasiswa_user_id = u_mhs.id
        JOIN mahasiswa m ON pkl.mahasiswa_user_id = m.user_id
        LEFT JOIN users u_dosen ON pkl.dosen_pembimbing_user_id = u_dosen.id
        ORDER BY pkl.created_at DESC";

$result = mysqli_query($koneksi, $sql);
?>

<div class="row">
    <div class="col-md-3">
        <?php include 'templates/sidebar.php'; ?>
    </div>
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-tasks"></i> Kelola Seluruh Data PKL</h4>
            </div>
            <div class="card-body">
                <p>Halaman ini digunakan untuk mengelola semua data PKL yang ada di dalam sistem.</p>
                
                <?php if (isset($_GET['status'])): ?>
                    <?php if ($_GET['status'] == 'sukses_edit'): ?>
                        <div class="alert alert-success">Data PKL berhasil diperbarui.</div>
                    <?php elseif ($_GET['status'] == 'sukses_hapus'): ?>
                        <div class="alert alert-success">Data PKL dan semua data terkait berhasil dihapus.</div>
                    <?php elseif ($_GET['status'] == 'gagal_hapus'): ?>
                        <div class="alert alert-danger">Gagal menghapus data.</div>
                    <?php endif; ?>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Mahasiswa</th>
                                <th>Perusahaan</th>
                                <th>Dosen Pembimbing</th>
                                <th>Status</th>
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
                                        <td><?php echo htmlspecialchars($data['nama_dosen'] ?? 'Belum ada'); ?></td>
                                        <td>
                                            <?php
                                                $status = $data['status'];
                                                $badge_class = 'bg-secondary';
                                                if ($status == 'diajukan') $badge_class = 'bg-warning text-dark';
                                                elseif ($status == 'disetujui_dosen') $badge_class = 'bg-success';
                                                elseif ($status == 'ditolak') $badge_class = 'bg-danger';
                                            ?>
                                            <span class="badge <?php echo $badge_class; ?>"><?php echo ucwords(str_replace('_', ' ', $status)); ?></span>
                                        </td>
                                        <td>
                                            <a href="edit_pkl_admin.php?id=<?php echo $data['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                            <a href="proses_pkl_admin.php?aksi=hapus&id=<?php echo $data['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Anda yakin ingin menghapus data PKL ini? Semua data terkait (bimbingan, laporan, nilai) juga akan terhapus.')">Hapus</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data PKL.</td>
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
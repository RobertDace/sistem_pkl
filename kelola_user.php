<?php
include 'templates/header.php';

// Proteksi halaman, hanya untuk admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Query untuk mengambil semua data pengguna
$sql = "SELECT users.*, mahasiswa.nim 
        FROM users 
        LEFT JOIN mahasiswa ON users.id = mahasiswa.user_id 
        ORDER BY users.role, users.nama_lengkap ASC";
$result = mysqli_query($koneksi, $sql);
?>

<div class="row">
    <div class="col-md-3">
        <?php include 'templates/sidebar.php'; ?>
    </div>
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h4>Kelola Data Pengguna</h4>
            </div>
            <div class="card-body">
                <a href="tambah_user.php" class="btn btn-primary mb-3"><i class="fas fa-plus"></i> Tambah Pengguna Baru</a>
                
                <?php
                // Tampilkan notifikasi jika ada
                if (isset($_GET['status'])) {
                    if ($_GET['status'] == 'sukses_hapus') {
                        echo '<div class="alert alert-success">Pengguna berhasil dihapus.</div>';
                    } elseif ($_GET['status'] == 'gagal_hapus') {
                        echo '<div class="alert alert-danger">Gagal menghapus pengguna. Pengguna ini mungkin terkait dengan data PKL atau bimbingan.</div>';
                    }
                }
                ?>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Username</th>
                                <th>Nama Lengkap</th>
                                <th>NIM</th>
                                <th>Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php $no = 1; ?>
                                <?php while($user = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td><?php echo htmlspecialchars($user['nama_lengkap']); ?></td>
                                        <td><?php echo $user['role'] == 'mahasiswa' ? htmlspecialchars($user['nim']) : '-'; ?></td>
                                        <td><span class="badge bg-info text-dark"><?php echo ucfirst($user['role']); ?></span></td>
                                        <td>
                                            <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                <a href="proses_user.php?aksi=hapus&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('PERINGATAN: Menghapus pengguna juga akan menghapus data terkait (jika ada). Apakah Anda yakin ingin melanjutkan?')">Hapus</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data pengguna.</td>
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
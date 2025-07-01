<?php
include 'templates/header.php';

// Proteksi halaman, hanya untuk dosen
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dosen') {
    header("Location: index.php");
    exit();
}

$dosen_id = $_SESSION['user_id'];
$page_title = 'Pilih Mahasiswa Bimbingan';
?>

<div class="row">
    <div class="col-md-3">
        <?php include 'templates/sidebar.php'; ?>
    </div>
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-search"></i> Monitoring Bimbingan PKL</h4>
            </div>
            <div class="card-body">

                <?php if (!isset($_GET['pkl_id'])): ?>
                    <p>Pilih mahasiswa untuk melihat detail logbook bimbingan mereka.</p>
                    <?php
                    // Query untuk mengambil daftar mahasiswa yang dibimbing oleh dosen ini & PKLnya sudah disetujui
                    $sql_mhs = "SELECT pkl.id AS pkl_id, users.nama_lengkap AS nama_mahasiswa, mahasiswa.nim, pkl.nama_perusahaan
                                FROM pkl
                                JOIN users ON pkl.mahasiswa_user_id = users.id
                                JOIN mahasiswa ON pkl.mahasiswa_user_id = mahasiswa.user_id
                                WHERE pkl.dosen_pembimbing_user_id = ? AND pkl.status = 'disetujui_dosen'";
                    $stmt_mhs = mysqli_prepare($koneksi, $sql_mhs);
                    mysqli_stmt_bind_param($stmt_mhs, "i", $dosen_id);
                    mysqli_stmt_execute($stmt_mhs);
                    $result_mhs = mysqli_stmt_get_result($stmt_mhs);
                    ?>
                    <ul class="list-group">
                        <?php if (mysqli_num_rows($result_mhs) > 0): ?>
                            <?php while($mhs = mysqli_fetch_assoc($result_mhs)): ?>
                                <a href="monitoring_bimbingan.php?pkl_id=<?php echo $mhs['pkl_id']; ?>" class="list-group-item list-group-item-action">
                                    <strong><?php echo htmlspecialchars($mhs['nama_mahasiswa']); ?></strong> (<?php echo htmlspecialchars($mhs['nim']); ?>)
                                    <br>
                                    <small class="text-muted">PKL di: <?php echo htmlspecialchars($mhs['nama_perusahaan']); ?></small>
                                </a>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <li class="list-group-item">Anda tidak memiliki mahasiswa bimbingan yang aktif.</li>
                        <?php endif; ?>
                    </ul>

                <?php else: ?>
                    <?php
                    $pkl_id = (int)$_GET['pkl_id'];

                    // Ambil data logbook untuk pkl_id yang dipilih
                    $sql_logbook = "SELECT * FROM bimbingan WHERE pkl_id = ? ORDER BY tanggal ASC";
                    $stmt_logbook = mysqli_prepare($koneksi, $sql_logbook);
                    mysqli_stmt_bind_param($stmt_logbook, "i", $pkl_id);
                    mysqli_stmt_execute($stmt_logbook);
                    $result_logbook = mysqli_stmt_get_result($stmt_logbook);
                    ?>
                    <a href="monitoring_bimbingan.php" class="btn btn-secondary mb-3">&laquo; Kembali ke Daftar Mahasiswa</a>
                    
                    <?php if(isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
                        <div class="alert alert-success">Logbook berhasil diverifikasi.</div>
                    <?php endif; ?>

                    <h5>Riwayat Logbook Mahasiswa</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kegiatan</th>
                                    <th>Verifikasi & Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($result_logbook) > 0): ?>
                                    <?php while($entry = mysqli_fetch_assoc($result_logbook)): ?>
                                        <tr>
                                            <td style="width: 15%;"><?php echo date('d M Y', strtotime($entry['tanggal'])); ?></td>
                                            <td><?php echo nl2br(htmlspecialchars($entry['kegiatan'])); ?></td>
                                            <td style="width: 35%;">
                                                <?php if($entry['status_verifikasi'] == 'diverifikasi'): ?>
                                                    <div class="alert alert-success p-2">
                                                        <strong><i class="fas fa-check-circle"></i> Diverifikasi</strong><br>
                                                        <small>Catatan: <?php echo nl2br(htmlspecialchars($entry['catatan_dosen'])); ?></small>
                                                    </div>
                                                <?php else: ?>
                                                    <form action="proses_verifikasi_bimbingan.php" method="POST">
                                                        <input type="hidden" name="bimbingan_id" value="<?php echo $entry['id']; ?>">
                                                        <input type="hidden" name="pkl_id" value="<?php echo $pkl_id; ?>">
                                                        <div class="mb-2">
                                                            <textarea name="catatan_dosen" class="form-control form-control-sm" rows="2" placeholder="Beri catatan..."></textarea>
                                                        </div>
                                                        <button type="submit" name="verifikasi" class="btn btn-sm btn-primary">Verifikasi</button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center">Mahasiswa ini belum mengisi logbook.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
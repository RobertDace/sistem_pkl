<?php
include 'templates/header.php';

// Proteksi halaman, hanya untuk mahasiswa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: index.php");
    exit();
}

$mahasiswa_id = $_SESSION['user_id'];
$pkl_data = null;
$laporan_data = null;

// 1. Cek apakah mahasiswa ini memiliki PKL yang sudah disetujui
$sql_pkl = "SELECT id FROM pkl WHERE mahasiswa_user_id = ? AND status = 'disetujui_dosen'";
$stmt_pkl = mysqli_prepare($koneksi, $sql_pkl);
mysqli_stmt_bind_param($stmt_pkl, "i", $mahasiswa_id);
mysqli_stmt_execute($stmt_pkl);
$result_pkl = mysqli_stmt_get_result($stmt_pkl);

if (mysqli_num_rows($result_pkl) > 0) {
    $pkl_data = mysqli_fetch_assoc($result_pkl);
    $pkl_id = $pkl_data['id'];

    // 2. Jika ada PKL, cek data laporan akhir dan nilai yang sudah ada
    $sql_laporan = "SELECT * FROM laporan_akhir WHERE pkl_id = ?";
    $stmt_laporan = mysqli_prepare($koneksi, $sql_laporan);
    mysqli_stmt_bind_param($stmt_laporan, "i", $pkl_id);
    mysqli_stmt_execute($stmt_laporan);
    $result_laporan = mysqli_stmt_get_result($stmt_laporan);
    if (mysqli_num_rows($result_laporan) > 0) {
        $laporan_data = mysqli_fetch_assoc($result_laporan);
    }
}
?>

<div class="row">
    <div class="col-md-3">
        <?php include 'templates/sidebar.php'; ?>
    </div>
    <div class="col-md-9">
        <?php if ($pkl_data): ?>
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-file-upload"></i> Laporan Akhir dan Nilai</h4>
                </div>
                <div class="card-body">
                    <?php if(isset($_GET['status']) && $_GET['status'] == 'sukses_upload'): ?>
                        <div class="alert alert-success">File laporan akhir berhasil diunggah.</div>
                    <?php endif; ?>

                    <h5>Status Laporan Akhir</h5>
                    <?php if (!empty($laporan_data['file_laporan'])): ?>
                        <div class="alert alert-info">
                            Anda sudah mengunggah laporan akhir: 
                            <a href="uploads/laporan_akhir/<?php echo htmlspecialchars($laporan_data['file_laporan']); ?>" target="_blank">
                                <?php echo htmlspecialchars($laporan_data['file_laporan']); ?>
                            </a>
                        </div>
                    <?php else: ?>
                        <p>Anda belum mengunggah laporan akhir.</p>
                        <form action="proses_laporan_akhir.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="pkl_id" value="<?php echo $pkl_data['id']; ?>">
                            <div class="mb-3">
                                <label for="laporan" class="form-label">Pilih File Laporan (Format: .PDF, Max: 5MB)</label>
                                <input class="form-control" type="file" id="laporan" name="laporan_akhir" accept=".pdf" required>
                            </div>
                            <button type="submit" name="upload_laporan" class="btn btn-primary">Upload Laporan</button>
                        </form>
                    <?php endif; ?>

                    <hr>

                    <h5>Rincian Nilai Akhir</h5>
                    <?php if (!empty($laporan_data['nilai_akhir'])): ?>
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Nilai dari Dosen Pembimbing
                                <span class="badge bg-primary rounded-pill"><?php echo htmlspecialchars($laporan_data['nilai_dosen']); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Nilai dari Mitra DUDI
                                <span class="badge bg-primary rounded-pill"><?php echo htmlspecialchars($laporan_data['nilai_dudi']); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center active">
                                <strong>Nilai Akhir PKL</strong>
                                <strong><?php echo htmlspecialchars($laporan_data['nilai_akhir']); ?></strong>
                            </li>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted">Nilai belum diinput oleh Dosen Pembimbing.</p>
                    <?php endif; ?>

                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                <h4>Akses Ditolak</h4>
                <p>Anda tidak dapat mengakses halaman ini karena belum memiliki pengajuan PKL yang berstatus 'Disetujui Dosen'.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
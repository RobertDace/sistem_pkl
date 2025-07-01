<?php
include 'templates/header.php';

// Proteksi halaman, hanya untuk mahasiswa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: index.php");
    exit();
}

$mahasiswa_id = $_SESSION['user_id'];
$pkl_data = null;
$logbook_entries = [];

// 1. Cek apakah mahasiswa ini memiliki PKL yang sudah disetujui atau sedang berjalan
$sql_pkl = "SELECT id FROM pkl WHERE mahasiswa_user_id = ? AND status = 'disetujui_dosen'";
$stmt_pkl = mysqli_prepare($koneksi, $sql_pkl);
mysqli_stmt_bind_param($stmt_pkl, "i", $mahasiswa_id);
mysqli_stmt_execute($stmt_pkl);
$result_pkl = mysqli_stmt_get_result($stmt_pkl);

if (mysqli_num_rows($result_pkl) > 0) {
    $pkl_data = mysqli_fetch_assoc($result_pkl);
    $pkl_id = $pkl_data['id'];

    // 2. Jika ada PKL, ambil semua entri logbook yang terkait
    $sql_logbook = "SELECT * FROM bimbingan WHERE pkl_id = ? ORDER BY tanggal DESC";
    $stmt_logbook = mysqli_prepare($koneksi, $sql_logbook);
    mysqli_stmt_bind_param($stmt_logbook, "i", $pkl_id);
    mysqli_stmt_execute($stmt_logbook);
    $result_logbook = mysqli_stmt_get_result($stmt_logbook);
    while ($row = mysqli_fetch_assoc($result_logbook)) {
        $logbook_entries[] = $row;
    }
}
?>

<div class="row">
    <div class="col-md-3">
        <?php include 'templates/sidebar.php'; ?>
    </div>
    <div class="col-md-9">
        <?php if ($pkl_data): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h4><i class="fas fa-book-open"></i> Isi Logbook Kegiatan PKL</h4>
                </div>
                <div class="card-body">
                    <?php if(isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
                        <div class="alert alert-success">Kegiatan berhasil disimpan.</div>
                    <?php endif; ?>
                    <form action="proses_logbook.php" method="POST">
                        <input type="hidden" name="pkl_id" value="<?php echo $pkl_data['id']; ?>">
                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal Kegiatan</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                        </div>
                        <div class="mb-3">
                            <label for="kegiatan" class="form-label">Uraian Kegiatan</label>
                            <textarea class="form-control" id="kegiatan" name="kegiatan" rows="4" placeholder="Jelaskan kegiatan yang Anda lakukan pada tanggal tersebut" required></textarea>
                        </div>
                        <button type="submit" name="simpan_kegiatan" class="btn btn-primary">Simpan Kegiatan</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-history"></i> Riwayat Logbook</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kegiatan</th>
                                    <th>Status Verifikasi</th>
                                    <th>Catatan Dosen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($logbook_entries)): ?>
                                    <?php foreach ($logbook_entries as $entry): ?>
                                        <tr>
                                            <td><?php echo date('d M Y', strtotime($entry['tanggal'])); ?></td>
                                            <td><?php echo nl2br(htmlspecialchars($entry['kegiatan'])); ?></td>
                                            <td>
                                                <?php if ($entry['status_verifikasi'] == 'diverifikasi'): ?>
                                                    <span class="badge bg-success">Diverifikasi</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark">Belum Dilihat</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo nl2br(htmlspecialchars($entry['catatan_dosen'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada kegiatan yang dicatat.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                <h4>Akses Ditolak</h4>
                <p>Anda tidak dapat mengisi logbook karena Anda belum memiliki pengajuan PKL yang berstatus 'Disetujui Dosen'.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
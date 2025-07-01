<?php
include 'templates/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: index.php"); exit(); }
if (!isset($_GET['id'])) { header("Location: kelola_pkl.php"); exit(); }

$pkl_id = (int)$_GET['id'];

// Ambil data PKL yang akan diedit
$sql_pkl = "SELECT * FROM pkl WHERE id = ?";
$stmt_pkl = mysqli_prepare($koneksi, $sql_pkl);
mysqli_stmt_bind_param($stmt_pkl, "i", $pkl_id);
mysqli_stmt_execute($stmt_pkl);
$result_pkl = mysqli_stmt_get_result($stmt_pkl);
$pkl = mysqli_fetch_assoc($result_pkl);

if (!$pkl) { header("Location: kelola_pkl.php"); exit(); }

// Ambil daftar dosen untuk dropdown
$sql_dosen = "SELECT id, nama_lengkap FROM users WHERE role = 'dosen'";
$result_dosen = mysqli_query($koneksi, $sql_dosen);
?>

<div class="row">
    <div class="col-md-3"><?php include 'templates/sidebar.php'; ?></div>
    <div class="col-md-9">
        <div class="card">
            <div class="card-header"><h4>Edit Data PKL</h4></div>
            <div class="card-body">
                <form action="proses_pkl_admin.php" method="POST">
                    <input type="hidden" name="aksi" value="edit">
                    <input type="hidden" name="pkl_id" value="<?php echo $pkl['id']; ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Perusahaan</label>
                        <input type="text" name="nama_perusahaan" class="form-control" value="<?php echo htmlspecialchars($pkl['nama_perusahaan']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat Perusahaan</label>
                        <textarea name="alamat_perusahaan" class="form-control" rows="3" required><?php echo htmlspecialchars($pkl['alamat_perusahaan']); ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" class="form-control" value="<?php echo $pkl['tanggal_mulai']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" class="form-control" value="<?php echo $pkl['tanggal_selesai']; ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dosen Pembimbing</label>
                        <select name="dosen_pembimbing_user_id" class="form-select" required>
                            <?php while($dosen = mysqli_fetch_assoc($result_dosen)): ?>
                                <option value="<?php echo $dosen['id']; ?>" <?php echo ($dosen['id'] == $pkl['dosen_pembimbing_user_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($dosen['nama_lengkap']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="diajukan" <?php echo ($pkl['status'] == 'diajukan') ? 'selected' : ''; ?>>Diajukan</option>
                            <option value="disetujui_dosen" <?php echo ($pkl['status'] == 'disetujui_dosen') ? 'selected' : ''; ?>>Disetujui Dosen</option>
                            <option value="ditolak" <?php echo ($pkl['status'] == 'ditolak') ? 'selected' : ''; ?>>Ditolak</option>
                            <option value="selesai" <?php echo ($pkl['status'] == 'selesai') ? 'selected' : ''; ?>>Selesai</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="kelola_pkl.php" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
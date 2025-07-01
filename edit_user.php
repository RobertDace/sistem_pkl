<?php
include 'templates/header.php';

// Proteksi dan ambil data user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: index.php"); exit(); }
if (!isset($_GET['id'])) { header("Location: kelola_user.php"); exit(); }
$user_id_edit = (int)$_GET['id'];
$sql = "SELECT users.*, mahasiswa.nim, mahasiswa.prodi FROM users LEFT JOIN mahasiswa ON users.id = mahasiswa.user_id WHERE users.id = ?";
$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id_edit);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
if (!$user) { header("Location: kelola_user.php"); exit(); }
?>

<div class="row">
    <div class="col-md-3">
        <?php include 'templates/sidebar.php'; ?>
    </div>
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h4>Edit Pengguna: <?php echo htmlspecialchars($user['nama_lengkap']); ?></h4>
            </div>
            <div class="card-body">
                <form action="proses_user.php" method="POST">
                    <input type="hidden" name="aksi" value="edit">
                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">

                    <div class="mb-3"><label for="username" class="form-label">Username</label><input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required></div>
                    <div class="mb-3"><label for="password" class="form-label">Password Baru (Opsional)</label><input type="password" class="form-control" id="password" name="password"><small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password.</small></div>
                    <div class="mb-3"><label for="nama_lengkap" class="form-label">Nama Lengkap</label><input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?php echo htmlspecialchars($user['nama_lengkap']); ?>" required></div>
                    
                    <div class="mb-3"><label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required onchange="toggleFields()">
                            <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                            <option value="dosen" <?php echo ($user['role'] == 'dosen') ? 'selected' : ''; ?>>Dosen</option>
                            <option value="mahasiswa" <?php echo ($user['role'] == 'mahasiswa') ? 'selected' : ''; ?>>Mahasiswa</option>
                            <option value="dudi" <?php echo ($user['role'] == 'dudi') ? 'selected' : ''; ?>>Mitra DUDI</option>
                        </select>
                    </div>

                    <div id="mahasiswa-fields" class="sub-fields" style="display: <?php echo ($user['role'] == 'mahasiswa') ? 'block' : 'none'; ?>;">
                        <div class="mb-3"><label for="nim" class="form-label">NIM</label><input type="text" class="form-control" id="nim" name="nim" value="<?php echo htmlspecialchars($user['nim'] ?? ''); ?>"></div>
                        <div class="mb-3"><label for="prodi" class="form-label">Program Studi</label><input type="text" class="form-control" id="prodi" name="prodi" value="<?php echo htmlspecialchars($user['prodi'] ?? ''); ?>"></div>
                    </div>

                     <div id="dudi-fields" class="sub-fields" style="display: <?php echo ($user['role'] == 'dudi') ? 'block' : 'none'; ?>;">
                        <div class="mb-3"><label for="nama_perusahaan" class="form-label">Nama Perusahaan</label><input type="text" class="form-control" id="nama_perusahaan" name="nama_perusahaan" value="<?php echo htmlspecialchars($user['nama_perusahaan'] ?? ''); ?>"></div>
                    </div>

                    <hr>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="kelola_user.php" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleFields() {
    var role = document.getElementById('role').value;
    // Sembunyikan semua field tambahan dulu
    document.querySelectorAll('.sub-fields').forEach(field => field.style.display = 'none');
    document.querySelectorAll('.sub-fields input').forEach(input => input.required = false);

    if (role === 'mahasiswa') {
        document.getElementById('mahasiswa-fields').style.display = 'block';
        document.getElementById('nim').required = true;
        document.getElementById('prodi').required = true;
    } else if (role === 'dudi') {
        document.getElementById('dudi-fields').style.display = 'block';
        document.getElementById('nama_perusahaan').required = true;
    }
}
// Jalankan fungsi saat halaman pertama kali dimuat untuk menyesuaikan tampilan awal
document.addEventListener('DOMContentLoaded', toggleFields);
</script>

<?php include 'templates/footer.php'; ?>
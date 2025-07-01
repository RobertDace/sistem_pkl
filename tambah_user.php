<?php
include 'templates/header.php';

// Proteksi halaman, hanya untuk admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}
?>

<div class="row">
    <div class="col-md-3">
        <?php include 'templates/sidebar.php'; ?>
    </div>
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h4>Tambah Pengguna Baru</h4>
            </div>
            <div class="card-body">
                <form action="proses_user.php" method="POST">
                    <input type="hidden" name="aksi" value="tambah">

                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required onchange="toggleFields()">
                            <option value="" disabled selected>-- Pilih Role --</option>
                            <option value="admin">Admin</option>
                            <option value="dosen">Dosen</option>
                            <option value="mahasiswa">Mahasiswa</option>
                            <option value="dudi">Mitra DUDI</option>
                        </select>
                    </div>

                    <div id="mahasiswa-fields" class="sub-fields" style="display: none;">
                        <div class="mb-3">
                            <label for="nim" class="form-label">NIM</label>
                            <input type="text" class="form-control" id="nim" name="nim">
                        </div>
                        <div class="mb-3">
                            <label for="prodi" class="form-label">Program Studi</label>
                            <input type="text" class="form-control" id="prodi" name="prodi">
                        </div>
                    </div>

                    <div id="dudi-fields" class="sub-fields" style="display: none;">
                        <div class="mb-3">
                            <label for="nama_perusahaan" class="form-label">Nama Perusahaan</label>
                            <input type="text" class="form-control" id="nama_perusahaan" name="nama_perusahaan">
                        </div>
                    </div>

                    <hr>
                    <button type="submit" class="btn btn-primary">Simpan Pengguna</button>
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
</script>

<?php include 'templates/footer.php'; ?>
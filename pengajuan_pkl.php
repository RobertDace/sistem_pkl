<?php
include 'templates/header.php';

// Proteksi halaman, hanya untuk mahasiswa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: index.php");
    exit();
}

// Ambil daftar dosen dari database untuk pilihan Dosen Pembimbing
$query_dosen = mysqli_query($koneksi, "SELECT id, nama_lengkap FROM users WHERE role = 'dosen'");

?>

<div class="row">
    <div class="col-md-3">
        <?php include 'templates/sidebar.php'; ?>
    </div>
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h4>Form Pengajuan Praktik Kerja Lapangan</h4>
            </div>
            <div class="card-body">
                <form action="proses_pengajuan.php" method="POST" enctype="multipart/form-data">

                    <div class="mb-3">
                        <label for="nama_perusahaan" class="form-label">Nama Perusahaan/Mitra DUDI</label>
                        <input type="text" class="form-control" id="nama_perusahaan" name="nama_perusahaan" required>
                    </div>

                    <div class="mb-3">
                        <label for="alamat_perusahaan" class="form-label">Alamat Perusahaan</label>
                        <textarea class="form-control" id="alamat_perusahaan" name="alamat_perusahaan" rows="3" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_mulai" class="form-label">Tanggal Mulai PKL</label>
                            <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_selesai" class="form-label">Tanggal Selesai PKL</label>
                            <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="dosen_pembimbing" class="form-label">Dosen Pembimbing</label>
                        <select class="form-select" id="dosen_pembimbing" name="dosen_pembimbing_user_id" required>
                            <option value="" disabled selected>-- Pilih Dosen Pembimbing --</option>
                            <?php while($dosen = mysqli_fetch_assoc($query_dosen)): ?>
                                <option value="<?php echo $dosen['id']; ?>"><?php echo htmlspecialchars($dosen['nama_lengkap']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="proposal" class="form-label">Upload Proposal (Format: .PDF, Max: 2MB)</label>
                        <input class="form-control" type="file" id="proposal" name="proposal" accept=".pdf" required>
                    </div>

                    <hr>
                    <button type="submit" name="ajukan" class="btn btn-primary">Ajukan Sekarang</button>
                    <a href="dashboard.php" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
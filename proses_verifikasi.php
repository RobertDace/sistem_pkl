<?php
require_once 'config/koneksi.php';

// Proteksi, pastikan yang akses adalah dosen yang login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dosen') {
    header("Location: index.php");
    exit();
}

// Cek apakah ada parameter 'id' dan 'aksi' dari URL
if (isset($_GET['id']) && isset($_GET['aksi'])) {
    $pkl_id = (int)$_GET['id'];
    $aksi = $_GET['aksi'];
    $dosen_id = $_SESSION['user_id'];

    // Tentukan status baru berdasarkan aksi
    $status_baru = '';
    if ($aksi == 'setujui') {
        $status_baru = 'disetujui_dosen';
    } elseif ($aksi == 'tolak') {
        $status_baru = 'ditolak';
    } else {
        // Aksi tidak valid, kembalikan
        header("Location: verifikasi_ajuan.php");
        exit();
    }

    // Query UPDATE untuk mengubah status
    $sql = "UPDATE pkl SET status = ? WHERE id = ? AND dosen_pembimbing_user_id = ?";
    $stmt = mysqli_prepare($koneksi, $sql);
    
    // Bind parameter ke query
    mysqli_stmt_bind_param($stmt, "sii", $status_baru, $pkl_id, $dosen_id);

    // Eksekusi query
    if (mysqli_stmt_execute($stmt)) {
        // =======================================================
        // ===== BAGIAN BARU: BUAT NOTIFIKASI UNTUK MAHASISWA =====
        // =======================================================

        // Dapatkan user_id mahasiswa untuk dikirimi notifikasi
        $sql_get_mhs_id = "SELECT mahasiswa_user_id FROM pkl WHERE id = ?";
        $stmt_get_mhs = mysqli_prepare($koneksi, $sql_get_mhs_id);
        mysqli_stmt_bind_param($stmt_get_mhs, "i", $pkl_id);
        mysqli_stmt_execute($stmt_get_mhs);
        $result_mhs = mysqli_stmt_get_result($stmt_get_mhs);
        $mhs_data = mysqli_fetch_assoc($result_mhs);
        $mahasiswa_id = $mhs_data['mahasiswa_user_id'];
        
        // Buat pesan notifikasi
        $status_text = ($status_baru == 'disetujui_dosen') ? "DISETUJUI" : "DITOLAK";
        $pesan = "Pengajuan PKL Anda telah " . $status_text . " oleh dosen pembimbing.";
        
        buatNotifikasi($koneksi, $mahasiswa_id, $pesan, 'laporan_akhir.php');

        // Jika berhasil, arahkan kembali ke halaman verifikasi
        header("Location: verifikasi_ajuan.php?status=sukses");
        exit();
    } else {
        // Jika gagal, bisa ditambahkan notifikasi error
        echo "Error: Gagal memperbarui status.";
    }

} else {
    // Jika parameter tidak lengkap, kembalikan ke halaman verifikasi
    header("Location: verifikasi_ajuan.php");
    exit();
}
?>
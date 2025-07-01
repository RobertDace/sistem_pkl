<?php
require_once 'config/koneksi.php';

// Proteksi, pastikan yang akses adalah dosen yang login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dosen') {
    header("Location: index.php");
    exit();
}

// Cek apakah tombol 'verifikasi' ditekan
if (isset($_POST['verifikasi'])) {

    // Ambil data dari form
    $bimbingan_id = (int)$_POST['bimbingan_id'];
    $pkl_id = (int)$_POST['pkl_id']; // Untuk redirect kembali
    $catatan_dosen = mysqli_real_escape_string($koneksi, $_POST['catatan_dosen']);
    $dosen_id = $_SESSION['user_id'];

    // Query untuk UPDATE status dan catatan dosen
    // Kita tidak perlu cek dosen_id di sini karena akses ke halaman monitoring sudah diproteksi,
    // tapi jika ingin lebih aman, bisa ditambahkan join untuk verifikasi.
    $sql = "UPDATE bimbingan SET status_verifikasi = 'diverifikasi', catatan_dosen = ? WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "si", $catatan_dosen, $bimbingan_id);

    if (mysqli_stmt_execute($stmt)) {
        // Jika berhasil, redirect kembali ke halaman detail logbook mahasiswa
        header("Location: monitoring_bimbingan.php?pkl_id=" . $pkl_id . "&status=sukses");
        exit();
    } else {
        echo "Error: Gagal memverifikasi.";
    }

} else {
    // Jika file diakses langsung, tendang ke dashboard
    header("Location: dashboard.php");
    exit();
}

?>
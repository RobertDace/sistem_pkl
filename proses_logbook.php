<?php
require_once 'config/koneksi.php';

// Proteksi, pastikan yang akses adalah mahasiswa yang login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: index.php");
    exit();
}

// Cek apakah tombol 'simpan_kegiatan' ditekan
if (isset($_POST['simpan_kegiatan'])) {
    
    // Ambil data dari form
    $pkl_id = (int)$_POST['pkl_id'];
    $tanggal = $_POST['tanggal'];
    $kegiatan = mysqli_real_escape_string($koneksi, $_POST['kegiatan']);

    // Validasi dasar, pastikan data tidak kosong
    if (empty($pkl_id) || empty($tanggal) || empty($kegiatan)) {
        // Seharusnya tidak terjadi jika 'required' di HTML bekerja
        header("Location: logbook.php?status=gagal");
        exit();
    }

    // Query INSERT ke tabel bimbingan
    $sql = "INSERT INTO bimbingan (pkl_id, tanggal, kegiatan) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $sql);

    mysqli_stmt_bind_param($stmt, "iss", $pkl_id, $tanggal, $kegiatan);

    if (mysqli_stmt_execute($stmt)) {
        // Jika berhasil, redirect kembali ke halaman logbook dengan notif sukses
        header("Location: logbook.php?status=sukses");
        exit();
    } else {
        // Jika gagal
        echo "Error: Gagal menyimpan data.";
    }

} else {
    // Jika file diakses langsung, tendang ke dashboard
    header("Location: dashboard.php");
    exit();
}
?>
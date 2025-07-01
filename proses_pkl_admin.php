<?php
require_once 'config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { die("Akses ditolak."); }

$aksi = isset($_POST['aksi']) ? $_POST['aksi'] : (isset($_GET['aksi']) ? $_GET['aksi'] : '');

// Aksi Hapus
if ($aksi == 'hapus') {
    $pkl_id = (int)$_GET['id'];
    mysqli_begin_transaction($koneksi);
    try {
        // Hapus data anak (bimbingan, laporan) dulu
        mysqli_query($koneksi, "DELETE FROM bimbingan WHERE pkl_id = $pkl_id");
        mysqli_query($koneksi, "DELETE FROM laporan_akhir WHERE pkl_id = $pkl_id");
        // Hapus data induk (pkl)
        $sql_pkl = "DELETE FROM pkl WHERE id = ?";
        $stmt_pkl = mysqli_prepare($koneksi, $sql_pkl);
        mysqli_stmt_bind_param($stmt_pkl, "i", $pkl_id);
        mysqli_stmt_execute($stmt_pkl);
        mysqli_commit($koneksi);
        header("Location: kelola_pkl.php?status=sukses_hapus");
    } catch (mysqli_sql_exception $e) {
        mysqli_rollback($koneksi);
        header("Location: kelola_pkl.php?status=gagal_hapus");
    }
    exit();
}
// Aksi Edit
elseif ($aksi == 'edit') {
    $pkl_id = (int)$_POST['pkl_id'];
    $nama_perusahaan = $_POST['nama_perusahaan'];
    $alamat_perusahaan = $_POST['alamat_perusahaan'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $dosen_id = (int)$_POST['dosen_pembimbing_user_id'];
    $status = $_POST['status'];

    $sql = "UPDATE pkl SET 
                nama_perusahaan = ?,
                alamat_perusahaan = ?,
                tanggal_mulai = ?,
                tanggal_selesai = ?,
                dosen_pembimbing_user_id = ?,
                status = ?
            WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "ssssisi", $nama_perusahaan, $alamat_perusahaan, $tanggal_mulai, $tanggal_selesai, $dosen_id, $status, $pkl_id);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: kelola_pkl.php?status=sukses_edit");
    } else {
        echo "Gagal memperbarui data.";
    }
    exit();
}
?>
<?php
require_once 'config/koneksi.php';

// Proteksi
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dudi') {
    header("Location: index.php");
    exit();
}

if (isset($_POST['simpan_nilai'])) {
    $pkl_id = (int)$_POST['pkl_id'];
    $nilai_dudi = (float)$_POST['nilai_dudi'];

    // Cek apakah sudah ada record di laporan_akhir untuk pkl_id ini
    $sql_cek = "SELECT id, nilai_dosen FROM laporan_akhir WHERE pkl_id = ?";
    $stmt_cek = mysqli_prepare($koneksi, $sql_cek);
    mysqli_stmt_bind_param($stmt_cek, "i", $pkl_id);
    mysqli_stmt_execute($stmt_cek);
    $result_cek = mysqli_stmt_get_result($stmt_cek);
    $existing_data = mysqli_fetch_assoc($result_cek);

    // Ambil nilai dosen yang sudah ada (jika ada), jika tidak, anggap 0
    $nilai_dosen = $existing_data['nilai_dosen'] ?? 0;

    // Hitung nilai akhir dengan bobot 60% Dosen, 40% DUDI
    $nilai_akhir = ($nilai_dosen * 0.6) + ($nilai_dudi * 0.4);

    if ($existing_data) {
        // Jika record sudah ada, lakukan UPDATE
        $sql = "UPDATE laporan_akhir SET nilai_dudi = ?, nilai_akhir = ? WHERE pkl_id = ?";
        $stmt = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "ddi", $nilai_dudi, $nilai_akhir, $pkl_id);
    } else {
        // Jika record belum ada, lakukan INSERT
        $sql = "INSERT INTO laporan_akhir (pkl_id, nilai_dudi, nilai_akhir) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "idd", $pkl_id, $nilai_dudi, $nilai_akhir);
    }

    if (mysqli_stmt_execute($stmt)) {
        header("Location: penilaian_dudi.php?status=sukses");
    } else {
        echo "Error saat menyimpan nilai.";
    }
    exit();
}
?>
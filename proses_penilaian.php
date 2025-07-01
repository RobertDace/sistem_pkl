<?php
require_once 'config/koneksi.php';

// Proteksi, pastikan yang akses adalah dosen yang login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dosen') {
    header("Location: index.php");
    exit();
}

// Cek apakah tombol 'simpan_nilai' ditekan
if (isset($_POST['simpan_nilai'])) {

    // Ambil data dari form
    $pkl_id = (int)$_POST['pkl_id'];
    $nilai_dosen = (float)$_POST['nilai_dosen'];
    $nilai_dudi = (float)$_POST['nilai_dudi'];

    // Hitung nilai akhir, contoh: 60% dari Dosen, 40% dari DUDI
    $bobot_dosen = 0.6;
    $bobot_dudi = 0.4;
    $nilai_akhir = ($nilai_dosen * $bobot_dosen) + ($nilai_dudi * $bobot_dudi);

    // Cek apakah sudah ada nilai sebelumnya untuk pkl_id ini di tabel laporan_akhir
    $sql_cek = "SELECT id FROM laporan_akhir WHERE pkl_id = ?";
    $stmt_cek = mysqli_prepare($koneksi, $sql_cek);
    mysqli_stmt_bind_param($stmt_cek, "i", $pkl_id);
    mysqli_stmt_execute($stmt_cek);
    $result_cek = mysqli_stmt_get_result($stmt_cek);

    if (mysqli_num_rows($result_cek) > 0) {
        // Jika sudah ada, lakukan UPDATE
        $sql = "UPDATE laporan_akhir SET nilai_dosen = ?, nilai_dudi = ?, nilai_akhir = ? WHERE pkl_id = ?";
        $stmt = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "dddi", $nilai_dosen, $nilai_dudi, $nilai_akhir, $pkl_id);
    } else {
        // Jika belum ada, lakukan INSERT
        $sql = "INSERT INTO laporan_akhir (pkl_id, nilai_dosen, nilai_dudi, nilai_akhir) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "iddd", $pkl_id, $nilai_dosen, $nilai_dudi, $nilai_akhir);
    }

    // Eksekusi query
    if (mysqli_stmt_execute($stmt)) {
        header("Location: penilaian.php?status=sukses");
        exit();
    } else {
        echo "Error: Gagal menyimpan nilai.";
        exit();
    }

} else {
    // Jika file diakses langsung, tendang ke dashboard
    header("Location: dashboard.php");
    exit();
}
?>
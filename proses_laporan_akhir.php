<?php
require_once 'config/koneksi.php';

// Proteksi, pastikan yang akses adalah mahasiswa yang login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: index.php");
    exit();
}

// Cek apakah tombol 'upload_laporan' ditekan
if (isset($_POST['upload_laporan'])) {

    $pkl_id = (int)$_POST['pkl_id'];
    $mahasiswa_user_id = $_SESSION['user_id'];

    // --- PROSES UPLOAD FILE ---
    $laporan_nama = $_FILES['laporan_akhir']['name'];
    $laporan_tmp = $_FILES['laporan_akhir']['tmp_name'];
    $laporan_error = $_FILES['laporan_akhir']['error'];
    $laporan_ukuran = $_FILES['laporan_akhir']['size'];

    if ($laporan_error === 0) {
        // Cek ukuran file (Max 5MB)
        if ($laporan_ukuran > 5242880) {
            echo "<script>alert('Ukuran file laporan terlalu besar! Maksimal 5MB.'); window.history.back();</script>";
            exit();
        }

        $ekstensi_file = strtolower(pathinfo($laporan_nama, PATHINFO_EXTENSION));
        if ($ekstensi_file !== 'pdf') {
            echo "<script>alert('Format file laporan harus .PDF!'); window.history.back();</script>";
            exit();
        }

        $laporan_nama_baru = "laporan_akhir_" . time() . "_" . $mahasiswa_user_id . "." . $ekstensi_file;
        $tujuan_upload = "uploads/laporan_akhir/" . $laporan_nama_baru;

        if (move_uploaded_file($laporan_tmp, $tujuan_upload)) {
            // --- PROSES INSERT/UPDATE DATABASE ---
            $sql_cek = "SELECT id FROM laporan_akhir WHERE pkl_id = ?";
            $stmt_cek = mysqli_prepare($koneksi, $sql_cek);
            mysqli_stmt_bind_param($stmt_cek, "i", $pkl_id);
            mysqli_stmt_execute($stmt_cek);
            $result_cek = mysqli_stmt_get_result($stmt_cek);

            if (mysqli_num_rows($result_cek) > 0) {
                // Jika record sudah ada (dibuat saat dosen input nilai), UPDATE
                $sql = "UPDATE laporan_akhir SET file_laporan = ? WHERE pkl_id = ?";
                $stmt = mysqli_prepare($koneksi, $sql);
                mysqli_stmt_bind_param($stmt, "si", $laporan_nama_baru, $pkl_id);
            } else {
                // Jika record belum ada sama sekali, INSERT
                $sql = "INSERT INTO laporan_akhir (pkl_id, file_laporan) VALUES (?, ?)";
                $stmt = mysqli_prepare($koneksi, $sql);
                mysqli_stmt_bind_param($stmt, "is", $pkl_id, $laporan_nama_baru);
            }

            if (mysqli_stmt_execute($stmt)) {
                header("Location: laporan_akhir.php?status=sukses_upload");
                exit();
            } else {
                echo "<script>alert('Gagal menyimpan data file ke database.'); window.history.back();</script>";
                exit();
            }

        } else {
            echo "<script>alert('Terjadi kesalahan saat mengupload file.'); window.history.back();</script>";
            exit();
        }
    } else {
        echo "<script>alert('Error: Pastikan Anda telah memilih file laporan.'); window.history.back();</script>";
        exit();
    }
} else {
    header("Location: dashboard.php");
    exit();
}
?>
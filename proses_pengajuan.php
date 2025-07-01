<?php
require_once 'config/koneksi.php';

// Pastikan hanya mahasiswa yang login yang bisa mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: index.php");
    exit();
}

// Cek apakah tombol 'ajukan' ditekan
if (isset($_POST['ajukan'])) {

    // Ambil semua data dari form
    $mahasiswa_user_id = $_SESSION['user_id'];
    $dosen_pembimbing_user_id = $_POST['dosen_pembimbing_user_id'];
    $nama_perusahaan = mysqli_real_escape_string($koneksi, $_POST['nama_perusahaan']);
    $alamat_perusahaan = mysqli_real_escape_string($koneksi, $_POST['alamat_perusahaan']);
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];

    // --- PROSES UPLOAD FILE PROPOSAL ---
    $proposal_nama = $_FILES['proposal']['name'];
    $proposal_tmp = $_FILES['proposal']['tmp_name'];
    $proposal_error = $_FILES['proposal']['error'];
    $proposal_ukuran = $_FILES['proposal']['size'];

    // Cek apakah ada file yang diupload dan tidak ada error
    if ($proposal_error === 0) {
        // Cek ukuran file (Max 2MB = 2 * 1024 * 1024 bytes)
        if ($proposal_ukuran > 2097152) {
            echo "<script>alert('Ukuran file proposal terlalu besar! Maksimal 2MB.'); window.history.back();</script>";
            exit();
        }

        // Cek ekstensi file (hanya .pdf)
        $ekstensi_file = strtolower(pathinfo($proposal_nama, PATHINFO_EXTENSION));
        if ($ekstensi_file !== 'pdf') {
            echo "<script>alert('Format file proposal harus .PDF!'); window.history.back();</script>";
            exit();
        }

        // Buat nama file baru yang unik untuk menghindari konflik nama file
        $proposal_nama_baru = "proposal_" . time() . "_" . $mahasiswa_user_id . "." . $ekstensi_file;
        $tujuan_upload = "uploads/proposal/" . $proposal_nama_baru;

        // Pindahkan file dari temporary ke folder tujuan
        if (move_uploaded_file($proposal_tmp, $tujuan_upload)) {

            // --- PROSES INSERT DATA KE DATABASE ---
            $sql = "INSERT INTO pkl (mahasiswa_user_id, dosen_pembimbing_user_id, nama_perusahaan, alamat_perusahaan, tanggal_mulai, tanggal_selesai, proposal, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'diajukan')";
            
            $stmt = mysqli_prepare($koneksi, $sql);
            mysqli_stmt_bind_param($stmt, "iisssss", 
                $mahasiswa_user_id, 
                $dosen_pembimbing_user_id, 
                $nama_perusahaan, 
                $alamat_perusahaan, 
                $tanggal_mulai, 
                $tanggal_selesai, 
                $proposal_nama_baru
            );

            if (mysqli_stmt_execute($stmt)) {
                // ===============================================
                // ===== BAGIAN BARU: BUAT NOTIFIKASI UNTUK DOSEN
                // ===============================================
                $nama_mahasiswa = $_SESSION['nama_lengkap'];
                $pesan = "Mahasiswa " . $nama_mahasiswa . " telah mengajukan permintaan bimbingan PKL.";
                $link = "verifikasi_ajuan.php";
                buatNotifikasi($koneksi, $dosen_pembimbing_user_id, $pesan, $link);

                // Arahkan ke dashboard dengan notifikasi sukses
                header("Location: dashboard.php?status=pengajuan_sukses");
                exit();
            } else {
                // Jika gagal menyimpan ke DB
                echo "<script>alert('Gagal menyimpan data ke database. Silakan coba lagi.'); window.history.back();</script>";
                exit();
            }

        } else {
            // Jika gagal memindahkan file
            echo "<script>alert('Terjadi kesalahan saat mengupload file proposal. Silakan coba lagi.'); window.history.back();</script>";
            exit();
        }

    } else {
        // Jika ada error saat upload
        echo "<script>alert('Error: Pastikan Anda telah memilih file proposal.'); window.history.back();</script>";
        exit();
    }
} else {
    // Jika file diakses langsung, tendang ke dashboard
    header("Location: dashboard.php");
    exit();
}
?>
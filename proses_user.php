<?php
require_once 'config/koneksi.php';

// Proteksi, pastikan yang akses adalah admin yang login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Akses ditolak. Anda bukan admin.");
}

// Ambil aksi dari POST atau GET
$aksi = isset($_POST['aksi']) ? $_POST['aksi'] : (isset($_GET['aksi']) ? $_GET['aksi'] : '');

// ==========================================================
// LOGIKA TAMBAH (CREATE) - MODE DARURAT
// ==========================================================
if ($aksi == 'tambah') {
    $username = $_POST['username'];
    $password = $_POST['password']; // Simpan langsung tanpa hash
    $nama_lengkap = $_POST['nama_lengkap'];
    $role = $_POST['role'];
    $nama_perusahaan = ($role == 'dudi') ? $_POST['nama_perusahaan'] : null;

    mysqli_begin_transaction($koneksi);
    try {
        $sql_user = "INSERT INTO users (username, password, nama_lengkap, nama_perusahaan, role) VALUES (?, ?, ?, ?, ?)";
        $stmt_user = mysqli_prepare($koneksi, $sql_user);
        mysqli_stmt_bind_param($stmt_user, "sssss", $username, $password, $nama_lengkap, $nama_perusahaan, $role);
        mysqli_stmt_execute($stmt_user);

        if ($role == 'mahasiswa') {
            $last_user_id = mysqli_insert_id($koneksi);
            $nim = $_POST['nim'];
            $prodi = $_POST['prodi'];
            $sql_mhs = "INSERT INTO mahasiswa (user_id, nim, prodi) VALUES (?, ?, ?)";
            $stmt_mhs = mysqli_prepare($koneksi, $sql_mhs);
            mysqli_stmt_bind_param($stmt_mhs, "iss", $last_user_id, $nim, $prodi);
            mysqli_stmt_execute($stmt_mhs);
        }
        mysqli_commit($koneksi);
        header("Location: kelola_user.php?status=sukses_tambah");
        exit();
    } catch (mysqli_sql_exception $exception) {
        mysqli_rollback($koneksi);
        if ($exception->getCode() == 1062) { header("Location: tambah_user.php?status=gagal_username"); } 
        else { header("Location: tambah_user.php?status=gagal"); }
        exit();
    }
}
// ==========================================================
// LOGIKA EDIT (UPDATE) - MODE DARURAT
// ==========================================================
elseif ($aksi == 'edit') {
    $user_id      = (int)$_POST['user_id'];
    $username     = $_POST['username'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $role         = $_POST['role'];
    $password     = $_POST['password'];
    $nama_perusahaan = ($role == 'dudi') ? $_POST['nama_perusahaan'] : null;

    if (!empty($password)) {
        $sql_user = "UPDATE users SET username = ?, password = ?, nama_lengkap = ?, nama_perusahaan = ?, role = ? WHERE id = ?";
        $stmt_user = mysqli_prepare($koneksi, $sql_user);
        mysqli_stmt_bind_param($stmt_user, "sssssi", $username, $password, $nama_lengkap, $nama_perusahaan, $role, $user_id);
    } else {
        $sql_user = "UPDATE users SET username = ?, nama_lengkap = ?, nama_perusahaan = ?, role = ? WHERE id = ?";
        $stmt_user = mysqli_prepare($koneksi, $sql_user);
        mysqli_stmt_bind_param($stmt_user, "ssssi", $username, $nama_lengkap, $nama_perusahaan, $role, $user_id);
    }
    mysqli_stmt_execute($stmt_user);

    if ($role == 'mahasiswa') {
        $nim = $_POST['nim'];
        $prodi = $_POST['prodi'];
        $sql_cek_mhs = "SELECT user_id FROM mahasiswa WHERE user_id = ?";
        $stmt_cek = mysqli_prepare($koneksi, $sql_cek_mhs);
        mysqli_stmt_bind_param($stmt_cek, "i", $user_id);
        mysqli_stmt_execute($stmt_cek);
        $result_cek = mysqli_stmt_get_result($stmt_cek);
        if (mysqli_num_rows($result_cek) > 0) {
            $sql_mhs = "UPDATE mahasiswa SET nim = ?, prodi = ? WHERE user_id = ?";
            $stmt_mhs = mysqli_prepare($koneksi, $sql_mhs);
            mysqli_stmt_bind_param($stmt_mhs, "ssi", $nim, $prodi, $user_id);
        } else {
            $sql_mhs = "INSERT INTO mahasiswa (user_id, nim, prodi) VALUES (?, ?, ?)";
            $stmt_mhs = mysqli_prepare($koneksi, $sql_mhs);
            mysqli_stmt_bind_param($stmt_mhs, "iss", $user_id, $nim, $prodi);
        }
        mysqli_stmt_execute($stmt_mhs);
    }
    header("Location: kelola_user.php?status=sukses_edit");
    exit();
}
// ==========================================================
// LOGIKA HAPUS (DELETE)
// ==========================================================
elseif ($aksi == 'hapus') {
    $user_id_hapus = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($user_id_hapus == $_SESSION['user_id']) { header("Location: kelola_user.php?status=gagal_hapus_diri"); exit(); }
    mysqli_begin_transaction($koneksi);
    try {
        $sql_get_pkl_ids = "SELECT id FROM pkl WHERE mahasiswa_user_id = ?";
        $stmt_get_pkl = mysqli_prepare($koneksi, $sql_get_pkl_ids);
        mysqli_stmt_bind_param($stmt_get_pkl, "i", $user_id_hapus);
        mysqli_stmt_execute($stmt_get_pkl);
        $result_pkl_ids = mysqli_stmt_get_result($stmt_get_pkl);
        $pkl_ids = [];
        while ($row = mysqli_fetch_assoc($result_pkl_ids)) { $pkl_ids[] = $row['id']; }
        if (!empty($pkl_ids)) {
            $id_list = implode(',', $pkl_ids);
            mysqli_query($koneksi, "DELETE FROM bimbingan WHERE pkl_id IN ($id_list)");
            mysqli_query($koneksi, "DELETE FROM laporan_akhir WHERE pkl_id IN ($id_list)");
        }
        mysqli_query($koneksi, "DELETE FROM pkl WHERE mahasiswa_user_id = $user_id_hapus");
        mysqli_query($koneksi, "DELETE FROM mahasiswa WHERE user_id = $user_id_hapus");
        $sql_delete_user = "DELETE FROM users WHERE id = ?";
        $stmt_user = mysqli_prepare($koneksi, $sql_delete_user);
        mysqli_stmt_bind_param($stmt_user, "i", $user_id_hapus);
        mysqli_stmt_execute($stmt_user);
        mysqli_commit($koneksi);
        header("Location: kelola_user.php?status=sukses_hapus");
        exit();
    } catch (mysqli_sql_exception $exception) {
        mysqli_rollback($koneksi);
        header("Location: kelola_user.php?status=gagal_hapus");
        exit();
    }
}
?>
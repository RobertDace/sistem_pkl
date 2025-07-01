<?php
// 1. Memanggil file koneksi dan memulai session
require_once 'config/koneksi.php';

// 2. Memeriksa apakah data dikirim dari form (metode POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // 3. Mengambil data dari form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 4. Membuat query untuk mencari user berdasarkan username (Prepared Statement)
    // Pastikan kita juga mengambil `nama_perusahaan`
    $sql = "SELECT id, username, password, nama_lengkap, role, nama_perusahaan FROM users WHERE username = ?";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // 5. Memeriksa apakah user ditemukan
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // 6. Memverifikasi password (MODE DARURAT - Perbandingan teks biasa)
        if ($password === $user['password']) {
            
            // Password cocok, login berhasil!
            // Simpan informasi pengguna ke dalam session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['nama_perusahaan'] = $user['nama_perusahaan']; // Untuk user DUDI

            // Arahkan pengguna ke halaman dashboard
            header("Location: dashboard.php");
            exit();

        } else {
            // Jika password salah
            header("Location: index.php?error=password");
            exit();
        }
    } else {
        // Jika username tidak ditemukan
        header("Location: index.php?error=username");
        exit();
    }

} else {
    // Jika file diakses langsung tanpa melalui form, kembalikan ke halaman login
    header("Location: index.php");
    exit();
}
?>
<?php
// File ini hanya untuk menguji fungsi password_verify secara terisolasi

// 1. Password plain text yang kita tahu benar
$password_diketik = 'testing123';

// 2. Hash yang kita tahu benar (saya salin langsung dari screenshot Anda)
$hash_dari_db = '$2y$10$Y1w.1aQIIkE3s3a9yB4kI.V/5aFrnRA0VKYaRBGjVnShSZFg36QEC';

echo "<h1>Tes Fungsi Verifikasi Password</h1>";
echo "<hr>";
echo "Mencoba memverifikasi...<br>";
echo "<b>Password:</b> " . $password_diketik . "<br>";
echo "<b>Hash:</b> " . $hash_dari_db . "<br><br>";

// 3. Menjalankan fungsi verifikasi
if (password_verify($password_diketik, $hash_dari_db)) {
    echo '<h2 style="color: green;">HASIL: Password Benar!</h2>';
    echo '<p>Ini berarti fungsi password_verify di PHP Anda bekerja normal. Jika ini hasilnya, masalahnya pasti 100% ada pada file proses_login.php Anda, kemungkinan ada karakter tak terlihat saat data diterima dari form.</p>';
} else {
    echo '<h2 style="color: red;">HASIL: Password Salah!</h2>';
    echo '<p>Ini sangat tidak biasa. Ini berarti ada masalah fundamental pada instalasi PHP di XAMPP Anda, karena fungsi verifikasi gagal dengan data yang seharusnya benar.</p>';
}
?>
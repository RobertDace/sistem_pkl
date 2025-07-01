<?php
require_once 'config/koneksi.php';

// Atur header agar outputnya berupa JSON
header('Content-Type: application/json');

// Query untuk menghitung jumlah mahasiswa berdasarkan status PKL
$query = "SELECT status, COUNT(*) as jumlah FROM pkl GROUP BY status";
$result = mysqli_query($koneksi, $query);

$data = [
    'labels' => [],
    'datasets' => [[
        'label' => 'Status PKL',
        'data' => [],
        'backgroundColor' => [
            'rgba(255, 99, 132, 0.8)',  // ditolak
            'rgba(54, 162, 235, 0.8)', // diajukan
            'rgba(75, 192, 192, 0.8)',  // disetujui_dosen
            'rgba(255, 206, 86, 0.8)'   // selesai
        ],
        'borderColor' => [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(255, 206, 86, 1)'
        ],
        'borderWidth' => 1
    ]]
];

while ($row = mysqli_fetch_assoc($result)) {
    // Menyesuaikan label agar lebih mudah dibaca
    $label = ucwords(str_replace('_', ' ', $row['status']));
    $data['labels'][] = $label;
    $data['datasets'][0]['data'][] = (int)$row['jumlah'];
}

// Mengubah data array menjadi format JSON
echo json_encode($data);
?>
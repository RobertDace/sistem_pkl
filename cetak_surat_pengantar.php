<?php
require_once 'config/koneksi.php';
require('fpdf/fpdf.php'); // Panggil library FPDF yang sudah di-download

// Pastikan hanya mahasiswa yang login dan PKL nya sudah disetujui
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    die("Akses ditolak. Silakan login sebagai mahasiswa.");
}
$mahasiswa_id = $_SESSION['user_id'];

// Ambil data lengkap yang dibutuhkan untuk surat dari beberapa tabel
$sql = "SELECT 
            u_mhs.nama_lengkap AS nama_mahasiswa,
            m.nim,
            m.prodi,
            pkl.nama_perusahaan,
            pkl.alamat_perusahaan,
            pkl.tanggal_mulai,
            pkl.tanggal_selesai,
            u_dosen.nama_lengkap AS nama_dosen
        FROM pkl
        JOIN users u_mhs ON pkl.mahasiswa_user_id = u_mhs.id
        JOIN mahasiswa m ON pkl.mahasiswa_user_id = m.user_id
        JOIN users u_dosen ON pkl.dosen_pembimbing_user_id = u_dosen.id
        WHERE pkl.mahasiswa_user_id = ? AND pkl.status = 'disetujui_dosen'";

$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "i", $mahasiswa_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    die("Data PKL tidak ditemukan atau statusnya belum disetujui. Anda tidak dapat mencetak surat.");
}

// ===================================================================
// Mulai Membuat Dokumen PDF dengan FPDF
// ===================================================================

class PDF extends FPDF
{
    // Fungsi untuk membuat Header Kop Surat
    function Header()
    {
        // Jika Anda punya file logo, bisa ditambahkan di sini
        // $this->Image('path/logo.png',10,6,30);
        
        // Setting Font
        $this->SetFont('Arial','B',15);
        // Ganti dengan nama institusi Anda
        $this->Cell(0,7,'POLITEKNIK NEGERI SAMARINDA',0,1,'C');
        $this->SetFont('Arial','',10);
        $this->Cell(0,5,'Jalan Cipto Mangunkusumo, Kota Samarinda, Kalimantan Timur',0,1,'C');
        $this->Cell(0,5,'Website: www.polnes.ac.id, Email: info@polnes-kampus.ac.id',0,1,'C');
        // Membuat garis bawah kop surat
        $this->Line(10, 32, 200, 32);
        // Line break
        $this->Ln(10);
    }

    // Fungsi untuk membuat Footer Halaman
    function Footer()
    {
        // Posisi 1.5 cm dari bawah
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        // Nomor halaman
        $this->Cell(0,10,'Halaman '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

// Instansiasi class PDF yang sudah kita modifikasi
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);

// ===================================================================
// Isi Surat
// ===================================================================

// Tanggal surat dibuat (menggunakan tanggal hari ini)
$pdf->Cell(0,7,'Samarinda, ' . date('d F Y'),0,1,'R');
$pdf->Ln(5);

// Tujuan Surat
$pdf->Cell(0,7,'Nomor    : 123/PKL/KAMPUS/VI/2025',0,1);
$pdf->Cell(0,7,'Lampiran : -',0,1);
$pdf->SetFont('Times','B',12);
$pdf->Cell(0,7,'Perihal  : Permohonan Praktik Kerja Lapangan (PKL)',0,1);
$pdf->Ln(7);

$pdf->SetFont('Times','',12);
$pdf->Cell(0,7,'Yth. Pimpinan',0,1);
$pdf->SetFont('Times','B',12);
$pdf->Cell(0,7,htmlspecialchars($data['nama_perusahaan']),0,1);
$pdf->SetFont('Times','',12);
$pdf->MultiCell(0,7,htmlspecialchars($data['alamat_perusahaan']));
$pdf->Cell(0,7,'di Tempat',0,1);
$pdf->Ln(7);

$pdf->Cell(0,7,'Dengan hormat,',0,1);
$pdf->MultiCell(0,7,'Sehubungan dengan kurikulum program studi kami, dengan ini kami mengajukan permohonan agar mahasiswa kami dapat melaksanakan Praktik Kerja Lapangan (PKL) di instansi/perusahaan yang Bapak/Ibu pimpin. Adapun data mahasiswa tersebut adalah sebagai berikut:',0,'J');
$pdf->Ln(5);

// Bagian data mahasiswa
$pdf->Cell(10);
$pdf->Cell(40,7,'Nama',0,0);
$pdf->Cell(5,7,':',0,0);
$pdf->Cell(0,7,htmlspecialchars($data['nama_mahasiswa']),0,1);
$pdf->Cell(10);
$pdf->Cell(40,7,'NIM',0,0);
$pdf->Cell(5,7,':',0,0);
$pdf->Cell(0,7,htmlspecialchars($data['nim']),0,1);
$pdf->Cell(10);
$pdf->Cell(40,7,'Program Studi',0,0);
$pdf->Cell(5,7,':',0,0);
$pdf->Cell(0,7,htmlspecialchars($data['prodi']),0,1);
$pdf->Ln(5);

// Mengubah format tanggal dari database
$tgl_mulai = date('d F Y', strtotime($data['tanggal_mulai']));
$tgl_selesai = date('d F Y', strtotime($data['tanggal_selesai']));

$pdf->MultiCell(0,7,'Pelaksanaan PKL direncanakan akan berlangsung mulai tanggal '.$tgl_mulai.' hingga '.$tgl_selesai.'.',0,'J');
$pdf->Ln(5);
$pdf->MultiCell(0,7,'Demikian surat permohonan ini kami sampaikan. Atas perhatian dan kerja sama Bapak/Ibu, kami ucapkan terima kasih.',0,'J');
$pdf->Ln(15);

// Bagian Tanda Tangan
$pdf->Cell(120); // Geser ke kanan untuk posisi tanda tangan
$pdf->Cell(0,7,'Hormat kami,',0,1,'C');
$pdf->Cell(120);
$pdf->Cell(0,7,'Dosen Pembimbing',0,1,'C');
$pdf->Ln(20); // Jarak untuk TTD dan stempel
$pdf->Cell(120);
$pdf->SetFont('Times','BU',12);
$pdf->Cell(0,7,htmlspecialchars($data['nama_dosen']),0,1,'C');
$pdf->SetFont('Times','',12);
$pdf->Cell(120);
$pdf->Cell(0,7,'NIDN: 123456789',0,1,'C');


// ===================================================================
// Menghasilkan file PDF
// 'I' : Tampilkan di browser (inline)
// 'D' : Munculkan dialog download
// =
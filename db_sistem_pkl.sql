-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 27 Jun 2025 pada 00.05
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_sistem_pkl`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `bimbingan`
--

CREATE TABLE `bimbingan` (
  `id` int(11) NOT NULL,
  `pkl_id` int(11) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `kegiatan` text DEFAULT NULL,
  `status_verifikasi` enum('belum','diverifikasi') DEFAULT 'belum',
  `catatan_dosen` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `laporan_akhir`
--

CREATE TABLE `laporan_akhir` (
  `id` int(11) NOT NULL,
  `pkl_id` int(11) DEFAULT NULL,
  `file_laporan` varchar(255) DEFAULT NULL,
  `nilai_dosen` float DEFAULT NULL,
  `nilai_dudi` float DEFAULT NULL,
  `nilai_akhir` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `laporan_akhir`
--

INSERT INTO `laporan_akhir` (`id`, `pkl_id`, `file_laporan`, `nilai_dosen`, `nilai_dudi`, `nilai_akhir`) VALUES
(1, 3, 'laporan_akhir_1750967039_6.pdf', 85, 90, 87),
(2, 1, 'laporan_akhir_1750969034_4.pdf', 80, 95, 86);

-- --------------------------------------------------------

--
-- Struktur dari tabel `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nim` varchar(20) NOT NULL,
  `prodi` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `mahasiswa`
--

INSERT INTO `mahasiswa` (`id`, `user_id`, `nim`, `prodi`) VALUES
(1, 4, '226151003', 'Teknik Informatika'),
(2, 5, '11223302', 'Sistem Informasi'),
(3, 6, '11223303', 'Teknik Informatika'),
(4, 7, '226151004', 'Teknik Informatika');

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id` int(11) NOT NULL,
  `user_id_tujuan` int(11) NOT NULL,
  `pesan` text NOT NULL,
  `status` enum('belum_dibaca','sudah_dibaca') DEFAULT 'belum_dibaca',
  `link_terkait` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `notifikasi`
--

INSERT INTO `notifikasi` (`id`, `user_id_tujuan`, `pesan`, `status`, `link_terkait`, `created_at`) VALUES
(1, 2, 'Mahasiswa Romi Maulana telah mengajukan permintaan bimbingan PKL.', 'sudah_dibaca', 'verifikasi_ajuan.php', '2025-06-26 20:16:24'),
(2, 4, 'Pengajuan PKL Anda telah DISETUJUI oleh dosen pembimbing.', 'sudah_dibaca', 'laporan_akhir.php', '2025-06-26 20:16:47'),
(3, 2, 'Mahasiswa Alfian Robit Nadifi Masyhudi telah mengajukan permintaan bimbingan PKL.', 'sudah_dibaca', 'verifikasi_ajuan.php', '2025-06-26 21:58:57');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pkl`
--

CREATE TABLE `pkl` (
  `id` int(11) NOT NULL,
  `mahasiswa_user_id` int(11) DEFAULT NULL,
  `dosen_pembimbing_user_id` int(11) DEFAULT NULL,
  `dudi_id` int(11) DEFAULT NULL,
  `nama_perusahaan` varchar(100) DEFAULT NULL,
  `alamat_perusahaan` text DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `proposal` varchar(255) DEFAULT NULL,
  `status` enum('diajukan','disetujui_dosen','ditolak','selesai') DEFAULT 'diajukan',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pkl`
--

INSERT INTO `pkl` (`id`, `mahasiswa_user_id`, `dosen_pembimbing_user_id`, `dudi_id`, `nama_perusahaan`, `alamat_perusahaan`, `tanggal_mulai`, `tanggal_selesai`, `proposal`, `status`, `created_at`) VALUES
(1, 4, 2, NULL, 'PT. Teknologi Maju Bersama', 'Jl. Jenderal Sudirman No. 123, Jakarta', '2025-08-01', '2025-11-01', 'proposal_contoh_1.pdf', 'disetujui_dosen', '2025-06-26 18:30:54'),
(2, 5, 3, NULL, 'CV. Cipta Karya Digital', 'Jl. Gajah Mada No. 45, Surabaya', '2025-08-15', '2025-11-15', 'proposal_contoh_2.pdf', 'diajukan', '2025-06-26 18:30:54'),
(3, 6, 2, NULL, 'Dinas Komunikasi dan Informatika Kota Samarinda', 'Jl. Kesuma Bangsa No. 1, Samarinda', '2025-07-20', '2025-10-20', 'proposal_contoh_3.pdf', 'disetujui_dosen', '2025-06-26 18:30:54'),
(4, 4, 2, NULL, 'POLNES', 'Jl. Cipto Mangun Kusumo', '2025-06-17', '2025-07-12', 'proposal_1750968984_4.pdf', 'disetujui_dosen', '2025-06-26 20:16:24'),
(5, 7, 2, NULL, 'UWGM SAMARINDA', 'Jl. Wahid Hasyim 2 No.28, Sempaja Sel., Kec. Samarinda Utara, Kota Samarinda, Kalimantan Timur 75243', '2025-06-10', '2025-08-29', 'proposal_1750975137_7.pdf', 'diajukan', '2025-06-26 21:58:57');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `nama_perusahaan` varchar(100) DEFAULT NULL,
  `role` enum('mahasiswa','dosen','dudi','admin') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `nama_perusahaan`, `role`, `created_at`) VALUES
(1, 'admin', 'admin123', 'Administrator Web', NULL, 'admin', '2025-06-26 18:33:06'),
(2, 'dosen1', 'password123', 'Fajerin Biabdillah, M.Kom.', NULL, 'dosen', '2025-06-26 18:33:06'),
(3, 'dosen2', 'password123', 'Aam Shodiqul Munir,M.Kom', NULL, 'dosen', '2025-06-26 18:33:06'),
(4, 'mahasiswa1', 'password123', 'Romi Maulana', NULL, 'mahasiswa', '2025-06-26 18:33:06'),
(5, 'mahasiswa2', '$2y$10$wAXCcnG.hLySBv0m8iBEIuA6zM.zP.W6sVfvyw2OftmI5fLST1aJq', 'Rina Wati', NULL, 'mahasiswa', '2025-06-26 18:33:06'),
(6, 'mahasiswa3', 'password123', 'Eko Nugroho', NULL, 'mahasiswa', '2025-06-26 18:33:06'),
(7, 'mahasiswa4', 'password123', 'Alfian Robit Nadifi Masyhudi', NULL, 'mahasiswa', '2025-06-26 19:36:28'),
(8, 'mitra dudi', 'password123', 'Mulyono', 'PT. Teknologi Maju Bersama', 'dudi', '2025-06-26 19:58:21');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `bimbingan`
--
ALTER TABLE `bimbingan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pkl_id` (`pkl_id`);

--
-- Indeks untuk tabel `laporan_akhir`
--
ALTER TABLE `laporan_akhir`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pkl_id` (`pkl_id`);

--
-- Indeks untuk tabel `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nim` (`nim`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id_tujuan` (`user_id_tujuan`);

--
-- Indeks untuk tabel `pkl`
--
ALTER TABLE `pkl`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mahasiswa_user_id` (`mahasiswa_user_id`),
  ADD KEY `dosen_pembimbing_user_id` (`dosen_pembimbing_user_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `bimbingan`
--
ALTER TABLE `bimbingan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `laporan_akhir`
--
ALTER TABLE `laporan_akhir`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `mahasiswa`
--
ALTER TABLE `mahasiswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `pkl`
--
ALTER TABLE `pkl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `bimbingan`
--
ALTER TABLE `bimbingan`
  ADD CONSTRAINT `bimbingan_ibfk_1` FOREIGN KEY (`pkl_id`) REFERENCES `pkl` (`id`);

--
-- Ketidakleluasaan untuk tabel `laporan_akhir`
--
ALTER TABLE `laporan_akhir`
  ADD CONSTRAINT `laporan_akhir_ibfk_1` FOREIGN KEY (`pkl_id`) REFERENCES `pkl` (`id`);

--
-- Ketidakleluasaan untuk tabel `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD CONSTRAINT `mahasiswa_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD CONSTRAINT `notifikasi_ibfk_1` FOREIGN KEY (`user_id_tujuan`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pkl`
--
ALTER TABLE `pkl`
  ADD CONSTRAINT `pkl_ibfk_1` FOREIGN KEY (`mahasiswa_user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `pkl_ibfk_2` FOREIGN KEY (`dosen_pembimbing_user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

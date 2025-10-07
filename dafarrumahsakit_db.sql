-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 06, 2025 at 02:17 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dafarrumahsakit_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int UNSIGNED NOT NULL,
  `location_id` int UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `specialty` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` int UNSIGNED NOT NULL,
  `type` enum('rumah_sakit','puskesmas','klinik') NOT NULL,
  `name` varchar(150) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `services` text,
  `open_time` time DEFAULT NULL,
  `close_time` time DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `doctors` text,
  `image` varchar(255) DEFAULT NULL,
  `maps_link` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `type`, `name`, `address`, `services`, `open_time`, `close_time`, `phone`, `created_at`, `doctors`, `description`, `image`, `maps_link`) VALUES
(6, 'klinik', 'Klinik Ananda', 'Depan dieng', 'ENAK ENAK \r\nMANTAP MANTAP', '11:11:00', '11:11:00', '0892222222', '2025-10-06 14:02:43', 'Syafri', 'linik adalah fasilitas pelayanan kesehatan yang menyelenggarakan dan menyediakan pelayanan medis dasar dan/atau spesialistik yang diselenggarakan oleh lebih dari satu tenaga kesehatan dan dipimpin oleh seorang dokter atau dokter gigi.', '68e3cc03cb89b.jpeg', 'https://maps.app.goo.gl/wzjVG8VXmJx3wnVM7'),
(7, 'rumah_sakit', 'Rumah Sakit Bunda', 'JL ANGKATAN 45', 'MANTAP MANTAPP\r\nENAK ENAK', '11:11:00', '11:01:00', '222222222', '2025-10-06 14:14:17', 'DOG DOG', '1. Klinik Umum\r\nMenyediakan layanan medis dasar seperti pemeriksaan kesehatan, pengobatan penyakit ringan, pemeriksaan tekanan darah, demam, flu, hingga luka ringan. Klinik umum biasanya merupakan klinik pratama.\r\n\r\n2. Klinik Gigi\r\nKhusus menyediakan pelayanan kesehatan gigi dan mulut seperti:\r\n\r\nTambal gigi\r\nPembersihan karang gigi\r\nPencabutan gigi\r\nPerawatan saluran akar\r\nPemasangan kawat gigi dan gigi tiruan\r\nKlinik gigi bisa berupa klinik pratama atau klinik utama tergantung skala dan fasilitasnya.\r\n\r\n3. Klinik Kulit dan Kelamin\r\nMelayani perawatan dan pengobatan berbagai masalah kulit serta penyakit kelamin, seperti:\r\n\r\nJerawat, eksim, psoriasis\r\nInfeksi menular seksual\r\nAlergi kulit\r\nPerawatan anti-aging dan estetika\r\nKlinik ini umumnya ditangani oleh dokter spesialis kulit dan kelamin (Sp.KK).\r\n\r\n4. Klinik Andrologi\r\nFokus pada kesehatan reproduksi dan seksual pria, termasuk penanganan:\r\n\r\nDisfungsi ereksi\r\nEjakulasi dini\r\nGangguan hormon\r\nInfertilitas pria\r\nKlinik andrologi biasanya menjadi bagian dari klinik utama yang memiliki layanan spesialis.\r\n\r\n5. Klinik Ginekologi\r\nMenangani kesehatan organ reproduksi wanita seperti:\r\n\r\nPemeriksaan kehamilan\r\nMasalah haid\r\nInfeksi organ intim wanita\r\nProgram kehamilan\r\nBiasanya ditangani oleh dokter spesialis kebidanan dan kandungan (Sp.OG).', '68e3ceb995240.jpeg', 'https://maps.app.goo.gl/AWRwZVZ3MkDqWHe29');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `role`, `created_at`) VALUES
(2, 'admin', '$2y$10$/oDvsxvsG.jSOPT6ZYCd3ObGFbvB6xF4obPZmw4cUq0SOzU4qageG', 'admin', '2025-10-06 12:31:08'),
(3, 'user', '$2y$10$h7kNvHgZPUzuYvzTs.E2jehc0ENidQ7XKM1M1I3tzDFv4eDrVcwuG', 'user', '2025-10-06 12:31:38'),
(4, 'alfazri', '$2y$10$X7BpcmWZ9WqmT3W1lpowRun/jbNdTjt24v6WuPIijE/yejfTeW//y', 'user', '2025-10-06 14:08:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `location_id` (`location_id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `doctors_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

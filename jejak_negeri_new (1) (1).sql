-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 04, 2026 at 12:07 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jejak_negeri_new`
--

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `content` text COLLATE utf8mb4_general_ci NOT NULL,
  `category` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `image_url` text COLLATE utf8mb4_general_ci,
  `author_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `title`, `content`, `category`, `image_url`, `author_id`, `created_at`) VALUES
(3, 'Dieng Culture Festival', 'Dieng Culture Festival adalah festival budaya tahunan di Dieng yang menampilkan tradisi, seni, musik, dan ritual ruwatan anak gimbal khas masyarakat Dieng. Festival ini juga menghadirkan pertunjukan budaya, pesta lampion, serta panorama alam yang indah sehingga menjadi daya tarik wisata populer. \r\n\r\nSalah satu acara utama dalam festival ini adalah ritual ruwatan anak gimbal, yaitu tradisi pemotongan rambut anak berambut gimbal yang dipercaya sebagai titipan leluhur oleh masyarakat Dieng. Prosesi sakral ini dilakukan dengan berbagai rangkaian adat dan doa sebagai bentuk penghormatan terhadap budaya serta tradisi nenek moyang yang masih dijaga hingga sekarang.\r\n\r\nSelain ritual budaya, Dieng Culture Festival juga menghadirkan berbagai pertunjukan seni tradisional, konser musik jazz di atas awan, pesta lampion, kembang api, dan pameran UMKM lokal. Suasana festival menjadi semakin menarik dengan latar pemandangan pegunungan, udara sejuk khas Dieng, serta keindahan sunrise yang memukau.', '', 'uploads/article_1779625383_6a12eda79588f.jpg', 1, '2026-05-24 12:23:03');

-- --------------------------------------------------------

--
-- Table structure for table `business_items`
--

CREATE TABLE `business_items` (
  `id` int NOT NULL,
  `parent_item_id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `price` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `image_url` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(1, 'Wisata Alam', '2026-06-03 06:33:50'),
(2, 'Budaya & Tradisi', '2026-06-03 06:33:50'),
(3, 'Kuliner', '2026-06-03 06:33:50'),
(4, 'Sejarah', '2026-06-03 06:33:50'),
(5, 'Event', '2026-06-03 06:49:36');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int NOT NULL,
  `item_id` int NOT NULL,
  `user_id` int NOT NULL,
  `comment_text` text COLLATE utf8mb4_general_ci NOT NULL,
  `likes` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `item_id`, `user_id`, `comment_text`, `likes`, `created_at`) VALUES
(1, 2, 6, 'Tempat ini sangat indah, saya senang sekalii!!!', 1, '2026-06-03 07:17:27');

-- --------------------------------------------------------

--
-- Table structure for table `comment_likes`
--

CREATE TABLE `comment_likes` (
  `id` int NOT NULL,
  `comment_id` int NOT NULL,
  `user_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comment_likes`
--

INSERT INTO `comment_likes` (`id`, `comment_id`, `user_id`) VALUES
(1, 1, 6);

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int NOT NULL,
  `category` enum('wisata','kuliner','penginapan') COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `price` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `hours` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sosmed` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `maps_url` text COLLATE utf8mb4_general_ci,
  `image_url` text COLLATE utf8mb4_general_ci,
  `whatsapp` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `menu` text COLLATE utf8mb4_general_ci,
  `room_types` text COLLATE utf8mb4_general_ci,
  `facilities` text COLLATE utf8mb4_general_ci,
  `status` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'Buka',
  `announcement` text COLLATE utf8mb4_general_ci,
  `owner_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `category`, `name`, `description`, `price`, `hours`, `location`, `sosmed`, `maps_url`, `image_url`, `whatsapp`, `menu`, `room_types`, `facilities`, `status`, `announcement`, `owner_id`, `created_at`) VALUES
(2, 'wisata', 'Kawah Sikidang', 'Kawah vulkanik aktif yang unik karena bisa berpindah-pindah tempat seperti kijang.', 'Rp 15.000', '07.00 - 17.00', 'Baktiyoso, Dieng', '@sikidang_dieng', 'https://goo.gl/maps/y', 'uploads/Kawah Sikidang.jpg', '628123456780', NULL, NULL, NULL, 'Buka', NULL, 3, '2026-05-24 12:20:23'),
(3, 'wisata', 'Candi Arjuna', 'Kompleks candi Hindu tertua di Jawa yang dibangun pada masa kerajaan Mataram Kuno.', 'Rp 15.000', '07.30 - 17.00', 'Dieng Kulon', '@candi_arjuna', 'https://goo.gl/maps/z', 'uploads/Candi Arjuna.jpg', '628123456781', NULL, NULL, NULL, 'Buka', NULL, 2, '2026-05-24 12:20:23'),
(4, 'kuliner', 'Mie Ongklok ', 'Mie khas Wonosobo dengan kuah kental gurih, sate sapi, dan tempe kemul.', 'Rp 25.000', '09.00 - 21.00', 'Wonosobo', '@mieongklok_longkrang', 'https://goo.gl/maps/d', 'uploads/Mie Ongklok.jpg', '628123456785', '', '', '', 'Buka', NULL, 4, '2026-05-24 12:20:23'),
(5, 'penginapan', 'Villa Dieng ', 'Penginapan nyaman dengan fasilitas air panas dan view gunung yang indah.', 'Rp 350.000', 'Check-in 14.00', 'Pusat Dieng', '@diengasri', 'https://goo.gl/maps/i', 'uploads/Villa.jpg', '628123456790', '', '', '', 'Buka', NULL, 5, '2026-05-24 12:20:23');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `nama_lengkap` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('user','admin','owner') COLLATE utf8mb4_general_ci DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama_lengkap`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Administrator', 'admin', 'admin@dieng.com', '$2y$10$8WKj985IwADSAi2oulctw..eElR3EnzAbyr6Dji0QBmdWiqQzSGzm', 'admin', '2026-05-24 12:20:23'),
(2, 'jennie', 'CandiArjuna', 'canadiarjuna@gmail.com', '$2y$10$UZLM85vjzq7qJHMqONP8XOF33PwxxMWwO.VJM5tZLRDk3qwxbsvxW', 'owner', '2026-05-24 12:24:21'),
(3, 'Lisa', 'KawahSikidang', 'KawahSikidang@gmail.com', '$2y$10$vQR3sxauwMQkrTJntxim0uMHVGWhH3E6rRVU8CMaYcYb96WTPGcUS', 'owner', '2026-05-24 12:25:02'),
(4, 'Jiso', 'MieOngklok', 'MieOngklok@gmail.com', '$2y$10$1f46ihdglP7UaIuJ0n05S.uMZqF0MNrNJ.Fx8EiKj78WwkW7DCsy2', 'owner', '2026-05-24 12:25:34'),
(5, 'Rose', 'VillaDieng', 'VillaDieng@gmail.com', '$2y$10$215P8BR8gbZP/oZJVZcCGe4HFJ1PQvI8UhAye2puYtOeS4ZRXpMsW', 'owner', '2026-05-24 12:26:04'),
(6, 'Irunn', 'irun', 'irun@gmail.com', '$2y$10$dEnNVUIPfsSvokPD5gPFYun/4uhzrnHltj6g3JIAefGQZpRMFaylu', 'user', '2026-06-03 07:09:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author_id` (`author_id`);

--
-- Indexes for table `business_items`
--
ALTER TABLE `business_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_item_id` (`parent_item_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `comment_likes`
--
ALTER TABLE `comment_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`comment_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `business_items`
--
ALTER TABLE `business_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `comment_likes`
--
ALTER TABLE `comment_likes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `business_items`
--
ALTER TABLE `business_items`
  ADD CONSTRAINT `business_items_ibfk_1` FOREIGN KEY (`parent_item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comment_likes`
--
ALTER TABLE `comment_likes`
  ADD CONSTRAINT `comment_likes_ibfk_1` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comment_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 13, 2026 at 04:44 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `resolvex_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `area` varchar(20) NOT NULL,
  `building` varchar(100) NOT NULL,
  `block` varchar(100) NOT NULL,
  `level` varchar(20) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `location_description` text DEFAULT NULL,
  `infra_category` varchar(100) DEFAULT NULL,
  `infra_subcategory` varchar(100) DEFAULT NULL,
  `damage_type` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `evidence_file` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `complaint_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`id`, `user_id`, `area`, `building`, `block`, `level`, `location`, `location_description`, `infra_category`, `infra_subcategory`, `damage_type`, `description`, `evidence_file`, `status`, `complaint_date`) VALUES
(1, 1, 'inside', 'D0101', 'D0101A', '1', 'Bilik Penyediaan Makanan', 'Rosak tempat masak', NULL, NULL, 'Furniture Damage', 'macam tulah', 'REV_6a2d5d7f693732.15091766.png', 'Pending', '2026-06-13 13:39:11');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'KHIRUNNISYA', 'nisya123@gmail.com', '$2y$10$wl4VVDUGO.YTSitkhpIBsu4nTUOuW4XdoP7ngmiGtjFTWDWNfv9j.', '2026-06-13 13:01:56'),
(2, 'fakiah', 'fatihahduereh@gmail.com', '$2y$10$oZ.HWTSDAshjMeswud/NmeFxRABXnT.uE6SgTTeqZc0M50ak76AQ.', '2026-06-13 13:08:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `complaints_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 19, 2018 at 10:28 PM
-- Server version: 5.7.21-0ubuntu0.16.04.1
-- PHP Version: 7.2.2-3+ubuntu16.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dangdung_dd`
--

-- --------------------------------------------------------

--
-- Table structure for table `api_key`
--

CREATE TABLE `api_key` (
  `user_id` varchar(50) CHARACTER SET utf8mb4 NOT NULL,
  `token` varchar(50) CHARACTER SET utf8mb4 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `rollup`
--

CREATE TABLE `rollup` (
  `user_id` varchar(50) NOT NULL,
  `roll_day` int(10) UNSIGNED NOT NULL,
  `first` time DEFAULT NULL,
  `last` time DEFAULT NULL,
  `note` text,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `rules`
--

CREATE TABLE `rules` (
  `id` int(10) UNSIGNED NOT NULL,
  `text` text NOT NULL,
  `functions` varchar(50) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `rules`
--

INSERT INTO `rules` (`id`, `text`, `functions`) VALUES
(1, 'Chuyển thành chuỗi đảo ngược', 'strrev'),
(2, 'Chuyển thành chuỗi chữ thường đảo ngược', 'strrev,strtolower'),
(3, 'Chuyển thành chuỗi chữ hoa', 'strtoupper'),
(4, 'Chuyển thành chuỗi chữ hoa đảo ngược', 'strrev,strtoupper'),
(5, 'Chữ thường => chữ hoa và ngược lại', 'lowerupper'),
(6, 'Chuyển thành chuỗi đảo ngược, chữ thường => chữ hoa và ngược lại', 'strrev,lowerupper');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `updated_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `updated_time`) VALUES
('1908448496062033', 'The Tien Nguyen', '2018-01-25 15:38:20'),
('2208382589387981', 'Nguyễn Đăng Dũng', '2018-01-25 15:27:21'),
('2440402312852176', 'Hoàng Liên', '2018-01-25 15:38:25'),
('835601776625598', 'Văn Tập', '2018-01-25 15:38:42'),
('846831985467200', 'Quân Lương', '2018-01-25 15:38:40'),
('859319544224236', 'Nguyễn Thành', '2018-01-25 18:26:42'),
('876384689190057', 'Vương Sơn', '2018-01-25 17:06:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `api_key`
--
ALTER TABLE `api_key`
  ADD KEY `FK_UserID` (`user_id`);

--
-- Indexes for table `rollup`
--
ALTER TABLE `rollup`
  ADD PRIMARY KEY (`user_id`,`roll_day`);

--
-- Indexes for table `rules`
--
ALTER TABLE `rules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `rules`
--
ALTER TABLE `rules`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `api_key`
--
ALTER TABLE `api_key`
  ADD CONSTRAINT `FK_UserID` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `rollup`
--
ALTER TABLE `rollup`
  ADD CONSTRAINT `FK1_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

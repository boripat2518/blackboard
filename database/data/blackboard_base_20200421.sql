-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 21, 2020 at 09:21 PM
-- Server version: 5.7.29-0ubuntu0.18.04.1
-- PHP Version: 7.2.24-0ubuntu0.18.04.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `blackboard_base`
--

-- --------------------------------------------------------

--
-- Table structure for table `categorys`
--

CREATE TABLE `categorys` (
  `id` int(11) NOT NULL COMMENT 'auto number',
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'title name',
  `icon` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'icon url',
  `cover` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'cover url',
  `active` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'active flag',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'create timestamp',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'update timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `categorys`
--

INSERT INTO `categorys` (`id`, `title`, `icon`, `cover`, `active`, `created_at`, `updated_at`) VALUES
(101, 'การออกแบบ', 'https://raw.githubusercontent.com/blackboard-coding/blackboard-icons/master/src/images/05Categories/All%20icon/01Design.png', 'https://freelance.boripatp.com/images/category/101.jpg', 1, '2020-04-06 06:46:02', '2020-04-06 06:46:02'),
(102, 'การถ่ายภาพ', 'https://raw.githubusercontent.com/blackboard-coding/blackboard-icons/master/src/images/05Categories/All%20icon/02Photograph.png', 'https://freelance.boripatp.com/images/category/102.jpg', 1, '2020-04-06 06:46:57', '2020-04-06 06:46:57'),
(103, 'ดนตรีและเพลง', 'https://raw.githubusercontent.com/blackboard-coding/blackboard-icons/master/src/images/05Categories/All%20icon/03Music.png', 'https://freelance.boripatp.com/images/category/103.jpg', 1, '2020-04-06 06:47:57', '2020-04-06 06:47:57'),
(104, 'ไอทีและซอฟต์แวร์', 'https://raw.githubusercontent.com/blackboard-coding/blackboard-icons/master/src/images/05Categories/All%20icon/04IT.png', 'https://freelance.boripatp.com/images/category/104.jpg', 1, '2020-04-06 06:49:11', '2020-04-06 06:49:11'),
(105, 'ศิลปะและการวาดการ์ตูน', 'https://raw.githubusercontent.com/blackboard-coding/blackboard-icons/master/src/images/05Categories/All%20icon/05ArtsToon.png', 'https://freelance.boripatp.com/images/category/105.jpg', 1, '2020-04-06 06:49:51', '2020-04-06 06:49:51'),
(106, 'ภาษา', 'https://raw.githubusercontent.com/blackboard-coding/blackboard-icons/master/src/images/05Categories/All%20icon/06Language.png', 'https://freelance.boripatp.com/images/category/106.jpg', 1, '2020-04-06 06:51:27', '2020-04-06 06:51:27'),
(107, 'กีฬา', 'https://raw.githubusercontent.com/blackboard-coding/blackboard-icons/master/src/images/05Categories/All%20icon/07Sports.png', 'https://freelance.boripatp.com/images/category/107.jpg', 1, '2020-04-06 06:52:11', '2020-04-06 06:52:11'),
(108, 'ทำอาหารและของหวาน', 'https://raw.githubusercontent.com/blackboard-coding/blackboard-icons/master/src/images/05Categories/All%20icon/08Cook.png', 'https://freelance.boripatp.com/images/category/108.jpg', 1, '2020-04-06 06:52:44', '2020-04-06 06:52:44'),
(109, 'งานฝีมือ', 'https://raw.githubusercontent.com/blackboard-coding/blackboard-icons/master/src/images/05Categories/All%20icon/09HandMade.png', 'https://freelance.boripatp.com/images/category/109.jpg', 1, '2020-04-06 06:53:15', '2020-04-06 06:53:15'),
(110, 'ร้องเพลงและการแสดง', 'https://raw.githubusercontent.com/blackboard-coding/blackboard-icons/master/src/images/05Categories/All%20icon/10SingAct.png', 'https://freelance.boripatp.com/images/category/110.jpg', 1, '2020-04-06 06:53:15', '2020-04-06 07:04:09'),
(111, 'อาชีพงานช่าง', 'https://raw.githubusercontent.com/blackboard-coding/blackboard-icons/master/src/images/05Categories/All%20icon/11techicain.png', 'https://freelance.boripatp.com/images/category/111.jpg', 1, '2020-04-06 06:53:15', '2020-04-06 07:04:09'),
(112, 'การบ้านระดับประถม', 'https://raw.githubusercontent.com/blackboard-coding/blackboard-icons/master/src/images/05Categories/All%20icon/12Primary%20homework.png', 'https://freelance.boripatp.com/images/category/112.jpg', 1, '2020-04-06 06:53:15', '2020-04-06 07:04:09'),
(113, 'การบ้านระดับมัธยม', 'https://raw.githubusercontent.com/blackboard-coding/blackboard-icons/master/src/images/05Categories/All%20icon/13High%20school%20homework.png', 'https://freelance.boripatp.com/images/category/113.jpg', 1, '2020-04-06 06:53:15', '2020-04-06 07:04:09'),
(114, 'อาชีวะ', 'https://raw.githubusercontent.com/blackboard-coding/blackboard-icons/master/src/images/05Categories/All%20icon/14Vocation.png', 'https://freelance.boripatp.com/images/category/114.jpg', 1, '2020-04-06 06:53:15', '2020-04-06 07:04:09'),
(115, 'พาณิชยการ บัญชี การเงิน', 'https://raw.githubusercontent.com/blackboard-coding/blackboard-icons/master/src/images/05Categories/All%20icon/15Commerce.png', 'https://freelance.boripatp.com/images/category/115.jpg', 1, '2020-04-06 06:53:15', '2020-04-06 07:04:09'),
(116, 'การตลาด', 'https://raw.githubusercontent.com/blackboard-coding/blackboard-icons/master/src/images/05Categories/All%20icon/16Marketing.png', 'https://freelance.boripatp.com/images/category/116.jpg', 1, '2020-04-06 06:53:15', '2020-04-06 07:04:09'),
(117, 'ดูดวง ฮวงจุ้ย', 'https://raw.githubusercontent.com/blackboard-coding/blackboard-icons/master/src/images/05Categories/All%20icon/17Horoscope.png', 'https://freelance.boripatp.com/images/category/117.jpg', 1, '2020-04-06 06:53:15', '2020-04-06 07:04:09'),
(118, 'การเกษตร', 'https://raw.githubusercontent.com/blackboard-coding/blackboard-icons/master/src/images/05Categories/All%20icon/18Agriculture.png', 'https://freelance.boripatp.com/images/category/118.jpg', 1, '2020-04-06 06:53:15', '2020-04-06 07:04:09'),
(119, 'ธรรมะ พระไตรปิฏก', 'https://raw.githubusercontent.com/blackboard-coding/blackboard-icons/master/src/images/05Categories/All%20icon/19Religion.png', 'https://freelance.boripatp.com/images/category/119.jpg', 1, '2020-04-06 06:53:15', '2020-04-06 07:04:09'),
(120, 'อื่นๆ', 'https://raw.githubusercontent.com/blackboard-coding/blackboard-icons/master/src/images/05Categories/All%20icon/20Etc.png', 'https://freelance.boripatp.com/images/category/120.jpg', 1, '2020-04-06 06:53:15', '2020-04-06 07:04:09');

-- --------------------------------------------------------

--
-- Table structure for table `lesson_favorites`
--

CREATE TABLE `lesson_favorites` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'auto number',
  `room_id` int(10) UNSIGNED NOT NULL COMMENT 'ref to room',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT 'ref to user',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'created timestamp',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'updated timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='following room';

-- --------------------------------------------------------

--
-- Table structure for table `lesson_infos`
--

CREATE TABLE `lesson_infos` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'auto number',
  `cat_id` int(10) UNSIGNED NOT NULL COMMENT 'ref to category',
  `title` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'title',
  `note` text COLLATE utf8_unicode_ci COMMENT 'description',
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'type of lesson',
  `tag` text COLLATE utf8_unicode_ci COMMENT 'tag of lesson',
  `cover` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'cover image',
  `price` decimal(7,2) NOT NULL COMMENT 'regular price',
  `net` decimal(7,2) NOT NULL COMMENT 'net price',
  `active` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'active flag',
  `room_id` int(10) UNSIGNED NOT NULL COMMENT 'ref to room',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'created timestamp',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT 'updated timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='lesson information';

-- --------------------------------------------------------

--
-- Table structure for table `lesson_purchases`
--

CREATE TABLE `lesson_purchases` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'auto number',
  `lesson_id` int(10) UNSIGNED NOT NULL COMMENT 'ref to lesson',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT 'ref to user',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'created timestamp',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'updated timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='purchased lesson';

-- --------------------------------------------------------

--
-- Table structure for table `lesson_rates`
--

CREATE TABLE `lesson_rates` (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT 'auto number',
  `lesson_id` int(10) UNSIGNED NOT NULL COMMENT 'ref to lesson',
  `rate` decimal(3,2) UNSIGNED NOT NULL DEFAULT '5.00' COMMENT 'rate value',
  `comment` text COLLATE utf8_unicode_ci COMMENT 'comments',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT 'ref to user',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'created timestamp',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'updated timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='lesson rates and comment';

-- --------------------------------------------------------

--
-- Table structure for table `lesson_reports`
--

CREATE TABLE `lesson_reports` (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT 'auto number',
  `lesson_id` int(10) UNSIGNED NOT NULL COMMENT 'ref to lesson',
  `report_id` int(10) UNSIGNED NOT NULL COMMENT 'ref to report item',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT 'ref to user',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'created timestamp',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'updated timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='report lesson';

-- --------------------------------------------------------

--
-- Table structure for table `lesson_videos`
--

CREATE TABLE `lesson_videos` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'auto number',
  `lesson_id` int(10) UNSIGNED NOT NULL COMMENT 'ref to lesson',
  `link` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'path to video',
  `seq` int(10) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'order number',
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'dummy' COMMENT 'video title',
  `note` text COLLATE utf8_unicode_ci COMMENT 'video note',
  `active` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'active flag',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'created timestamp',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'updated timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='video list in lesson';

-- --------------------------------------------------------

--
-- Table structure for table `log_wallets`
--

CREATE TABLE `log_wallets` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'auto number',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT 'ref to user',
  `current` decimal(7,2) NOT NULL DEFAULT '0.00' COMMENT 'current cost',
  `type` varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'TOP UP' COMMENT 'type of updated (TOP UP, BUY, ADJUST, WITHDRAW)',
  `note` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'history note',
  `files` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'file upload',
  `updated` decimal(7,2) NOT NULL DEFAULT '0.00' COMMENT 'update cost',
  `status` tinyint(3) UNSIGNED NOT NULL COMMENT 'status flag',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'created timestamp',
  `created_uid` int(10) UNSIGNED NOT NULL COMMENT 'ref to admin',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'updated timestamp',
  `updated_uid` int(10) UNSIGNED NOT NULL DEFAULT '99' COMMENT 'ref to admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='transection for wallets';

-- --------------------------------------------------------

--
-- Table structure for table `master_banks`
--

CREATE TABLE `master_banks` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'auto number',
  `bank` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'bank name',
  `logo` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'logo url',
  `account` varchar(16) COLLATE utf8_unicode_ci NOT NULL COMMENT 'bank account',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'account name',
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'DEPOSITE' COMMENT 'bank account type',
  `branch` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Dummy' COMMENT 'bank branch',
  `seq` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'order number',
  `active` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'active flag',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'created timestamp',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'updated timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='banking information';

--
-- Dumping data for table `master_banks`
--

INSERT INTO `master_banks` (`id`, `bank`, `logo`, `account`, `name`, `type`, `branch`, `seq`, `active`, `created_at`, `updated_at`) VALUES
(101, 'KBANK', 'https://kasikornbank.com/SiteCollectionDocuments/about/img/logo/logo.png', '098-0-64258-0', 'blackboard', 'DEPOSITE', 'Dummy', 1, 1, '2020-04-07 14:48:32', '2020-04-07 14:48:32'),
(102, 'SIAM COMMERCIAL BANK', 'https://is3-ssl.mzstatic.com/image/thumb/Purple118/v4/29/d0/f2/29d0f21a-c89c-52c8-2069-6f2b93d19963/AppIcon-1x_U007emarketing-85-220-0-1.png/600x600wa.png\r\n      ', '098-0-64258-0', 'blackboard', 'DEPOSITE', 'Dummy', 1, 1, '2020-04-07 14:48:32', '2020-04-07 14:48:32'),
(103, 'KRUNGSRI BANK', 'https://is1-ssl.mzstatic.com/image/thumb/Purple118/v4/8e/f1/39/8ef13919-5f08-9bac-913a-203219b5e790/AppIcons-1x_U007emarketing-85-220-1.png/1200x630bb.jpg\r\n      \"', '098-0-64258-0', 'blackboard', 'DEPOSITE', 'Dummy', 1, 1, '2020-04-07 14:48:32', '2020-04-07 14:48:32');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(20, '2014_10_12_000000_create_users_table', 1),
(21, '2014_10_12_100000_create_password_resets_table', 1),
(22, '2016_06_01_000001_create_oauth_auth_codes_table', 1),
(23, '2016_06_01_000002_create_oauth_access_tokens_table', 1),
(24, '2016_06_01_000003_create_oauth_refresh_tokens_table', 1),
(25, '2016_06_01_000004_create_oauth_clients_table', 1),
(26, '2016_06_01_000005_create_oauth_personal_access_clients_table', 1),
(27, '2020_03_25_154344_create_admins_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report_items`
--

CREATE TABLE `report_items` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'auto number',
  `title` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'title name',
  `seq` tinyint(3) UNSIGNED NOT NULL COMMENT 'order number'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='option for report lesson';

-- --------------------------------------------------------

--
-- Table structure for table `room_certs`
--

CREATE TABLE `room_certs` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'auto number',
  `room_id` int(10) UNSIGNED NOT NULL COMMENT 'ref to room',
  `file_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'path to file',
  `note` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'name',
  `active` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'active flag',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'status flag',
  `result` text COLLATE utf8_unicode_ci COMMENT 'result message',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'created timestamp',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'updated timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='certify of room';

-- --------------------------------------------------------

--
-- Table structure for table `room_follows`
--

CREATE TABLE `room_follows` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'auto number',
  `room_id` int(10) UNSIGNED NOT NULL COMMENT 'ref to room',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT 'ref to user',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'created timestamp',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'updated timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='following room';

-- --------------------------------------------------------

--
-- Table structure for table `room_infos`
--

CREATE TABLE `room_infos` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'auto number',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT 'ref to user',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'shop name',
  `note` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'shop description',
  `cover` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'cover url',
  `active` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'active flag',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'status flag',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='room information';

--
-- Dumping data for table `room_infos`
--

INSERT INTO `room_infos` (`id`, `user_id`, `name`, `note`, `cover`, `active`, `status`, `created_at`, `updated_at`) VALUES
(7, 1, 'Room Name', 'Shop Description', 'storage/rooms/7/cover.jpg', 0, 0, '2020-04-19 11:42:31', '2020-04-19 11:42:31');

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'auto number',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT 'ref to user',
  `current` decimal(7,2) NOT NULL DEFAULT '0.00' COMMENT 'current cost',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'created timestamp',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'updated timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='wallet of each user';

-- --------------------------------------------------------

--
-- Table structure for table `wallet_wips`
--

CREATE TABLE `wallet_wips` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'auto number',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT 'ref to user',
  `current` decimal(7,2) NOT NULL DEFAULT '0.00' COMMENT 'current cost',
  `type` varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'TOP UP' COMMENT 'type of updated (TOP UP, BUY, ADJUST, WITHDRAW)',
  `note` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'history note',
  `files` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'file upload',
  `updated` decimal(7,2) NOT NULL DEFAULT '0.00' COMMENT 'update cost',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'status flag',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'created timestamp',
  `created_uid` int(10) UNSIGNED NOT NULL COMMENT 'ref to admin',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'updated timestamp',
  `updated_uid` int(10) UNSIGNED NOT NULL DEFAULT '99' COMMENT 'ref to admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='transection for wallets';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categorys`
--
ALTER TABLE `categorys`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lesson_favorites`
--
ALTER TABLE `lesson_favorites`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lesson_infos`
--
ALTER TABLE `lesson_infos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lesson_purchases`
--
ALTER TABLE `lesson_purchases`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lesson_rates`
--
ALTER TABLE `lesson_rates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lesson_reports`
--
ALTER TABLE `lesson_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lesson_videos`
--
ALTER TABLE `lesson_videos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `log_wallets`
--
ALTER TABLE `log_wallets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `master_banks`
--
ALTER TABLE `master_banks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `report_items`
--
ALTER TABLE `report_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room_certs`
--
ALTER TABLE `room_certs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room_follows`
--
ALTER TABLE `room_follows`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room_infos`
--
ALTER TABLE `room_infos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wallet_wips`
--
ALTER TABLE `wallet_wips`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categorys`
--
ALTER TABLE `categorys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'auto number', AUTO_INCREMENT=121;
--
-- AUTO_INCREMENT for table `lesson_favorites`
--
ALTER TABLE `lesson_favorites`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'auto number';
--
-- AUTO_INCREMENT for table `lesson_infos`
--
ALTER TABLE `lesson_infos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'auto number', AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `lesson_purchases`
--
ALTER TABLE `lesson_purchases`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'auto number';
--
-- AUTO_INCREMENT for table `lesson_rates`
--
ALTER TABLE `lesson_rates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'auto number';
--
-- AUTO_INCREMENT for table `lesson_reports`
--
ALTER TABLE `lesson_reports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'auto number';
--
-- AUTO_INCREMENT for table `lesson_videos`
--
ALTER TABLE `lesson_videos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'auto number', AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `log_wallets`
--
ALTER TABLE `log_wallets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'auto number';
--
-- AUTO_INCREMENT for table `master_banks`
--
ALTER TABLE `master_banks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'auto number', AUTO_INCREMENT=104;
--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
--
-- AUTO_INCREMENT for table `report_items`
--
ALTER TABLE `report_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'auto number';
--
-- AUTO_INCREMENT for table `room_certs`
--
ALTER TABLE `room_certs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'auto number';
--
-- AUTO_INCREMENT for table `room_follows`
--
ALTER TABLE `room_follows`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'auto number';
--
-- AUTO_INCREMENT for table `room_infos`
--
ALTER TABLE `room_infos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'auto number', AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'auto number';
--
-- AUTO_INCREMENT for table `wallet_wips`
--
ALTER TABLE `wallet_wips`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'auto number';
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 10, 2026 at 01:56 PM
-- Server version: 5.7.38
-- PHP Version: 8.1.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `thegentl_production`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('superadmin','admin','moderator','finance') COLLATE utf8mb4_unicode_ci DEFAULT 'admin',
  `is_active` tinyint(1) DEFAULT '1',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `email`, `password_hash`, `name`, `role`, `is_active`, `last_login_at`, `last_login_ip`, `created_at`) VALUES
(1, 'alexdempers51@gmail.com', '$2y$12$A23ajKUD3eAxY3ERCx7NReRe/wIfrXtL46/JhtkHKsOFJwQkur2/m', 'Admin', 'admin', 1, '2026-06-10 10:08:45', '105.1.170.184', '2026-05-28 08:28:42');

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `actor_type` enum('user','admin','system','performer') COLLATE utf8mb4_unicode_ci NOT NULL,
  `actor_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_id` int(10) UNSIGNED DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `calls`
--

CREATE TABLE `calls` (
  `id` int(10) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'voice',
  `call_link_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `performer_id` int(10) UNSIGNED NOT NULL,
  `telephony_call_sid` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('initiating','ringing','in_progress','completed','failed','no_answer','busy','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'initiating',
  `direction` enum('inbound','outbound_bridge') COLLATE utf8mb4_unicode_ci DEFAULT 'outbound_bridge',
  `started_at` timestamp NULL DEFAULT NULL,
  `answered_at` timestamp NULL DEFAULT NULL,
  `ended_at` timestamp NULL DEFAULT NULL,
  `duration_seconds` int(10) UNSIGNED DEFAULT '0',
  `credits_used` decimal(10,4) DEFAULT '0.0000',
  `rate_per_minute` decimal(6,2) NOT NULL,
  `min_credits` decimal(10,2) DEFAULT '0.00',
  `min_minutes` int(11) DEFAULT '0',
  `performer_earnings` decimal(10,4) DEFAULT '0.0000',
  `platform_earnings` decimal(10,4) DEFAULT '0.0000',
  `termination_reason` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recording_sid` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `calls`
--

INSERT INTO `calls` (`id`, `uuid`, `type`, `call_link_id`, `user_id`, `performer_id`, `telephony_call_sid`, `status`, `direction`, `started_at`, `answered_at`, `ended_at`, `duration_seconds`, `credits_used`, `rate_per_minute`, `min_credits`, `min_minutes`, `performer_earnings`, `platform_earnings`, `termination_reason`, `recording_sid`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'f61932ba-816b-405c-9f9c-e2e0b5d75bfb', 'voice', 2, 99, 4, NULL, '', 'outbound_bridge', NULL, NULL, NULL, 0, 0.0000, 2.00, 0.00, 0, 0.0000, 0.0000, NULL, NULL, NULL, '2026-06-02 08:45:45', '2026-06-02 08:46:41'),
(2, '85b070b4-12c5-4085-b903-9ec7584e363a', 'voice', 3, 99, 4, NULL, '', 'outbound_bridge', NULL, NULL, NULL, 0, 0.0000, 2.00, 0.00, 0, 0.0000, 0.0000, NULL, NULL, NULL, '2026-06-02 08:50:40', '2026-06-02 08:50:46'),
(3, '285d6ea7-11d1-4b6b-8f6d-829d454af558', 'voice', 4, 99, 4, NULL, '', 'outbound_bridge', NULL, NULL, NULL, 0, 0.0000, 2.00, 0.00, 0, 0.0000, 0.0000, NULL, NULL, NULL, '2026-06-02 08:54:17', '2026-06-02 08:54:24'),
(4, '06aedda2-e0d4-4fef-b26a-28aa7fd5ad03', 'voice', 5, 99, 4, NULL, '', 'outbound_bridge', NULL, NULL, NULL, 0, 0.0000, 2.00, 0.00, 0, 0.0000, 0.0000, NULL, NULL, NULL, '2026-06-02 08:54:58', '2026-06-02 08:55:07'),
(5, '9ad5a47b-f4f6-478a-8853-0fb02cf6ad55', 'voice', 6, 99, 4, NULL, '', 'outbound_bridge', NULL, NULL, NULL, 0, 0.0000, 2.00, 0.00, 0, 0.0000, 0.0000, NULL, NULL, NULL, '2026-06-02 08:57:22', '2026-06-02 08:57:26'),
(6, '78104630-8bb3-49ad-9947-9a465efc9df1', 'voice', 7, 99, 4, NULL, '', 'outbound_bridge', NULL, NULL, NULL, 0, 0.0000, 2.00, 0.00, 0, 0.0000, 0.0000, NULL, NULL, NULL, '2026-06-02 08:58:50', '2026-06-02 08:58:54'),
(7, 'fcb4aba3-75a9-4603-8644-5181d043581b', 'voice', 8, 99, 4, NULL, '', 'outbound_bridge', NULL, NULL, NULL, 0, 0.0000, 2.00, 0.00, 0, 0.0000, 0.0000, NULL, NULL, NULL, '2026-06-02 09:00:07', '2026-06-02 09:00:12'),
(8, 'c7217eb5-14a9-48e6-bffb-a306bb3b2180', 'voice', 9, 99, 4, NULL, '', 'outbound_bridge', NULL, NULL, NULL, 0, 0.0000, 2.00, 0.00, 0, 0.0000, 0.0000, NULL, NULL, NULL, '2026-06-02 09:00:32', '2026-06-02 09:00:36'),
(9, '3c12f872-4d72-4db5-9474-c7763a0314cf', 'voice', 10, 99, 4, NULL, '', 'outbound_bridge', NULL, NULL, NULL, 0, 0.0000, 2.00, 0.00, 0, 0.0000, 0.0000, NULL, NULL, NULL, '2026-06-02 09:03:30', '2026-06-02 09:03:34'),
(10, 'ee589c47-4ec1-49a3-a030-9f85c964e4b6', 'voice', 11, 99, 4, NULL, '', 'outbound_bridge', NULL, NULL, NULL, 0, 0.0000, 2.00, 0.00, 0, 0.0000, 0.0000, NULL, NULL, NULL, '2026-06-02 09:04:20', '2026-06-02 09:04:25'),
(11, 'd74be848-86c8-432e-a826-b9bfc52faf42', 'voice', 12, 99, 4, NULL, '', 'outbound_bridge', NULL, NULL, NULL, 0, 0.0000, 2.00, 0.00, 0, 0.0000, 0.0000, NULL, NULL, NULL, '2026-06-02 09:04:42', '2026-06-02 09:04:46'),
(12, 'b5e9616d-8cd3-4b0e-ae55-50add32eba02', 'voice', 13, 99, 4, NULL, '', 'outbound_bridge', NULL, NULL, NULL, 0, 0.0000, 2.00, 0.00, 0, 0.0000, 0.0000, NULL, NULL, NULL, '2026-06-02 09:07:52', '2026-06-02 09:07:56'),
(13, 'e1afb69c-3eeb-4558-a83f-d2bda6e4fdbc', 'voice', 14, 99, 4, NULL, '', 'outbound_bridge', NULL, NULL, NULL, 0, 0.0000, 2.00, 0.00, 0, 0.0000, 0.0000, NULL, NULL, NULL, '2026-06-02 09:10:46', '2026-06-02 09:10:49'),
(14, '5099d364-ac76-4272-bcdc-47e495ee8f22', 'voice', 15, 99, 4, NULL, '', 'outbound_bridge', NULL, NULL, NULL, 0, 0.0000, 2.00, 0.00, 0, 0.0000, 0.0000, 'declined_by_performer', NULL, NULL, '2026-06-02 09:13:23', '2026-06-02 09:13:52'),
(15, '8621ce34-2dee-48f8-8403-06a3b74ed926', 'voice', 16, 99, 4, NULL, '', 'outbound_bridge', NULL, NULL, NULL, 0, 0.0000, 2.00, 0.00, 0, 0.0000, 0.0000, NULL, NULL, NULL, '2026-06-02 09:13:59', '2026-06-02 09:14:03'),
(16, '61487819-3831-4fcd-af12-0d3e445626d6', 'voice', 17, 99, 4, NULL, '', 'outbound_bridge', NULL, NULL, NULL, 0, 0.0000, 2.00, 0.00, 0, 0.0000, 0.0000, NULL, NULL, NULL, '2026-06-02 09:20:24', '2026-06-02 09:20:31'),
(17, 'e77c2cf9-b3b0-43e0-9896-7666f265bd8d', 'voice', 18, 99, 4, NULL, '', 'outbound_bridge', NULL, NULL, NULL, 0, 0.0000, 2.00, 0.00, 0, 0.0000, 0.0000, NULL, NULL, NULL, '2026-06-02 09:24:10', '2026-06-02 09:24:19'),
(18, '67b18b46-1fce-475d-954b-6f1485a08308', 'voice', 19, 99, 4, NULL, '', 'outbound_bridge', NULL, NULL, NULL, 0, 0.0000, 2.00, 0.00, 0, 0.0000, 0.0000, NULL, NULL, NULL, '2026-06-02 09:28:25', '2026-06-02 09:28:37'),
(19, 'e8b356b2-c0bc-459b-a343-bd3e95be423c', 'voice', 20, 99, 4, NULL, 'in_progress', 'outbound_bridge', NULL, '2026-06-02 09:36:55', NULL, 0, 0.0000, 2.00, 0.00, 0, 0.0000, 0.0000, NULL, NULL, NULL, '2026-06-02 09:36:50', '2026-06-02 09:36:55'),
(20, 'c4a5cb52-53bc-492c-9789-985a0b06f7db', 'voice', 21, 99, 4, NULL, 'in_progress', 'outbound_bridge', NULL, '2026-06-02 09:37:57', NULL, 0, 0.0000, 2.00, 0.00, 0, 0.0000, 0.0000, NULL, NULL, NULL, '2026-06-02 09:37:53', '2026-06-02 09:37:57'),
(21, '558e0726-b2f2-49ec-bc44-ff284aef7b0c', 'voice', 22, 99, 4, NULL, 'completed', 'outbound_bridge', NULL, '2026-06-02 09:47:20', '2026-06-02 06:49:02', 0, 0.0000, 2.00, 0.00, 0, 0.0000, 0.0000, 'completed', NULL, NULL, '2026-06-02 09:47:15', '2026-06-02 09:49:02'),
(22, '0f404ecc-d6ad-4a53-a03c-ac984c9df77e', 'voice', 23, 99, 4, NULL, 'completed', 'outbound_bridge', NULL, '2026-06-02 09:52:17', '2026-06-02 06:53:33', 0, 0.0000, 2.00, 0.00, 0, 0.0000, 0.0000, 'completed', NULL, NULL, '2026-06-02 09:52:08', '2026-06-02 09:53:33'),
(23, 'c6b6a198-9340-469a-b5cb-d94505342bf0', 'voice', 24, 99, 4, NULL, 'completed', 'outbound_bridge', NULL, '2026-06-02 09:57:58', '2026-06-02 06:58:08', 0, 0.0000, 2.00, 0.00, 0, 0.0000, 0.0000, 'completed', NULL, NULL, '2026-06-02 09:57:53', '2026-06-02 09:58:08'),
(24, '7ba9e166-ef60-4b01-bf5f-10c0d73edfc2', 'voice', 25, 99, 4, NULL, 'completed', 'outbound_bridge', NULL, '2026-06-02 09:58:25', '2026-06-02 06:58:37', 0, 0.0000, 2.00, 0.00, 0, 0.0000, 0.0000, 'completed', NULL, NULL, '2026-06-02 09:58:20', '2026-06-02 09:58:37'),
(25, '15c1c0af-f1ed-4125-8d4c-bdcd0e11b554', 'voice', 26, 99, 4, NULL, 'completed', 'outbound_bridge', NULL, '2026-06-02 10:03:59', NULL, 0, 0.0000, 2.00, 0.00, 0, 0.0000, 0.0000, 'out_of_credits', NULL, NULL, '2026-06-02 10:03:55', '2026-06-02 10:09:06'),
(26, '8504a8ae-afde-4a17-b808-2d7711926bf6', 'voice', 27, 99, 3, NULL, 'completed', 'outbound_bridge', NULL, '2026-06-02 10:45:22', '2026-06-02 07:45:35', 0, 0.0000, 1.00, 0.00, 0, 0.0000, 0.0000, 'completed', NULL, NULL, '2026-06-02 10:45:14', '2026-06-02 10:45:35'),
(27, '93b051ca-0130-46aa-953d-511e85eda2c9', 'voice', 28, 99, 3, NULL, 'completed', 'outbound_bridge', NULL, '2026-06-02 10:49:29', '2026-06-02 07:50:33', 0, 0.0000, 1.00, 0.00, 0, 0.0000, 0.0000, 'completed', NULL, NULL, '2026-06-02 10:49:21', '2026-06-02 10:50:33'),
(28, 'f703e8f0-7e15-4d56-926d-9b382cc13132', 'voice', 29, 99, 5, NULL, '', 'outbound_bridge', NULL, '2026-06-02 11:27:23', NULL, 0, 0.0000, 5.00, 0.00, 0, 0.0000, 0.0000, NULL, NULL, NULL, '2026-06-02 11:27:11', '2026-06-02 11:27:23'),
(29, '264f795d-65f8-4fc5-a5a1-6e2de6a4fd8e', 'voice', 30, 99, 5, NULL, '', 'outbound_bridge', NULL, '2026-06-02 11:28:53', NULL, 0, 0.0000, 5.00, 0.00, 0, 0.0000, 0.0000, NULL, NULL, NULL, '2026-06-02 11:28:50', '2026-06-02 11:28:53'),
(30, '5d703d10-0f19-46bc-990b-95970681062a', 'voice', 31, 99, 3, NULL, 'completed', 'outbound_bridge', NULL, '2026-06-02 11:31:33', '2026-06-02 08:31:47', 0, 0.0000, 1.00, 0.00, 0, 0.0000, 0.0000, 'completed', NULL, NULL, '2026-06-02 11:31:29', '2026-06-02 11:31:47'),
(31, '7c6b07d4-d155-47e3-b9e7-6f0ba2d187ad', 'voice', 32, 99, 3, NULL, '', 'outbound_bridge', NULL, '2026-06-02 11:32:48', NULL, 0, 0.0000, 1.00, 0.00, 0, 0.0000, 0.0000, NULL, NULL, NULL, '2026-06-02 11:32:45', '2026-06-02 11:32:48'),
(32, '4464914b-435b-407f-9a91-3dbaac8faa8e', 'voice', 33, 99, 1, NULL, 'completed', 'outbound_bridge', NULL, '2026-06-02 11:51:50', '2026-06-02 08:52:40', 0, 0.0000, 1.00, 0.00, 0, 0.0000, 0.0000, 'completed', NULL, NULL, '2026-06-02 11:51:44', '2026-06-02 11:52:40'),
(33, '42847449-38ad-4bb4-8cb3-187b468bff74', 'voice', 34, 99, 1, NULL, 'completed', 'outbound_bridge', NULL, '2026-06-02 11:54:12', '2026-06-02 08:56:11', 0, 0.0000, 1.00, 0.00, 0, 0.0000, 0.0000, 'completed', NULL, NULL, '2026-06-02 11:54:05', '2026-06-02 11:56:11'),
(34, '9f95db1a-d0b3-44ca-b696-db377cc2c2a1', 'voice', 35, 99, 1, NULL, 'completed', 'outbound_bridge', NULL, '2026-06-02 11:57:48', NULL, 0, 0.0000, 1.00, 0.00, 0, 0.0000, 0.0000, 'out_of_credits', NULL, NULL, '2026-06-02 11:57:44', '2026-06-02 12:06:52'),
(35, '30b201b2-194f-48e7-8166-9cd11af1479f', 'voice', 36, 99, 3, NULL, 'completed', 'outbound_bridge', NULL, '2026-06-02 12:08:47', '2026-06-02 09:09:08', 0, 0.0000, 1.00, 0.00, 0, 0.0000, 0.0000, 'completed', NULL, NULL, '2026-06-02 12:08:42', '2026-06-02 12:09:08'),
(36, '69abfc5f-a270-4ba8-906b-52287c29d31c', 'voice', 37, 99, 3, NULL, 'completed', 'outbound_bridge', NULL, '2026-06-02 12:13:50', '2026-06-02 09:14:13', 0, 0.0000, 1.00, 0.00, 0, 0.0000, 0.0000, 'completed', NULL, NULL, '2026-06-02 12:13:43', '2026-06-02 12:14:13'),
(37, 'b06037d5-84ba-40c2-9f34-e6719ca65692', 'video', 38, 99, 3, NULL, 'completed', 'outbound_bridge', NULL, '2026-06-02 12:51:30', '2026-06-02 09:53:25', 0, 15.0000, 5.00, 15.00, 10, 6.0000, 9.0000, 'completed', NULL, NULL, '2026-06-02 12:51:22', '2026-06-02 12:53:25'),
(38, '18af230c-f125-4695-ac51-b9e68ec409e9', 'video', 39, 99, 3, NULL, 'completed', 'outbound_bridge', NULL, '2026-06-02 12:57:49', '2026-06-02 09:58:06', 0, 15.0000, 5.00, 15.00, 10, 6.0000, 9.0000, 'completed', NULL, NULL, '2026-06-02 12:57:44', '2026-06-02 12:58:06'),
(39, 'eae92925-03a2-4147-9104-09a9b8bfa005', 'video', 40, 99, 3, NULL, 'completed', 'outbound_bridge', NULL, '2026-06-02 13:00:15', '2026-06-02 10:00:46', 0, 15.0000, 5.00, 15.00, 10, 6.0000, 9.0000, 'completed', NULL, NULL, '2026-06-02 13:00:09', '2026-06-02 13:00:46'),
(40, '44ed376a-c54e-42a8-8a0c-d2e9c018ec0c', 'video', 41, 99, 3, NULL, 'completed', 'outbound_bridge', NULL, '2026-06-02 13:01:27', '2026-06-02 10:02:50', 0, 15.0000, 5.00, 15.00, 10, 6.0000, 9.0000, 'completed', NULL, NULL, '2026-06-02 13:01:25', '2026-06-02 13:02:50'),
(41, '77764bf9-28cc-41a0-bcbd-8ad7ce6a6293', 'video', 42, 99, 3, NULL, 'completed', 'outbound_bridge', NULL, '2026-06-02 13:03:28', '2026-06-02 10:04:15', 0, 15.0000, 5.00, 15.00, 10, 6.0000, 9.0000, 'completed', NULL, NULL, '2026-06-02 13:03:14', '2026-06-02 13:04:15'),
(42, '1c55c7cf-a581-4fec-9721-e5dd47149d05', 'video', 43, 99, 3, NULL, 'completed', 'outbound_bridge', NULL, '2026-06-02 13:05:02', '2026-06-02 10:05:18', 0, 15.0000, 5.00, 15.00, 10, 6.0000, 9.0000, 'completed', NULL, NULL, '2026-06-02 13:04:54', '2026-06-02 13:05:18'),
(43, '092682ae-b0aa-4e93-b209-5e61ca628f48', 'video', 44, 99, 3, NULL, 'completed', 'outbound_bridge', NULL, '2026-06-02 13:06:02', '2026-06-02 10:06:15', 0, 15.0000, 5.00, 15.00, 10, 6.0000, 9.0000, 'completed', NULL, NULL, '2026-06-02 13:05:57', '2026-06-02 13:06:15'),
(44, '9092e40f-1f7a-415a-9a4b-ff735719cfaf', 'video', 45, 99, 3, NULL, 'completed', 'outbound_bridge', NULL, '2026-06-02 13:13:57', '2026-06-02 10:14:35', 38, 15.0000, 5.00, 15.00, 10, 6.0000, 9.0000, 'completed', NULL, NULL, '2026-06-02 13:13:52', '2026-06-02 13:14:35'),
(45, '3cb97029-2c1d-4f60-a616-7c00092c4e72', 'video', 46, 99, 3, NULL, 'completed', 'outbound_bridge', NULL, '2026-06-02 13:21:31', '2026-06-02 10:21:54', 23, 15.0000, 5.00, 15.00, 10, 6.0000, 9.0000, 'completed', NULL, NULL, '2026-06-02 13:21:20', '2026-06-02 13:21:54'),
(46, '5dc748cc-4db0-45a5-bf43-096b4fe1a293', 'voice', 47, 99, 3, NULL, 'completed', 'outbound_bridge', NULL, '2026-06-02 13:22:12', '2026-06-02 10:22:26', 14, 1.0000, 1.00, 0.00, 0, 0.4000, 0.6000, 'completed', NULL, NULL, '2026-06-02 13:22:08', '2026-06-02 13:22:26'),
(47, 'b416c101-f04c-4703-b5fd-81b7f1541fd9', 'video', 48, 99, 3, NULL, 'completed', 'outbound_bridge', NULL, '2026-06-02 13:24:28', '2026-06-02 10:24:46', 18, 15.0000, 5.00, 15.00, 10, 6.0000, 9.0000, 'completed', NULL, NULL, '2026-06-02 13:24:22', '2026-06-02 13:24:46'),
(48, 'bb322ef6-3191-48dd-8735-4722dea26527', 'video', 49, 99, 3, NULL, 'completed', 'outbound_bridge', NULL, '2026-06-02 13:27:47', '2026-06-02 10:27:58', 11, 15.0000, 5.00, 15.00, 10, 6.0000, 9.0000, 'completed', NULL, NULL, '2026-06-02 13:27:44', '2026-06-02 13:27:58'),
(49, '7ce8158e-4370-4cca-a98e-b81afb29899c', 'video', 50, 99, 3, NULL, 'completed', 'outbound_bridge', NULL, '2026-06-02 13:48:27', '2026-06-02 10:48:40', 13, 5.0000, 5.00, 5.00, 10, 2.0000, 3.0000, 'completed', NULL, NULL, '2026-06-02 13:48:20', '2026-06-02 13:48:40'),
(50, '3f9cb433-9e62-4554-be07-7f25861f2b27', 'video', 51, 99, 3, NULL, '', 'outbound_bridge', NULL, NULL, NULL, 0, 0.0000, 5.00, 5.00, 10, 0.0000, 0.0000, 'declined_by_performer', NULL, NULL, '2026-06-02 13:49:18', '2026-06-02 13:49:23'),
(51, 'f4c28dbf-37b1-4114-8283-574e0a62133f', 'video', 52, 99, 3, NULL, 'completed', 'outbound_bridge', NULL, '2026-06-02 13:52:22', '2026-06-02 10:52:32', 10, 5.0000, 5.00, 5.00, 10, 2.0000, 3.0000, 'completed', NULL, NULL, '2026-06-02 13:52:17', '2026-06-02 13:52:32'),
(52, '7ce92f6b-c910-44bc-94a6-58fd48961851', 'voice', 53, 99, 3, NULL, '', 'outbound_bridge', NULL, NULL, NULL, 0, 0.0000, 1.00, 0.00, 0, 0.0000, 0.0000, 'declined_by_performer', NULL, NULL, '2026-06-02 13:52:44', '2026-06-02 13:52:49'),
(53, '75f9d307-f6d8-4bc3-91f7-b98c1913ce86', 'voice', 54, 99, 3, NULL, 'completed', 'outbound_bridge', NULL, '2026-06-02 13:55:23', '2026-06-02 10:55:32', 9, 1.0000, 1.00, 0.00, 0, 0.4000, 0.6000, 'completed', NULL, NULL, '2026-06-02 13:55:18', '2026-06-02 13:55:32'),
(54, '0d6676a5-4f25-4a7f-ad43-006ebed1b2c9', 'voice', 55, 99, 3, NULL, 'completed', 'outbound_bridge', NULL, '2026-06-02 14:00:11', '2026-06-02 11:00:16', 5, 1.0000, 1.00, 0.00, 0, 0.4000, 0.6000, 'completed', NULL, NULL, '2026-06-02 14:00:06', '2026-06-02 14:00:16'),
(55, '0a65fa08-ec28-4ef4-8122-2beefe9e23b4', 'video', 56, 99, 3, NULL, 'completed', 'outbound_bridge', NULL, '2026-06-02 14:00:28', '2026-06-02 11:00:35', 7, 5.0000, 5.00, 5.00, 10, 2.0000, 3.0000, 'completed', NULL, NULL, '2026-06-02 14:00:22', '2026-06-02 14:00:35'),
(56, 'a58f668c-6e7d-41e7-bc46-388da4beea3d', 'video', 57, 99, 3, NULL, 'completed', 'outbound_bridge', NULL, '2026-06-02 14:02:22', '2026-06-02 11:02:42', 20, 5.0000, 5.00, 5.00, 10, 2.0000, 3.0000, 'completed', NULL, NULL, '2026-06-02 14:02:16', '2026-06-02 14:02:42'),
(57, '76985145-b4e8-4489-8248-350edb5f3620', 'voice', 58, 99, 3, NULL, 'completed', 'outbound_bridge', NULL, '2026-06-03 09:13:40', '2026-06-03 06:13:51', 11, 1.0000, 1.00, 0.00, 0, 0.4000, 0.6000, 'completed', NULL, NULL, '2026-06-03 09:13:35', '2026-06-03 09:13:51'),
(58, 'c3a8e9bb-46b5-44de-bf28-da9955d47cef', 'video', 59, 99, 3, NULL, 'completed', 'outbound_bridge', NULL, '2026-06-03 09:14:03', '2026-06-03 06:14:22', 19, 5.0000, 5.00, 5.00, 10, 2.0000, 3.0000, 'completed', NULL, NULL, '2026-06-03 09:14:00', '2026-06-03 09:14:22'),
(59, 'dce7a93a-c1d4-4a09-9b1e-61b8700dbe6c', 'voice', 60, 99, 3, NULL, 'completed', 'outbound_bridge', NULL, '2026-06-03 09:17:06', '2026-06-03 06:17:15', 9, 1.0000, 1.00, 0.00, 0, 0.4000, 0.6000, 'completed', NULL, NULL, '2026-06-03 09:17:00', '2026-06-03 09:17:15'),
(60, 'd48f60fc-e034-4138-a5d0-171d8e83bf5f', 'voice', 61, 1, 1, NULL, 'completed', 'outbound_bridge', NULL, '2026-06-10 10:11:05', '2026-06-10 07:11:21', 16, 1.0000, 1.00, 0.00, 0, 0.4000, 0.6000, 'completed', NULL, NULL, '2026-06-10 10:11:01', '2026-06-10 10:11:21'),
(61, 'c89eed9f-6b90-459c-87fc-383d76452d83', 'video', 62, 1, 1, NULL, 'completed', 'outbound_bridge', NULL, '2026-06-10 10:12:37', '2026-06-10 07:13:06', 29, 15.0000, 5.00, 15.00, 10, 6.0000, 9.0000, 'completed', NULL, NULL, '2026-06-10 10:12:32', '2026-06-10 10:13:06');

-- --------------------------------------------------------

--
-- Table structure for table `call_links`
--

CREATE TABLE `call_links` (
  `id` int(10) UNSIGNED NOT NULL,
  `token` char(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `performer_id` int(10) UNSIGNED NOT NULL,
  `status` enum('pending','used','expired','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `expires_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `used_at` timestamp NULL DEFAULT NULL,
  `ip_created` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_used` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `call_links`
--

INSERT INTO `call_links` (`id`, `token`, `user_id`, `performer_id`, `status`, `expires_at`, `used_at`, `ip_created`, `ip_used`, `created_at`) VALUES
(1, '8e3b5ba3aab9044c7f16795be331a242e3030d8f4ff9867f1fd3a8eb03fb5d89', 1, 3, 'pending', '2026-06-02 06:54:44', NULL, '::1', NULL, '2026-06-02 06:39:44'),
(2, 'c83562c83e4056fdadec39c7589a7548d6709837c96e047feea0b1cd6573053b', 99, 4, 'pending', '2026-06-02 05:50:45', NULL, '168.210.241.18', NULL, '2026-06-02 08:45:45'),
(3, '9618da954799a418e4c248e7c6be58dfefab18a045c7a4f400b77885300d9499', 99, 4, 'pending', '2026-06-02 05:55:40', NULL, '105.245.169.168', NULL, '2026-06-02 08:50:40'),
(4, '8ae97c7768107c3081ec81afce37b9f4360fe762ae639ac5dd2bb9100485a3c2', 99, 4, 'pending', '2026-06-02 05:59:17', NULL, '168.210.241.18', NULL, '2026-06-02 08:54:17'),
(5, '81180bbd90d1486bdb77e7a03722af2ab2b43fafbbb286fba91c03eced17d0f8', 99, 4, 'pending', '2026-06-02 05:59:58', NULL, '168.210.241.18', NULL, '2026-06-02 08:54:58'),
(6, 'c973dcded5ff68c337bc359a0a0425451f49a098757119edcde7b20cf23db317', 99, 4, 'pending', '2026-06-02 06:02:22', NULL, '168.210.241.18', NULL, '2026-06-02 08:57:22'),
(7, '7c177ae6711bfb68ab0f0197fc169712b8d765000ac448146ce58b0e62d48be7', 99, 4, 'pending', '2026-06-02 06:03:50', NULL, '168.210.241.18', NULL, '2026-06-02 08:58:50'),
(8, '0bdb4b83557175b3cbee4475376b7ea05adc117bbb6042fc6dd6afd08fe1b92a', 99, 4, 'pending', '2026-06-02 06:05:07', NULL, '168.210.241.18', NULL, '2026-06-02 09:00:07'),
(9, 'd1db34c50d23420b18b6046063a710721d71b2e4754cc2b7c245a658ed95153a', 99, 4, 'pending', '2026-06-02 06:05:32', NULL, '168.210.241.18', NULL, '2026-06-02 09:00:32'),
(10, '0f98875b9df66f138b49f13f9fdc2a3082ffc07a9ba8040486945d6995b51833', 99, 4, 'pending', '2026-06-02 06:08:30', NULL, '168.210.241.18', NULL, '2026-06-02 09:03:30'),
(11, 'decc4504fca2a870f33c71d29ae3648652a413e5c65a72bc22b47f986409e91c', 99, 4, 'pending', '2026-06-02 06:09:20', NULL, '168.210.241.18', NULL, '2026-06-02 09:04:20'),
(12, 'a446529f79b98a1b08ddcd951d36df5b6477831c4bd3bbf9e365b3a76264a4b6', 99, 4, 'pending', '2026-06-02 06:09:42', NULL, '168.210.241.18', NULL, '2026-06-02 09:04:42'),
(13, '33dadd9db9810027d1d595e75232c948e8506daedd51436aa0358e5318873b6a', 99, 4, 'pending', '2026-06-02 06:12:52', NULL, '168.210.241.18', NULL, '2026-06-02 09:07:52'),
(14, 'e0f939690d69f61fda84f56af63b50d215727756ecf22ea4e2576bfc2cc234f6', 99, 4, 'pending', '2026-06-02 06:15:46', NULL, '168.210.241.18', NULL, '2026-06-02 09:10:46'),
(15, '15b90ade49a512155ff1c5bcb4556ff8941780d086eb88dcd401e46202dcd1a1', 99, 4, 'pending', '2026-06-02 06:18:23', NULL, '168.210.241.18', NULL, '2026-06-02 09:13:23'),
(16, '700e34b6b9d1558410129cdb4960a4f46dfeee4d57acb7f32bf1e5a700203b74', 99, 4, 'pending', '2026-06-02 06:18:59', NULL, '168.210.241.18', NULL, '2026-06-02 09:13:59'),
(17, 'ec564e8cbbeaa0a93223ffa0c801f6525a1bbc8ca7f23976bc675a902033a249', 99, 4, 'pending', '2026-06-02 06:25:24', NULL, '168.210.241.18', NULL, '2026-06-02 09:20:24'),
(18, 'f22bcea7715f8cc1307e1ac607e722cb27e97ee0b9d0cf674405aca83480840e', 99, 4, 'pending', '2026-06-02 06:29:10', NULL, '168.210.241.18', NULL, '2026-06-02 09:24:10'),
(19, 'd180a4ca1c0097b7726fee40af74e3a44d48a405b3c63da1694d46e89baf4e57', 99, 4, 'pending', '2026-06-02 06:33:25', NULL, '168.210.241.18', NULL, '2026-06-02 09:28:25'),
(20, '3067928b3ca4897b95ddf514e5aef64d0dbee4dfb75ff8038e6e04330fe331dd', 99, 4, 'pending', '2026-06-02 06:41:50', NULL, '168.210.241.18', NULL, '2026-06-02 09:36:50'),
(21, 'cbf8230710a68b91c60b8971f15d70d81676cf554612bd2504d36b6a2960c5d9', 99, 4, 'pending', '2026-06-02 06:42:53', NULL, '168.210.241.18', NULL, '2026-06-02 09:37:53'),
(22, '342b4faee3bf7661f4bd5405dc22fe6d9b061775de39b47c46cb8da58c38ef09', 99, 4, 'pending', '2026-06-02 06:52:15', NULL, '168.210.241.18', NULL, '2026-06-02 09:47:15'),
(23, '41324dad3ecabdeac25e16d91c22d2501641cddaab41491c9ac93e3fe0a1c55c', 99, 4, 'pending', '2026-06-02 06:57:08', NULL, '168.210.241.18', NULL, '2026-06-02 09:52:08'),
(24, '49ab2f80d7582194e74e8b1e6bf8b116dabf56a1cf6067e686218e5e338ea57f', 99, 4, 'pending', '2026-06-02 07:02:53', NULL, '168.210.241.18', NULL, '2026-06-02 09:57:53'),
(25, '9da33fc8cb261564210a0f30b98c432ed720053084a5c4581ede65ca98901b4a', 99, 4, 'pending', '2026-06-02 07:03:20', NULL, '168.210.241.18', NULL, '2026-06-02 09:58:20'),
(26, '6bb8e5bca083897c564f40892a905dc115f1c70c0f942420d9f171f5c606781e', 99, 4, 'pending', '2026-06-02 07:08:55', NULL, '168.210.241.18', NULL, '2026-06-02 10:03:55'),
(27, 'caa4ea88607e9900a90878de82b56e7b45d69ba2c6dd5c8a86e13bde5ec98679', 99, 3, 'pending', '2026-06-02 07:50:14', NULL, '168.210.241.18', NULL, '2026-06-02 10:45:14'),
(28, '5835997d89836405190e02daac563aa9a269451313e4fcac8c5591f1c535ae53', 99, 3, 'pending', '2026-06-02 07:54:21', NULL, '168.210.241.18', NULL, '2026-06-02 10:49:21'),
(29, 'e0f5961cd49876dbb8300e166e00d11c140a9a020f9be2ccc0fd1d5aada87fc2', 99, 5, 'pending', '2026-06-02 08:32:11', NULL, '168.210.241.18', NULL, '2026-06-02 11:27:11'),
(30, '3761c5f1e8c43c4a462ada4010e2e13738a4a65351a20c1224e27552c0bbf181', 99, 5, 'pending', '2026-06-02 08:33:50', NULL, '168.210.241.18', NULL, '2026-06-02 11:28:50'),
(31, '32ace739d3110b70921a6ebc97e485ce312667ccb1966dc1d2886eb379f1d9e5', 99, 3, 'pending', '2026-06-02 08:36:29', NULL, '168.210.241.18', NULL, '2026-06-02 11:31:29'),
(32, '5c92a44659dacb3950a1eab8150bf70c2f751001f49610ee5301b077a0e5c2b2', 99, 3, 'pending', '2026-06-02 08:37:45', NULL, '168.210.241.18', NULL, '2026-06-02 11:32:45'),
(33, '092b20a6d0ea0bdca3496a534a351b583a12a995d17cca887e39b364edf49125', 99, 1, 'pending', '2026-06-02 08:56:44', NULL, '168.210.241.18', NULL, '2026-06-02 11:51:44'),
(34, '2dc096b5087e1033e1c0bdbb6d3807ee179416d96b86744147103e6aa4af3c6c', 99, 1, 'pending', '2026-06-02 08:59:05', NULL, '168.210.241.18', NULL, '2026-06-02 11:54:05'),
(35, '7e4deb57ec548e1495b45f766ba0dde3e4a4588387974230f6fbceba2592b3de', 99, 1, 'pending', '2026-06-02 09:02:44', NULL, '168.210.241.18', NULL, '2026-06-02 11:57:44'),
(36, '1b040389cd2a4dece225a4f1f55cf8ea6070ca5d9fa552e4c7aa80a4f9a8dd04', 99, 3, 'pending', '2026-06-02 09:13:42', NULL, '168.210.241.18', NULL, '2026-06-02 12:08:42'),
(37, 'c7cfa7a8c15b11f5155d8bb8424b0122cd0f8e6d4a403303adbaa57ba36588f2', 99, 3, 'pending', '2026-06-02 09:18:43', NULL, '168.210.241.18', NULL, '2026-06-02 12:13:43'),
(38, 'e1cc86f1a2ea618f636abea917e1e095f7a136b558c6e1e887b0bab740c9bb8c', 99, 3, 'pending', '2026-06-02 09:56:22', NULL, '168.210.241.18', NULL, '2026-06-02 12:51:22'),
(39, 'b99d16f0926de083d390a8f34fbd7b32e36fd16d613c7bdcbb99bff21725536a', 99, 3, 'pending', '2026-06-02 10:02:44', NULL, '168.210.241.18', NULL, '2026-06-02 12:57:44'),
(40, 'bfa19eff8ba856ac13e29b2a6ff8de55904241ff0c15e829d4cd541e08f4fc80', 99, 3, 'pending', '2026-06-02 10:05:09', NULL, '168.210.241.18', NULL, '2026-06-02 13:00:09'),
(41, '7ac889aec76c270c48589441ee77afde9400fcb28bce5f16ef333d0a8ef585f3', 99, 3, 'pending', '2026-06-02 10:06:25', NULL, '168.210.241.18', NULL, '2026-06-02 13:01:25'),
(42, '758cb11ac620c9ce31656697e5ac2e5e2943cf63b8cc025e44d79166a860dfa4', 99, 3, 'pending', '2026-06-02 10:08:14', NULL, '168.210.241.18', NULL, '2026-06-02 13:03:14'),
(43, 'a9ad038b3613e8e975f50a60e9fccf3f2656ab81520fd230ca2c0b99a8500df9', 99, 3, 'pending', '2026-06-02 10:09:54', NULL, '168.210.241.18', NULL, '2026-06-02 13:04:54'),
(44, '472c1367c64f0a33fb1f4e827f1f22fa7ce24ec2e42d309fdfdb86216100c2d7', 99, 3, 'pending', '2026-06-02 10:10:57', NULL, '168.210.241.18', NULL, '2026-06-02 13:05:57'),
(45, 'd46481c44e5004bb26df357ae1b8955711abc6c4b0e48fe29410b81d4fa15e35', 99, 3, 'pending', '2026-06-02 10:18:52', NULL, '168.210.241.18', NULL, '2026-06-02 13:13:52'),
(46, '724b984ccc10eda0b826b8433cb3b840f0c1677a6157b64653d518011f09809d', 99, 3, 'pending', '2026-06-02 10:26:20', NULL, '168.210.241.18', NULL, '2026-06-02 13:21:20'),
(47, 'd5cf7fae553dc26b9e43440e605de92b91c940eb1610fa421c66c02be2ec6afd', 99, 3, 'pending', '2026-06-02 10:27:08', NULL, '168.210.241.18', NULL, '2026-06-02 13:22:08'),
(48, '93ba92f014c778869e3e9afa6f00872c529ef5ae570b0dc18e58c7693b198cb8', 99, 3, 'pending', '2026-06-02 10:29:22', NULL, '168.210.241.18', NULL, '2026-06-02 13:24:22'),
(49, '4bf36ea507b71721c8e2708ede659e395cdb74b2ea18b566e1b324222b15240e', 99, 3, 'pending', '2026-06-02 10:32:44', NULL, '168.210.241.18', NULL, '2026-06-02 13:27:44'),
(50, 'bc751f33db7d53f328d4bf6e4dd002b7ee2023faee4143bcbbfa33dd2d87d277', 99, 3, 'pending', '2026-06-02 10:53:20', NULL, '168.210.241.18', NULL, '2026-06-02 13:48:20'),
(51, '61f1945ba868acd0ff92dbf7eeb27953e17cd96d32d139f751b98778a5c3275d', 99, 3, 'pending', '2026-06-02 10:54:18', NULL, '168.210.241.18', NULL, '2026-06-02 13:49:18'),
(52, '4149fd2ab793d17b95264ec572028f4af156f8606529d093a37a284901787c41', 99, 3, 'pending', '2026-06-02 10:57:17', NULL, '168.210.241.18', NULL, '2026-06-02 13:52:17'),
(53, '5873ac3b85cc986719cf93899330e08764675346761281859504e5adcdb3af8c', 99, 3, 'pending', '2026-06-02 10:57:44', NULL, '168.210.241.18', NULL, '2026-06-02 13:52:44'),
(54, '778d0f9661da06a838d80ec59171ec42a27e221f7de101e6e923e4baa9127537', 99, 3, 'pending', '2026-06-02 11:00:18', NULL, '168.210.241.18', NULL, '2026-06-02 13:55:18'),
(55, 'cfa345be52ae2beb5685c91fd5c976e9a504787cbaac697d6f2a3d5d8f445499', 99, 3, 'pending', '2026-06-02 11:05:06', NULL, '168.210.241.18', NULL, '2026-06-02 14:00:06'),
(56, '7e53e1e40c54a7d19b9f6c8f05f801ea9d0eabfe5108a46005605d1c7cbc6711', 99, 3, 'pending', '2026-06-02 11:05:22', NULL, '168.210.241.18', NULL, '2026-06-02 14:00:22'),
(57, '811a16871922b87e1a855765a05e7b77647f42b3b0ead62b92a6b08c417c0bc3', 99, 3, 'pending', '2026-06-02 11:07:16', NULL, '168.210.241.18', NULL, '2026-06-02 14:02:16'),
(58, '5c0bca067e59e1fe0d5319e29b9a71bc897e2498194a59d8eb5d0fb7a8e2ac35', 99, 3, 'pending', '2026-06-03 06:18:35', NULL, '168.210.241.18', NULL, '2026-06-03 09:13:35'),
(59, 'ed371346b934a79edec827d348d8948a918dca8b06074cbc934c9772f871405b', 99, 3, 'pending', '2026-06-03 06:19:00', NULL, '168.210.241.18', NULL, '2026-06-03 09:14:00'),
(60, 'd9aa313a40c75740be4550ab9b39bf443f983941859bba03465e963554e55b9e', 99, 3, 'pending', '2026-06-03 06:22:00', NULL, '168.210.241.18', NULL, '2026-06-03 09:17:00'),
(61, '1a997803ee5cd409a61a1810d1bd8381885b8cdb5f26c537ac56abb24a50a76a', 1, 1, 'pending', '2026-06-10 07:16:01', NULL, '105.1.170.184', NULL, '2026-06-10 10:11:01'),
(62, 'd5aa0f2706495d4da7823bef7c1f53fe209daa9ca22cade55d03bf237d389f08', 1, 1, 'pending', '2026-06-10 07:17:32', NULL, '105.1.170.184', NULL, '2026-06-10 10:12:32');

-- --------------------------------------------------------

--
-- Table structure for table `credit_ledger`
--

CREATE TABLE `credit_ledger` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `type` enum('purchase','call_debit','refund','bonus','adjustment','payout') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,4) NOT NULL,
  `balance_after` decimal(10,4) NOT NULL,
  `reference_id` int(10) UNSIGNED DEFAULT NULL,
  `reference_type` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `credit_ledger`
--

INSERT INTO `credit_ledger` (`id`, `user_id`, `type`, `amount`, `balance_after`, `reference_id`, `reference_type`, `notes`, `created_at`) VALUES
(1, 1, 'purchase', 45.0000, 45.0000, 1, 'transaction', 'PayFast ref: SANDBOX-97738E1B', '2026-05-27 13:23:59'),
(2, 1, 'purchase', 105.0000, 150.0000, 2, 'transaction', 'PayFast ref: SANDBOX-07EB2E22', '2026-05-29 10:12:48'),
(3, 99, 'purchase', 15.0000, 15.0000, 3, 'transaction', 'PayFast ref: SANDBOX-21AF17C9', '2026-06-02 08:45:27'),
(4, 99, 'call_debit', -2.0000, 13.0000, 19, 'call', NULL, '2026-06-02 09:37:04'),
(5, 99, 'call_debit', -2.0000, 11.0000, 20, 'call', NULL, '2026-06-02 09:38:00'),
(6, 99, 'call_debit', -2.0000, 9.0000, 20, 'call', NULL, '2026-06-02 09:39:01'),
(7, 99, 'call_debit', -2.0000, 7.0000, 20, 'call', NULL, '2026-06-02 09:40:02'),
(8, 99, 'call_debit', -2.0000, 5.0000, 21, 'call', NULL, '2026-06-02 09:47:28'),
(9, 99, 'call_debit', -2.0000, 3.0000, 21, 'call', NULL, '2026-06-02 09:48:29'),
(10, 99, 'purchase', 105.0000, 108.0000, 4, 'transaction', 'PayFast ref: SANDBOX-2BDC183E', '2026-06-02 09:49:37'),
(11, 99, 'call_debit', -2.0000, 106.0000, 22, 'call', NULL, '2026-06-02 09:52:20'),
(12, 99, 'call_debit', -2.0000, 104.0000, 22, 'call', NULL, '2026-06-02 09:53:21'),
(13, 99, 'call_debit', -2.0000, 102.0000, 23, 'call', NULL, '2026-06-02 09:58:01'),
(14, 99, 'call_debit', -2.0000, 100.0000, 24, 'call', NULL, '2026-06-02 09:58:29'),
(15, 99, 'call_debit', -2.0000, 8.0000, 25, 'call', NULL, '2026-06-02 10:04:05'),
(16, 99, 'call_debit', -2.0000, 6.0000, 25, 'call', NULL, '2026-06-02 10:05:06'),
(17, 99, 'call_debit', -2.0000, 4.0000, 25, 'call', NULL, '2026-06-02 10:06:06'),
(18, 99, 'call_debit', -2.0000, 2.0000, 25, 'call', NULL, '2026-06-02 10:07:06'),
(19, 99, 'call_debit', -2.0000, 0.0000, 25, 'call', NULL, '2026-06-02 10:08:06'),
(20, 99, 'purchase', 15.0000, 15.0000, 5, 'transaction', 'PayFast ref: SANDBOX-AA465C7E', '2026-06-02 10:44:52'),
(21, 99, 'call_debit', -1.0000, 14.0000, 26, 'call', NULL, '2026-06-02 10:45:28'),
(22, 99, 'call_debit', -1.0000, 13.0000, 27, 'call', NULL, '2026-06-02 10:49:30'),
(23, 99, 'call_debit', -1.0000, 12.0000, 27, 'call', NULL, '2026-06-02 10:50:31'),
(24, 99, 'call_debit', -1.0000, 11.0000, 30, 'call', NULL, '2026-06-02 11:31:39'),
(25, 99, 'call_debit', -1.0000, 10.0000, 32, 'call', NULL, '2026-06-02 11:51:56'),
(26, 99, 'call_debit', -1.0000, 9.0000, 33, 'call', NULL, '2026-06-02 11:54:15'),
(27, 99, 'call_debit', -1.0000, 8.0000, 33, 'call', NULL, '2026-06-02 11:55:15'),
(28, 99, 'call_debit', -1.0000, 7.0000, 34, 'call', NULL, '2026-06-02 11:57:51'),
(29, 99, 'call_debit', -1.0000, 6.0000, 34, 'call', NULL, '2026-06-02 11:58:52'),
(30, 99, 'call_debit', -1.0000, 5.0000, 34, 'call', NULL, '2026-06-02 12:01:00'),
(31, 99, 'call_debit', -1.0000, 4.0000, 34, 'call', NULL, '2026-06-02 12:01:52'),
(32, 99, 'call_debit', -1.0000, 3.0000, 34, 'call', NULL, '2026-06-02 12:03:08'),
(33, 99, 'call_debit', -1.0000, 2.0000, 34, 'call', NULL, '2026-06-02 12:03:53'),
(34, 99, 'call_debit', -1.0000, 1.0000, 34, 'call', NULL, '2026-06-02 12:04:52'),
(35, 99, 'call_debit', -1.0000, 0.0000, 34, 'call', NULL, '2026-06-02 12:05:52'),
(36, 99, 'purchase', 15.0000, 15.0000, 6, 'transaction', 'PayFast ref: SANDBOX-E5D3171E', '2026-06-02 12:08:16'),
(37, 99, 'call_debit', -1.0000, 14.0000, 35, 'call', NULL, '2026-06-02 12:08:51'),
(38, 99, 'call_debit', -1.0000, 13.0000, 36, 'call', NULL, '2026-06-02 12:13:56'),
(39, 99, 'purchase', 105.0000, 118.0000, 7, 'transaction', 'PayFast ref: SANDBOX-104D5501', '2026-06-02 12:51:08'),
(40, 99, 'call_debit', -15.0000, 103.0000, 45, 'call', NULL, '2026-06-02 13:21:35'),
(41, 99, 'call_debit', -1.0000, 102.0000, 46, 'call', NULL, '2026-06-02 13:22:16'),
(42, 99, 'purchase', 45.0000, 45.0000, 8, 'transaction', 'PayFast ref: SANDBOX-2C80C654', '2026-06-02 13:24:01'),
(43, 99, 'call_debit', -15.0000, 30.0000, 47, 'call', NULL, '2026-06-02 13:24:33'),
(44, 99, 'call_debit', -15.0000, 15.0000, 48, 'call', NULL, '2026-06-02 13:27:50'),
(45, 99, 'call_debit', -5.0000, 10.0000, 49, 'call', NULL, '2026-06-02 13:48:31'),
(46, 99, 'call_debit', -5.0000, 5.0000, 51, 'call', NULL, '2026-06-02 13:52:27'),
(47, 99, 'call_debit', -1.0000, 4.0000, 53, 'call', NULL, '2026-06-02 13:55:27'),
(48, 99, 'purchase', 105.0000, 109.0000, 9, 'transaction', 'PayFast ref: SANDBOX-B10DA8E0', '2026-06-02 13:59:51'),
(49, 99, 'call_debit', -1.0000, 108.0000, 54, 'call', NULL, '2026-06-02 14:00:13'),
(50, 99, 'call_debit', -5.0000, 103.0000, 55, 'call', NULL, '2026-06-02 14:00:32'),
(51, 99, 'call_debit', -5.0000, 98.0000, 56, 'call', NULL, '2026-06-02 14:02:27'),
(52, 99, 'call_debit', -1.0000, 97.0000, 57, 'call', NULL, '2026-06-03 09:13:46'),
(53, 99, 'call_debit', -5.0000, 92.0000, 58, 'call', NULL, '2026-06-03 09:14:11'),
(54, 99, 'call_debit', -1.0000, 91.0000, 59, 'call', NULL, '2026-06-03 09:17:09'),
(55, 1, 'purchase', 15.0000, 15.0000, 21, 'transaction', 'PayFast ref: 305932569', '2026-06-04 06:51:33'),
(56, 1, 'purchase', 15.0000, 30.0000, 20, 'transaction', 'PayFast ref: 305920789', '2026-06-04 06:59:17'),
(57, 1, 'purchase', 15.0000, 45.0000, 18, 'transaction', 'PayFast ref: 305907227', '2026-06-04 07:00:51'),
(58, 1, 'purchase', 15.0000, 60.0000, 19, 'transaction', 'PayFast ref: 305917977', '2026-06-04 08:16:50'),
(59, 1, 'call_debit', -1.0000, 59.0000, 60, 'call', NULL, '2026-06-10 10:11:14'),
(60, 1, 'call_debit', -15.0000, 44.0000, 61, 'call', NULL, '2026-06-10 10:12:44');

-- --------------------------------------------------------

--
-- Table structure for table `credit_packages`
--

CREATE TABLE `credit_packages` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `credits` decimal(10,4) NOT NULL,
  `price_zar` decimal(8,2) NOT NULL,
  `price_eur` decimal(8,2) DEFAULT NULL,
  `price_gbp` decimal(8,2) DEFAULT NULL,
  `price_usd` decimal(8,2) DEFAULT NULL,
  `bonus_credits` decimal(10,4) DEFAULT '0.0000',
  `is_featured` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `sort_order` int(11) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `credit_packages`
--

INSERT INTO `credit_packages` (`id`, `name`, `credits`, `price_zar`, `price_eur`, `price_gbp`, `price_usd`, `bonus_credits`, `is_featured`, `is_active`, `sort_order`, `created_at`) VALUES
(1, 'Starter', 15.0000, 5.33, 0.26, 6.50, 9.00, 0.0000, 0, 1, 1, '2026-05-27 13:19:56'),
(2, 'Gentleman', 40.0000, 350.00, 18.00, 15.00, 20.00, 5.0000, 1, 1, 2, '2026-05-27 13:19:56'),
(3, 'Elite', 90.0000, 700.00, 36.00, 30.00, 40.00, 15.0000, 0, 1, 3, '2026-05-27 13:19:56');

-- --------------------------------------------------------

--
-- Table structure for table `email_queue`
--

CREATE TABLE `email_queue` (
  `id` int(10) UNSIGNED NOT NULL,
  `to_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `to_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body_html` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `body_text` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','sent','failed') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `attempts` tinyint(3) UNSIGNED DEFAULT '0',
  `last_attempt` timestamp NULL DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `performers`
--

CREATE TABLE `performers` (
  `id` int(10) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `display_name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `age` tinyint(3) UNSIGNED NOT NULL,
  `phone_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_verified` tinyint(1) NOT NULL DEFAULT '0',
  `rate_per_minute` decimal(6,2) NOT NULL DEFAULT '1.00',
  `video_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `video_min_credits` decimal(10,2) DEFAULT '15.00',
  `video_min_minutes` int(11) DEFAULT '10',
  `video_rate_per_minute` decimal(10,2) DEFAULT '5.00',
  `status` enum('active','offline','busy','suspended','pending_approval') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending_approval',
  `online_status` tinyint(1) NOT NULL DEFAULT '0',
  `last_seen_at` timestamp NULL DEFAULT NULL,
  `profile_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cover_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `short_video` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `voice_sample` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` set('chat','roleplay','fantasy','couples','mature','fetish') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'chat',
  `languages` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'English',
  `rating_avg` decimal(3,2) DEFAULT '0.00',
  `rating_count` int(10) UNSIGNED DEFAULT '0',
  `total_calls` int(10) UNSIGNED DEFAULT '0',
  `total_minutes` int(10) UNSIGNED DEFAULT '0',
  `earnings_balance` decimal(10,4) DEFAULT '0.0000',
  `earnings_total` decimal(10,4) DEFAULT '0.0000',
  `commission_rate` decimal(5,2) DEFAULT '40.00',
  `approved_by` int(10) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `performers`
--

INSERT INTO `performers` (`id`, `uuid`, `user_id`, `display_name`, `slug`, `bio`, `age`, `phone_number`, `phone_verified`, `rate_per_minute`, `video_enabled`, `video_min_credits`, `video_min_minutes`, `video_rate_per_minute`, `status`, `online_status`, `last_seen_at`, `profile_photo`, `cover_photo`, `short_video`, `voice_sample`, `category`, `languages`, `rating_avg`, `rating_count`, `total_calls`, `total_minutes`, `earnings_balance`, `earnings_total`, `commission_rate`, `approved_by`, `approved_at`, `created_at`, `updated_at`) VALUES
(1, 'de0cd222-427b-4b31-afc4-907b2b8b67d6', 2, 'Sexy Roxy', 'sexy-roxy', 'I\'m a petite lonely girl just waiting at home for a man to show me how princesses are really treated!', 18, '5455555555', 1, 1.00, 0, 15.00, 10, 5.00, 'active', 1, NULL, 'uploads/performers/1/425bcac504aeb329c5d7203f603d3608.jpg', 'uploads/performers/1/4e85736d5f1e85c9698e76f801151aaf.jpg', 'uploads/performers/1/video_3bd49e0780235139.mp4', 'uploads/performers/1/voice_f027234b081deb90.mp3', 'fantasy', 'French', 4.50, 71, 77, 2, 10.8000, 10.8000, 40.00, NULL, NULL, '2026-05-28 13:06:54', '2026-06-10 10:13:39'),
(3, '69ca4f40-446c-4788-9417-e730d7a71ef4', 4, 'Zonika', 'zonika', 'Your horny Polish girl. I\'m here because I know your wife will not do to you what I love doing.', 20, '5252525252', 1, 1.00, 0, 5.00, 10, 5.00, 'active', 1, '2026-06-02 12:12:44', 'uploads/performers/3/1ac8526eced2f249cd16832364307ff6.jpg', 'uploads/performers/3/9c8d2f436aecde7bca79ad8a55bb66f4.jpg', 'uploads/performers/3/video_e78979a8012be876.mp4', 'uploads/performers/3/voice_f9bc83e1bbf78980.mp3', 'roleplay', 'Polish', 5.00, 107, 146, 14, 32.4000, 32.4000, 40.00, NULL, NULL, '2026-05-28 13:23:21', '2026-06-03 09:18:41'),
(4, 'b0278c5e-3d08-4b7c-aada-b53a732baf8d', 5, 'Sky Hayes', 'sky-hayes', 'Always horny and always ready for the best cocks in my face!', 24, '+27821814916', 1, 2.00, 0, 15.00, 10, 5.00, 'active', 1, '2026-06-02 10:43:07', 'uploads/performers/4/71c8a4bc80140a2dea71436c7162e31d.jpg', 'uploads/performers/4/f647e081b9fa2e0399bc254d3d8021d1.jpg', NULL, NULL, 'fetish', 'English, French', 5.00, 52, 69, 0, 12.0000, 12.0000, 40.00, NULL, NULL, '2026-05-28 14:05:42', '2026-06-03 09:31:31'),
(5, '1411c026-4dce-41bb-84fd-b8b9af235415', 6, 'Grace Rose', 'grace-rose', '', 25, '+27828062882', 1, 5.00, 0, 15.00, 10, 5.00, 'active', 1, '2026-06-02 08:36:05', 'uploads/performers/5/ee3aa06e1108bca18d8c969398b09cd8.jpg', 'uploads/performers/5/0da6ef864acc0df135785f25b72b3ca5.jpg', NULL, NULL, 'fantasy', 'English, Spanish', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:42', '2026-06-03 09:19:51'),
(6, '3b36bf94-f962-41c2-ab3c-4e3229f76d9d', 7, 'Sophia Vance', 'sophia-vance', '', 18, '+27829743589', 1, 10.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'chat', 'English, Italian', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:42', '2026-06-03 09:19:25'),
(7, 'd5c61dad-ebab-40d3-acd9-0a2102d9a232', 8, 'Honey Rose', 'honey-rose', '', 25, '+27826166526', 1, 8.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'mature', 'English, Spanish', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:42', '2026-06-03 09:19:01'),
(8, '190850b0-fc70-4951-9bf3-372ee2931fcb', 9, 'Chloe Cole', 'chloe-cole', '', 31, '+27824039794', 1, 9.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'fetish', 'English, Italian', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:42', '2026-06-03 09:18:50'),
(9, '5a52d76e-0d85-440a-93e1-155097ff03e6', 10, 'Ivy Pierce', 'ivy-pierce', '', 31, '+27823174634', 1, 6.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'roleplay', 'English, Italian', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:43', '2026-06-03 09:33:15'),
(10, '39bf3f22-31fa-492b-af5f-1cd67b6b1129', 11, 'Carmen Velvet', 'carmen-velvet', '', 19, '+27823272702', 1, 3.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'roleplay', 'English, Italian', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:43', '2026-06-03 09:32:59'),
(11, 'e5dda1e2-b518-49fb-a529-779af47bddf4', 12, 'Roxy Hayes', 'roxy-hayes', '', 33, '+27821379063', 1, 3.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'chat', 'English, French', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:43', '2026-06-03 09:32:03'),
(12, '10a7cf7a-1674-4982-b136-aff9551c50dc', 13, 'Ivy Monroe', 'ivy-monroe', '', 22, '+27824990618', 1, 5.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'couples', 'English, Portuguese', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:43', '2026-06-03 09:31:54'),
(13, 'b26282f2-7dd1-4007-add5-75075ded846a', 14, 'Brooke Fox', 'brooke-fox', '', 23, '+27827129652', 1, 10.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'mature', 'English', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:43', '2026-06-03 09:31:43'),
(14, '24a86e62-6e4d-49db-87c3-d76a29456bcd', 15, 'Aurora Star', 'aurora-star', '', 22, '+27821050614', 1, 6.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'fantasy', 'English', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:44', '2026-06-03 09:34:12'),
(15, 'e58f284e-3c2b-442b-a49e-4e8bb1fd9a4e', 16, 'Lexi Wild', 'lexi-wild', '', 23, '+27829471839', 1, 10.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'roleplay', 'English, Spanish', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:44', '2026-06-03 09:34:00'),
(16, 'ecef9383-0312-4f4d-9756-0b616036f1a3', 17, 'Carmen Stone', 'carmen-stone', '', 29, '+27822518291', 1, 4.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'roleplay', 'English', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:44', '2026-06-03 09:33:49'),
(17, '2ab0a707-1ee3-45cf-b5f7-786c7a122b87', 18, 'Ruby Monroe', 'ruby-monroe', '', 22, '+27824379189', 1, 6.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'roleplay', 'English, Spanish', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:44', '2026-06-03 09:33:38'),
(18, '8a0fa480-6c4a-465a-89ee-eaa055981ebf', 19, 'Scarlett Pierce', 'scarlett-pierce', '', 18, '+27823016997', 1, 8.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'mature', 'English, Italian', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:44', '2026-06-03 09:33:28'),
(19, 'f45c348f-9eda-49f6-b23c-5a5ff52cd030', 20, 'Lexi Hayes', 'lexi-hayes', '', 34, '+27829318310', 1, 4.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'fantasy', 'English, French', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:45', '2026-06-03 09:35:06'),
(20, 'd7f64494-b8ae-418b-9ab3-4d5fd606f3e5', 21, 'Autumn Hayes', 'autumn-hayes', '', 27, '+27829249173', 1, 7.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'couples', 'English, German', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:45', '2026-06-03 09:34:53'),
(21, '8d84a84a-dbe8-4a73-875e-59c39eebfcc3', 22, 'Summer Rose', 'summer-rose', '', 31, '+27827791011', 1, 10.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'roleplay', 'English, French', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:45', '2026-06-03 09:34:40'),
(22, '039b74c3-087c-4735-ad09-61ff9a20e64a', 23, 'Aurora Brooks', 'aurora-brooks', '', 34, '+27822664896', 1, 3.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'fetish', 'English, French', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:45', '2026-06-03 09:34:27'),
(23, '3da1d852-f8bd-494f-93bd-9ffab0f71f21', 24, 'Sky Fox', 'sky-fox', '', 31, '+27825947838', 1, 10.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'chat', 'English, French', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:46', '2026-06-03 09:29:40'),
(24, 'fdae6010-c640-4094-bf50-d09e62500acb', 25, 'Penelope Knight', 'penelope-knight', '', 34, '+27825457457', 1, 7.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'roleplay', 'English, German', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:46', '2026-06-03 09:30:11'),
(25, '1919c9aa-132b-40a1-8b3b-cb33e977513a', 26, 'Serena Summers', 'serena-summers', '', 26, '+27826998201', 1, 6.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'fantasy', 'English, French', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:46', '2026-06-03 09:30:49'),
(26, 'a993adb8-da31-4747-b099-ae9b52bd5d0b', 27, 'Summer Winters', 'summer-winters', '', 19, '+27824688475', 1, 5.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'chat', 'English, French', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:46', '2026-06-03 09:31:08'),
(27, '9182da3f-7f19-4d81-900e-47fa28dcb30a', 28, 'Nina Pierce', 'nina-pierce', '', 18, '+27827213889', 1, 4.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'mature', 'English, German', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:46', '2026-06-03 09:35:19'),
(28, 'e21c0100-9c54-4734-987b-2b12105b7fc6', 29, 'Nina Vance', 'nina-vance', '', 25, '+27828290455', 1, 8.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'roleplay', 'English, German', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:47', '2026-06-03 09:28:08'),
(29, 'b8622015-f09c-4bf8-a721-11ac7ce38419', 30, 'Amber Cole', 'amber-cole', '', 21, '+27823769673', 1, 3.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'fantasy', 'English', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:47', '2026-06-03 09:28:24'),
(30, '0309b632-212d-491f-b0a8-9d87f9bb9640', 31, 'Natalia Monroe', 'natalia-monroe', '', 28, '+27825229502', 1, 3.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'fantasy', 'English', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:47', '2026-06-03 09:28:47'),
(31, '286b2cd4-fe39-4f26-a74a-1d78c383e6b8', 32, 'Penelope Pierce', 'penelope-pierce', '', 20, '+27829175306', 1, 4.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'fetish', 'English, Italian', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:47', '2026-06-03 09:29:06'),
(32, 'b563994b-117a-470b-ba7c-691d2a50c83f', 33, 'Summer Sterling', 'summer-sterling', '', 28, '+27822280911', 1, 5.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'chat', 'English, Portuguese', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:47', '2026-06-03 09:29:22'),
(33, '54d0f9ec-17b5-4bcb-8144-f39a9853842b', 34, 'Penelope Summers', 'penelope-summers', NULL, 35, '+27821928463', 1, 4.00, 1, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'couples', 'English, Italian', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:48', '2026-06-02 12:11:15'),
(34, '8e2e9402-7a97-4ba5-bd5d-38593f0b74e0', 35, 'Sky Winters', 'sky-winters', NULL, 22, '+27828139600', 1, 6.00, 1, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'fantasy', 'English, French', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:48', '2026-06-02 12:11:15'),
(35, 'b60b9dbf-c56b-407e-b88a-43ce985642f6', 36, 'Natalia Star', 'natalia-star', '', 26, '+27826459217', 1, 3.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'chat', 'English, Portuguese', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:48', '2026-06-03 09:27:02'),
(36, '4b55627b-380a-4131-a4f4-7beb89650f3c', 37, 'Luna Pierce', 'luna-pierce', '', 33, '+27826831569', 1, 3.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'fetish', 'English, Italian', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:48', '2026-06-03 09:27:21'),
(37, 'c5c9489d-9977-4a24-be2a-c07d3374acaa', 38, 'Mia Cole', 'mia-cole', '', 33, '+27828969876', 1, 8.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'mature', 'English', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:48', '2026-06-03 09:27:40'),
(38, 'b761583e-8334-4ad0-a25f-c1be8d3b7fe3', 39, 'Serena Wild', 'serena-wild', NULL, 23, '+27826046615', 1, 10.00, 1, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'roleplay', 'English, French', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:49', '2026-06-02 12:11:15'),
(39, '4b932ab6-347c-4586-8767-b81a24a93667', 40, 'Nina Delaney', 'nina-delaney', NULL, 20, '+27826416659', 1, 5.00, 1, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'mature', 'English, French', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:49', '2026-06-02 12:11:15'),
(40, '731ed2f7-ca40-4666-9998-f6b71d7d5268', 41, 'Penelope Blair', 'penelope-blair', NULL, 21, '+27829983619', 1, 9.00, 1, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'chat', 'English, Spanish', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:49', '2026-06-02 12:11:15'),
(41, '4eab204d-4407-4348-af64-e67913cc0219', 42, 'Bella Vance', 'bella-vance', NULL, 25, '+27828907717', 1, 4.00, 1, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'couples', 'English', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:49', '2026-06-02 12:11:15'),
(42, 'a1f1c852-99a7-48a8-a860-1bdbb2323036', 43, 'Jade Knight', 'jade-knight', NULL, 28, '+27822645819', 1, 5.00, 1, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'chat', 'English, French', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:49', '2026-06-02 12:11:15'),
(43, '054c7856-eb5e-4b42-bc0f-cc81cde02b17', 44, 'Carmen Vance', 'carmen-vance', NULL, 25, '+27825202367', 1, 8.00, 1, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'roleplay', 'English', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:50', '2026-06-02 12:11:15'),
(44, '02dde6f7-8a3e-4c1e-b666-530b64866b82', 45, 'Ivy Delaney', 'ivy-delaney', NULL, 34, '+27826678526', 1, 9.00, 1, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'roleplay', 'English, Portuguese', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:50', '2026-06-02 12:11:15'),
(45, '12c70ffa-8c5f-4928-af37-26dffe53fa2f', 46, 'Ivy Brooks', 'ivy-brooks', NULL, 32, '+27822791035', 1, 4.00, 1, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'couples', 'English, French', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:50', '2026-06-02 12:11:15'),
(46, 'c15e6151-edbb-4a88-b747-dffa148b47c8', 47, 'Paris Summers', 'paris-summers', NULL, 34, '+27829130315', 1, 10.00, 1, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'mature', 'English, Italian', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:50', '2026-06-02 12:11:15'),
(47, '13345dec-6480-46a9-a7a7-964653dd6330', 48, 'Scarlett Brooks', 'scarlett-brooks', NULL, 23, '+27826846968', 1, 10.00, 1, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'fetish', 'English, Italian', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:51', '2026-06-02 12:11:15'),
(48, '7ab2eb14-1744-4357-9257-a47c8dcdcb98', 49, 'Lily Rose', 'lily-rose', NULL, 22, '+27825678438', 1, 3.00, 1, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'fetish', 'English, Portuguese', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:51', '2026-06-02 12:11:15'),
(49, '2df3c714-d1d2-48eb-bc8a-0f71887cbe93', 50, 'Luna Velvet', 'luna-velvet', NULL, 31, '+27823671953', 1, 4.00, 1, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'fantasy', 'English, Spanish', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:51', '2026-06-02 12:11:15'),
(50, '1c6e0a30-2fbc-4448-bb1c-af1d42d13c1e', 51, 'Roxy Fox', 'roxy-fox', NULL, 18, '+27829740329', 1, 9.00, 1, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'roleplay', 'English, Italian', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:51', '2026-06-02 12:11:15'),
(51, 'de0adaf1-2fc7-47d7-bd9e-3f74aaaa27a9', 52, 'Serena Pierce', 'serena-pierce', NULL, 32, '+27823158116', 1, 5.00, 1, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'couples', 'English, Spanish', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:51', '2026-06-02 12:11:15'),
(52, 'c7c0303e-b4d0-4634-ac5a-f8f1b869ae7b', 53, 'Penelope Delaney', 'penelope-delaney', NULL, 23, '+27821319865', 1, 6.00, 1, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'mature', 'English, Italian', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:52', '2026-06-02 12:11:15'),
(53, '4133d8a3-7bc2-409b-913b-7905c199ce7f', 54, 'Angel Summers', 'angel-summers', NULL, 33, '+27824175177', 1, 9.00, 1, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'fetish', 'English, German', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:52', '2026-06-02 12:11:15'),
(54, '0eec7dc9-a325-4cb8-9d0a-82eb359ac269', 55, 'Summer Winters', 'summer-winters-51-1', NULL, 32, '+27826213780', 1, 5.00, 1, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'fetish', 'English, French', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:52', '2026-06-02 12:11:15'),
(55, '929ed9e2-8750-445c-9604-78c1f7e70909', 56, 'Luna Fox', 'luna-fox', NULL, 30, '+27824732510', 1, 6.00, 1, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'fetish', 'English, Italian', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:52', '2026-06-02 12:11:15'),
(56, 'f52f27a4-b1ec-49e8-8490-163505339bab', 57, 'Stella Summers', 'stella-summers', '', 19, '+27827350319', 1, 6.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'fetish', 'English, Portuguese', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:53', '2026-06-03 09:25:55'),
(57, 'a4fcd2f5-120f-43a1-a3a9-33cd8882437a', 58, 'Sophia Pierce', 'sophia-pierce', '', 25, '+27829503142', 1, 4.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'couples', 'English, Portuguese', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:53', '2026-06-03 09:26:16'),
(58, 'adb04726-01a4-40bb-84d6-8930fdf0ce74', 59, 'Elena Star', 'elena-star', '', 33, '+27824968769', 1, 4.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'chat', 'English, German', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:53', '2026-06-03 09:26:26'),
(59, '2a42e5f0-f5c4-46ee-9b75-ecd89f5d2776', 60, 'Sky Winters', 'sky-winters-56-1', '', 22, '+27824157893', 1, 10.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'mature', 'English, Italian', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:53', '2026-06-03 09:26:44'),
(60, '4b0a1165-0f7e-490d-a556-d602a2d5508c', 61, 'Penelope Rose', 'penelope-rose', NULL, 34, '+27824650808', 1, 10.00, 1, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'roleplay', 'English, Portuguese', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:53', '2026-06-02 12:11:15'),
(61, 'd87dbfbb-8f68-4760-a240-075d145f9f5e', 62, 'Chloe Summers', 'chloe-summers', '', 24, '+27825309160', 1, 10.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'couples', 'English', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:54', '2026-06-03 09:25:00'),
(62, 'a351b0bc-1bd9-4a29-a4f9-1ccde0503ace', 63, 'Serena Cole', 'serena-cole', '', 32, '+27827358486', 1, 10.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'fetish', 'English, French', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:54', '2026-06-03 09:25:09'),
(63, '7c716ce5-2440-46e2-9c62-1cb01744ce26', 64, 'Carmen Winters', 'carmen-winters', '', 28, '+27827896484', 1, 6.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'chat', 'English, Italian', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:54', '2026-06-03 09:25:24'),
(64, 'd9d7f449-0d53-4d84-8999-f95b4b2d0910', 65, 'Luna Delaney', 'luna-delaney', '', 34, '+27829229509', 1, 10.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'fantasy', 'English', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:54', '2026-06-03 09:25:36'),
(65, 'b25e22c5-6548-4a28-94ae-0f2503498d20', 66, 'Elena Thorne', 'elena-thorne', '', 18, '+27825231246', 1, 4.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'roleplay', 'English, Italian', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:54', '2026-06-03 09:25:47'),
(66, '494938e4-ecd1-46be-9c18-5f1d66225d44', 67, 'Angel Sterling', 'angel-sterling', '', 31, '+27829999507', 1, 4.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'mature', 'English', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:55', '2026-06-03 09:23:59'),
(67, 'baa57de5-31cc-4657-9f1a-8d1187e2f3b4', 68, 'Violet Velvet', 'violet-velvet', '', 35, '+27826830167', 1, 4.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'mature', 'English, Italian', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:55', '2026-06-03 09:24:08'),
(68, '10522841-f3bd-4899-95f9-7075b3fdaa43', 69, 'Nina Fox', 'nina-fox', '', 26, '+27829438657', 1, 3.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'mature', 'English, Spanish', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:55', '2026-06-03 09:24:26'),
(69, 'c41dc876-b046-4604-af32-bf015e4af7b0', 70, 'Sky Chase', 'sky-chase', '', 25, '+27823294070', 1, 3.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'roleplay', 'English, Spanish', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:55', '2026-06-03 09:24:35'),
(70, '6a8fa387-fb0f-4011-ac69-db1aad226ec4', 71, 'Chloe Velvet', 'chloe-velvet', '', 30, '+27822700075', 1, 7.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'fantasy', 'English', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:55', '2026-06-03 09:24:44'),
(71, '22864d42-2fb0-47ea-bf4f-04a2f4053ab6', 72, 'Sienna Rose', 'sienna-rose', '', 23, '+27822031110', 1, 5.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'chat', 'English, Portuguese', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:56', '2026-06-03 09:23:23'),
(72, '579c12cd-2291-4674-9630-814f44cd731f', 73, 'Elena Monroe', 'elena-monroe', '', 25, '+27829286169', 1, 9.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'mature', 'English, German', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:56', '2026-06-03 09:23:31'),
(73, 'e93ecca6-8923-46d9-96db-77de596cb39f', 74, 'Bianca Vance', 'bianca-vance', '', 21, '+27828107483', 1, 6.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'fantasy', 'English, German', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:56', '2026-06-03 09:23:40'),
(74, 'd899e0f9-6567-4d10-9590-20848c6e45a3', 75, 'Natalia Vance', 'natalia-vance', '', 33, '+27824396483', 1, 6.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'fantasy', 'English', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:56', '2026-06-03 09:23:50'),
(75, 'd722a6c0-9f25-4290-a1db-73893aa05da5', 76, 'Nicole Knight', 'nicole-knight', NULL, 22, '+27823988866', 1, 10.00, 1, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'chat', 'English, Portuguese', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:57', '2026-06-02 12:11:15'),
(76, 'f049c7c0-9269-480f-aac9-76087b8eb468', 77, 'Brooke Vance', 'brooke-vance', NULL, 33, '+27825270061', 1, 3.00, 1, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'couples', 'English, German', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:57', '2026-06-02 12:11:15'),
(77, 'fa8d9938-de74-47d0-9908-b4f274a90e83', 78, 'Amber Pierce', 'amber-pierce', NULL, 34, '+27825022938', 1, 8.00, 1, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'chat', 'English, Spanish', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:57', '2026-06-02 12:11:15'),
(78, 'edd1d1e6-9e25-496e-abcc-0804f5bbd96d', 79, 'Paris Velvet', 'paris-velvet', '', 33, '+27829875265', 1, 10.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'roleplay', 'English', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:57', '2026-06-03 09:23:02'),
(79, 'c05ba614-f6c5-4da2-9aae-7cd5898417ca', 80, 'Scarlett Stone', 'scarlett-stone', '', 25, '+27824277707', 1, 7.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'fantasy', 'English, French', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:57', '2026-06-03 09:23:12'),
(80, 'c882a9b0-9335-4e1b-8118-1cbf00cab530', 81, 'Mia Stone', 'mia-stone', '', 19, '+27826516870', 1, 8.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'couples', 'English, German', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:58', '2026-06-03 09:22:17'),
(81, 'bc7c8170-7c2b-43fe-b5c2-def69984f33b', 82, 'Grace Thorne', 'grace-thorne', '', 20, '+27828347365', 1, 8.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'fetish', 'English, Spanish', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:58', '2026-06-03 09:22:27'),
(82, 'b86c9e53-a1a2-4693-8972-2e77791b2f45', 83, 'Ivy Brooks', 'ivy-brooks-79-1', '', 18, '+27829590274', 1, 10.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'mature', 'English, French', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:58', '2026-06-03 09:22:37'),
(83, '4cdd95a3-1ec3-4734-9011-0e8831a0d01a', 84, 'Carmen Delaney', 'carmen-delaney', '', 26, '+27821955094', 1, 5.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'chat', 'English, Portuguese', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:58', '2026-06-03 09:22:46'),
(84, '6c9da7da-8e75-4cd7-a29b-d8ae1340ffa3', 85, 'Amber Chase', 'amber-chase', '', 24, '+27827909971', 1, 7.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'roleplay', 'English, French', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:58', '2026-06-03 09:22:54'),
(85, 'b22e50da-25e6-4b7c-a69c-9d661a11e815', 86, 'Elena Velvet', 'elena-velvet', '', 26, '+27825062886', 1, 8.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, 'uploads/performers/85/9fd59e7146d7ce06704ca7ab1de3b502.jpg', NULL, NULL, NULL, 'roleplay', 'English, Spanish', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:59', '2026-06-03 09:20:51'),
(86, '06bf3018-d64d-4184-9541-c048090c30a5', 87, 'Jade Chase', 'jade-chase', '', 26, '+27824068474', 1, 5.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'fetish', 'English, Spanish', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:59', '2026-06-03 09:20:58'),
(87, '07935dbe-3cc7-4ce4-b349-99acd30430e7', 88, 'Daisy Hayes', 'daisy-hayes', '', 27, '+27826677873', 1, 9.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'couples', 'English, Portuguese', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:59', '2026-06-03 09:21:05'),
(88, '67bd6509-bb8e-43b0-8f42-38f75a44c52e', 89, 'Maya Stone', 'maya-stone', '', 29, '+27827300236', 1, 6.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'chat', 'English, Italian', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:59', '2026-06-03 09:21:12'),
(89, '17f791ef-46ac-45cf-86c1-b3b4dcd2f956', 90, 'Grace Knight', 'grace-knight', '', 35, '+27822896821', 1, 4.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, NULL, NULL, NULL, NULL, 'chat', 'English, German', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:05:59', '2026-06-03 09:22:04'),
(90, 'd363b176-52fb-4cff-a4f9-99b23cc85099', 91, 'Serena Stone', 'serena-stone', '', 26, '+27828476978', 1, 5.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, 'uploads/performers/90/b41363ab6d1e1f175a8c517ee699ebf7.jpg', NULL, NULL, NULL, 'fetish', 'English, German', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:06:00', '2026-06-03 09:20:13'),
(91, '99096ec4-495e-4cc5-b564-5b5ca7396563', 92, 'Paris Delaney', 'paris-delaney', '', 19, '+27821344921', 1, 7.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, 'uploads/performers/91/c9bdf0e1273ddd06fdac7ee001a0e4c6.jpg', NULL, NULL, NULL, 'mature', 'English, Spanish', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:06:00', '2026-06-03 09:20:21'),
(92, '4adb46f1-5664-4cb6-b6b0-f0891199ecf5', 93, 'Jasmine Star', 'jasmine-star', '', 27, '+27828694884', 1, 9.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, 'uploads/performers/92/b1070f3eee1aa486f55baa378d151d63.jpg', NULL, NULL, NULL, 'couples', 'English, Italian', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:06:00', '2026-06-03 09:20:28'),
(93, '97f48997-ceb8-42ed-b6d7-69e16971f0f6', 94, 'Carmen Thorne', 'carmen-thorne', '', 22, '+27824452163', 1, 9.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, 'uploads/performers/93/1fbb67ba986c80ad393774dfbaa863ca.jpg', NULL, NULL, NULL, 'fantasy', 'English, German', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:06:00', '2026-06-03 09:20:36'),
(94, '89b0a05b-7dac-4518-bfb8-d4c94fcff9aa', 95, 'Luna Hayes', 'luna-hayes', '', 29, '+27821894778', 1, 7.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, 'uploads/performers/94/d9c3d60d57576c4073773827252babd2.jpg', NULL, NULL, NULL, 'fetish', 'English', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:06:00', '2026-06-03 09:20:43'),
(95, 'cd2f5b92-d524-40ab-847c-2bb28e5aa1e7', 96, 'Jasmine Delaney', 'jasmine-delaney', '', 18, '+27823140834', 1, 2.50, 0, 15.00, 10, 5.00, 'active', 0, NULL, 'uploads/performers/95/9887e22b17b866a552244bd0ba8f0ccb.jpg', 'uploads/performers/95/67786122c3abff48e1ecac9a18f7e165.jpg', NULL, NULL, 'couples', 'English, Spanish', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:06:01', '2026-06-03 09:20:00'),
(96, 'a4dccf2e-b97d-4595-a591-5a12bf3995a6', 97, 'Roxy Monroe', 'roxy-monroe', '', 29, '+27822449629', 1, 3.00, 0, 15.00, 10, 5.00, 'active', 0, NULL, 'uploads/performers/96/71a0246f7a0f30d7acfc4282a0e4210c.jpg', 'uploads/performers/96/b43df4b06caef01ee8848aa57a7980f9.jpg', NULL, NULL, 'fantasy', 'English, German', 0.00, 0, 0, 0, 0.0000, 0.0000, 40.00, NULL, NULL, '2026-05-28 14:06:01', '2026-06-03 09:20:07');

-- --------------------------------------------------------

--
-- Table structure for table `performer_payouts`
--

CREATE TABLE `performer_payouts` (
  `id` int(10) UNSIGNED NOT NULL,
  `performer_id` int(10) UNSIGNED NOT NULL,
  `amount` decimal(10,4) NOT NULL,
  `method` enum('bank_transfer','eft','paypal','other') COLLATE utf8mb4_unicode_ci DEFAULT 'eft',
  `reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','processing','paid','failed') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `processed_by` int(10) UNSIGNED DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `performer_photos`
--

CREATE TABLE `performer_photos` (
  `id` int(10) UNSIGNED NOT NULL,
  `performer_id` int(10) UNSIGNED NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `thumbnail_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` tinyint(3) UNSIGNED DEFAULT '0',
  `is_primary` tinyint(1) DEFAULT '0',
  `approved` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rate_limits`
--

CREATE TABLE `rate_limits` (
  `id` int(10) UNSIGNED NOT NULL,
  `identifier` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint(5) UNSIGNED DEFAULT '1',
  `window_start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rate_limits`
--

INSERT INTO `rate_limits` (`id`, `identifier`, `action`, `attempts`, `window_start`) VALUES
(29, '105.1.170.184', 'admin_login', 1, '2026-06-10 10:08:45');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(10) UNSIGNED NOT NULL,
  `call_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `performer_id` int(10) UNSIGNED NOT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `is_approved` tinyint(1) DEFAULT '0',
  `approved_by` int(10) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('string','integer','boolean','json') COLLATE utf8mb4_unicode_ci DEFAULT 'string',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`key`, `value`, `type`, `description`, `updated_at`) VALUES
('admin_proxy_forced_online', '', 'string', '', '2026-06-02 12:11:15'),
('admin_proxy_mode', '0', 'boolean', 'Admin answers all incoming performer calls', '2026-06-10 10:14:05'),
('age_gate_enabled', '1', 'boolean', 'Show age verification gate', '2026-05-27 13:19:56'),
('call_recording_enabled', '0', 'boolean', 'Record calls (requires legal disclosure)', '2026-05-27 13:19:56'),
('default_currency', 'ZAR', 'string', 'Default display currency', '2026-05-27 13:19:56'),
('eur_to_zar', '20.50', 'string', '1 EUR in ZAR (update daily via cron)', '2026-05-27 13:19:56'),
('gbp_to_zar', '24.00', 'string', '1 GBP in ZAR (update daily via cron)', '2026-05-27 13:19:56'),
('low_credit_warning', '2', 'integer', 'Credits remaining to trigger IVR warning', '2026-05-27 13:19:56'),
('maintenance_mode', '0', 'boolean', 'Site maintenance mode', '2026-06-10 10:16:00'),
('max_call_duration_mins', '120', 'integer', 'Hard cap on call duration in minutes', '2026-05-27 13:19:56'),
('min_credits_to_call', '5', 'integer', 'Minimum credits needed to initiate a call', '2026-05-27 13:19:56'),
('payfast_sandbox', '1', 'boolean', 'Use PayFast sandbox (set 0 for production)', '2026-05-27 13:19:56'),
('performer_min_age', '18', 'integer', 'Minimum performer age', '2026-05-27 13:19:56'),
('site_name', 'TheGentlemensPlace', 'string', 'Site name', '2026-05-27 13:19:56'),
('support_email', 'support@thegentlemensplace.eu', 'string', 'Support email', '2026-05-27 13:19:56'),
('usd_to_zar', '18.50', 'string', '1 USD in ZAR (update daily via cron)', '2026-05-27 13:19:56'),
('user_min_age', '18', 'integer', 'Minimum user age', '2026-05-27 13:19:56');

-- --------------------------------------------------------

--
-- Table structure for table `streams`
--

CREATE TABLE `streams` (
  `id` int(10) UNSIGNED NOT NULL,
  `uuid` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `performer_id` int(10) UNSIGNED NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'live',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `channel_name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` text COLLATE utf8mb4_unicode_ci,
  `viewer_count` int(11) DEFAULT '0',
  `started_at` datetime NOT NULL,
  `ended_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `package_id` int(10) UNSIGNED NOT NULL,
  `amount_zar` decimal(8,2) NOT NULL,
  `credits_purchased` decimal(10,4) NOT NULL,
  `bonus_credits` decimal(10,4) DEFAULT '0.0000',
  `payfast_pf_payment_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payfast_payment_status` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `merchant_reference` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','completed','failed','cancelled','refunded') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `itn_received_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `uuid`, `user_id`, `package_id`, `amount_zar`, `credits_purchased`, `bonus_credits`, `payfast_pf_payment_id`, `payfast_payment_status`, `merchant_reference`, `item_name`, `status`, `ip_address`, `user_agent`, `itn_received_at`, `created_at`, `updated_at`) VALUES
(1, '97738e1b-66a4-413e-999b-f36d536bc430', 1, 2, 369.00, 40.0000, 5.0000, NULL, 'COMPLETE', 'TGP-97738E1B', 'Gentleman Credit Package', 'completed', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-27 13:23:59', '2026-05-27 13:23:49', '2026-05-27 13:23:59'),
(2, '07eb2e22-c83f-44f0-a9d8-029e8f1df1b5', 1, 3, 738.00, 90.0000, 15.0000, NULL, 'COMPLETE', 'TGP-07EB2E22', 'Elite Credit Package', 'completed', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-29 10:12:48', '2026-05-29 10:12:37', '2026-05-29 10:12:48'),
(3, '21af17c9-8f3f-4768-ab98-c407a241a10f', 99, 1, 164.00, 15.0000, 0.0000, NULL, 'COMPLETE', 'TGP-21AF17C9', 'Starter Credit Package', 'completed', '168.210.241.18', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:151.0) Gecko/20100101 Firefox/151.0', '2026-06-02 08:45:27', '2026-06-02 08:45:23', '2026-06-02 08:45:27'),
(4, '2bdc183e-88e1-409f-8648-0d8a1e06981f', 99, 3, 738.00, 90.0000, 15.0000, NULL, 'COMPLETE', 'TGP-2BDC183E', 'Elite Credit Package', 'completed', '168.210.241.18', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-06-02 09:49:37', '2026-06-02 09:49:33', '2026-06-02 09:49:37'),
(5, 'aa465c7e-a187-4df1-9bcb-306bb6b7d30f', 99, 1, 164.00, 15.0000, 0.0000, NULL, 'COMPLETE', 'TGP-AA465C7E', 'Starter Credit Package', 'completed', '168.210.241.18', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-06-02 10:44:52', '2026-06-02 10:44:48', '2026-06-02 10:44:52'),
(6, 'e5d3171e-d1da-418f-8880-46aabebed26f', 99, 1, 164.00, 15.0000, 0.0000, NULL, 'COMPLETE', 'TGP-E5D3171E', 'Starter Credit Package', 'completed', '168.210.241.18', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-06-02 12:08:16', '2026-06-02 12:08:10', '2026-06-02 12:08:16'),
(7, '104d5501-7fcf-432d-9e87-db1e06f81295', 99, 3, 738.00, 90.0000, 15.0000, NULL, 'COMPLETE', 'TGP-104D5501', 'Elite Credit Package', 'completed', '168.210.241.18', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-06-02 12:51:08', '2026-06-02 12:51:04', '2026-06-02 12:51:08'),
(8, '2c80c654-c65c-4f23-b770-17cc20ecfff4', 99, 2, 369.00, 40.0000, 5.0000, NULL, 'COMPLETE', 'TGP-2C80C654', 'Gentleman Credit Package', 'completed', '168.210.241.18', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-06-02 13:24:01', '2026-06-02 13:23:53', '2026-06-02 13:24:01'),
(9, 'b10da8e0-a009-42f0-9c60-c6e2ada087f3', 99, 3, 738.00, 90.0000, 15.0000, NULL, 'COMPLETE', 'TGP-B10DA8E0', 'Elite Credit Package', 'completed', '168.210.241.18', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-06-02 13:59:51', '2026-06-02 13:59:47', '2026-06-02 13:59:51'),
(10, 'a56d7e6a-aa5b-4605-9606-173dafcc89dc', 1, 1, 164.00, 15.0000, 0.0000, NULL, 'pending', 'TGP-A56D7E6A', 'Starter Credit Package', 'completed', '168.210.241.18', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', NULL, '2026-06-03 13:25:26', '2026-06-03 13:50:06'),
(11, 'fd82f938-cc26-48c8-9c13-5155f77d21ea', 1, 1, 164.00, 15.0000, 0.0000, NULL, 'pending', 'TGP-FD82F938', 'Starter Credit Package', 'completed', '168.210.241.18', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', NULL, '2026-06-03 13:27:07', '2026-06-03 13:39:45'),
(12, 'b548fa5a-0736-4956-83cf-54b83beda5fb', 1, 1, 20.50, 15.0000, 0.0000, NULL, 'pending', 'TGP-B548FA5A', 'Starter Credit Package', 'completed', '168.210.241.18', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', NULL, '2026-06-03 14:37:13', '2026-06-03 14:39:44'),
(13, 'f827d7ed-d1b9-4e1a-8e57-4922f1ca1586', 1, 1, 5.33, 15.0000, 0.0000, NULL, 'pending', 'TGP-F827D7ED', 'Starter Credit Package', 'pending', '168.210.241.18', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', NULL, '2026-06-04 06:04:33', '2026-06-04 06:04:33'),
(14, 'd3521937-0dd2-4bc6-ae80-a5cea458d342', 1, 1, 5.33, 15.0000, 0.0000, NULL, 'pending', 'TGP-D3521937', 'Starter Credit Package', 'pending', '168.210.241.18', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', NULL, '2026-06-04 06:05:21', '2026-06-04 06:05:21'),
(15, '2d43cbad-0a00-422a-b355-a3a3c43b0506', 1, 1, 5.33, 15.0000, 0.0000, NULL, 'pending', 'TGP-2D43CBAD', 'Starter Credit Package', 'pending', '168.210.241.18', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', NULL, '2026-06-04 06:07:16', '2026-06-04 06:07:16'),
(16, 'a210356d-f221-4513-88c6-4fc9c0116fe6', 1, 1, 5.33, 15.0000, 0.0000, NULL, 'pending', 'TGP-A210356D', 'Starter Credit Package', 'pending', '168.210.241.18', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', NULL, '2026-06-04 06:08:17', '2026-06-04 06:08:17'),
(17, 'a5e6a4bd-2838-466a-9236-6060c41efc8d', 1, 1, 5.33, 15.0000, 0.0000, NULL, 'pending', 'TGP-A5E6A4BD', 'Starter Credit Package', 'pending', '168.210.241.18', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', NULL, '2026-06-04 06:09:02', '2026-06-04 06:09:02'),
(18, '49475759-4014-4d59-82af-0d043652d6ac', 1, 1, 5.33, 15.0000, 0.0000, '305907227', 'COMPLETE', 'TGP-49475759', 'Starter Credit Package', 'completed', '168.210.241.18', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-04 07:00:51', '2026-06-04 06:15:50', '2026-06-04 07:00:51'),
(19, '9cdb9753-1b92-43f6-a63a-99f29df4b88b', 1, 1, 5.33, 15.0000, 0.0000, '305917977', 'COMPLETE', 'TGP-9CDB9753', 'Starter Credit Package', 'completed', '168.210.241.18', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-04 08:16:50', '2026-06-04 06:32:28', '2026-06-04 08:16:50'),
(20, 'ace3ddf3-0c6e-4abb-9da0-f1cb29548e73', 1, 1, 5.33, 15.0000, 0.0000, '305920789', 'COMPLETE', 'TGP-ACE3DDF3', 'Starter Credit Package', 'completed', '168.210.241.18', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-04 06:59:17', '2026-06-04 06:43:17', '2026-06-04 06:59:17'),
(21, 'aa6ff379-ad12-4b05-98c8-2fdf800f781b', 1, 1, 5.33, 15.0000, 0.0000, '305932569', 'COMPLETE', 'TGP-AA6FF379', 'Starter Credit Package', 'completed', '168.210.241.18', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-04 06:51:33', '2026-06-04 06:49:25', '2026-06-04 06:51:33');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_birth` date NOT NULL,
  `age_verified` tinyint(1) NOT NULL DEFAULT '0',
  `age_verified_at` timestamp NULL DEFAULT NULL,
  `age_verify_method` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `credit_balance` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `status` enum('active','suspended','banned','pending') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `email_verified` tinyint(1) NOT NULL DEFAULT '0',
  `email_verify_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verify_expires` timestamp NULL DEFAULT NULL,
  `reset_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_expires` timestamp NULL DEFAULT NULL,
  `two_factor_enabled` tinyint(1) DEFAULT '0',
  `two_factor_secret` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `uuid`, `username`, `email`, `password_hash`, `phone`, `date_of_birth`, `age_verified`, `age_verified_at`, `age_verify_method`, `credit_balance`, `status`, `email_verified`, `email_verify_token`, `email_verify_expires`, `reset_token`, `reset_expires`, `two_factor_enabled`, `two_factor_secret`, `last_login_at`, `last_login_ip`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'f0a49b3a-0ce6-4204-abfa-627257547713', 'Cyrus', 'cyruskingz@outlook.com', '$2y$12$DuV6mwuhCGQtq25PKJtX9OI0iyTzFcYFy20D4AZelfVZO4GYPJMJi', NULL, '1997-02-04', 1, '2026-05-27 13:22:54', 'dob_declaration', 44.0000, 'active', 1, '13c3ce182b2ba241057485d8136fecf92487a048ce792a2c4aa4e56a8e5d72a7', '2026-05-28 13:22:54', NULL, NULL, 0, NULL, '2026-06-10 10:10:12', '105.1.170.184', '2026-05-27 13:22:54', '2026-06-10 10:12:44', NULL),
(2, '1ec72f27-76ee-4580-bd26-36a7f55f35ca', 'Roxy', 'roxy@gmail.com', '$2y$12$fKzEIky13hEWrmb3ijVs8uMrWCWO19.tnqHK1ypvjWenu/GAo2tsu', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, '2026-05-28 13:26:17', '::1', '2026-05-28 13:06:54', '2026-05-28 13:26:17', NULL),
(4, 'e80958cb-ad8b-4e33-921e-86fce6c591d0', 'Zonika', 'zonika@gmail.com', '$2y$12$UQuUWP/OI2FOnoS3YWxWmOgDZreXm14xjruJSs1p2qgSv.NZqeno.', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, '2026-06-03 09:11:08', '168.210.241.18', '2026-05-28 13:23:21', '2026-06-03 09:11:08', NULL),
(5, 'b73ae6d2-e3f7-4285-862c-6ab5fe6bdc71', 'skyhayes1', 'skyhayes1@example.com', '$2y$12$RvwqtQ19pLK7IwvmgzmeGuU/q.oAF7za8szjnm4U/9CEXbKcVhOrS', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, '2026-06-02 08:46:32', '168.210.241.18', '2026-05-28 14:05:41', '2026-06-02 08:46:32', NULL),
(6, '623bb6a7-7791-425a-af5b-44c8ac30beb6', 'gracerose2', 'gracerose2@example.com', '$2y$12$dlEMd8hSRQM9QimN35nezensNzyZccGkwRC7rHTKkrR3KVBJZWi6y', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, '2026-06-02 08:35:59', '168.210.241.18', '2026-05-28 14:05:42', '2026-06-02 08:35:59', NULL),
(7, '27ae58af-9d87-4c56-80bd-048ce910bb10', 'sophiavance3', 'sophiavance3@example.com', '$2y$12$oXWJO4Rr0rUS7o9YylAExuDJquCLkSJgLh38S9kIh4BPiQs3C63wq', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:42', '2026-05-28 14:05:42', NULL),
(8, '5036f9ec-06fb-49d3-89bc-70126363c508', 'honeyrose4', 'honeyrose4@example.com', '$2y$12$.J8k2j.vyT3U0Qtrf0tkA.981X.IVXuCCz4kl/ZYup4zj4H0gVkbm', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:42', '2026-05-28 14:05:42', NULL),
(9, '019c6980-6481-49fa-8081-475a39e32f63', 'chloecole5', 'chloecole5@example.com', '$2y$12$lNMnAaT212PLXjM4/XCAR.vrCz4jOOmRCQrTOqSbkjKcwEetxYNl2', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:42', '2026-05-28 14:05:42', NULL),
(10, 'daebcda0-7528-48fe-b76b-ec9e3cfe4ca9', 'ivypierce6', 'ivypierce6@example.com', '$2y$12$2lep9B4mjy.NLsVBFWgLIe/hPBCKYEvmWqw8AA0eoW5aYPEEeF1nm', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:43', '2026-05-28 14:05:43', NULL),
(11, '74bdf0a2-1152-4816-b065-c945a0885be9', 'carmenvelvet7', 'carmenvelvet7@example.com', '$2y$12$ILe8HjOJHGolcrbBB7tUSum3cNiS4gte0Rtk4ubXnnFJ2i3do.QbC', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:43', '2026-05-28 14:05:43', NULL),
(12, 'bf76a293-6ff5-45a5-bc2e-a5fd426a6299', 'roxyhayes8', 'roxyhayes8@example.com', '$2y$12$hXTOTGyqX08MZFmb3oMd.e2o0HBlMXIrTRoTOTKxclXTBh7/TJJ4q', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:43', '2026-05-28 14:05:43', NULL),
(13, '200bd2c8-01cd-45b1-a623-b5c3219149ab', 'ivymonroe9', 'ivymonroe9@example.com', '$2y$12$e2UEMECqkXZJ7XhpiBGqA.pH0ylL3JcV0hoJGujBIeB0iSVtmk3Ra', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:43', '2026-05-28 14:05:43', NULL),
(14, '570bffa6-c44b-4d5e-8719-1601c52cba14', 'brookefox10', 'brookefox10@example.com', '$2y$12$MlLB.QOb8iErlDiiexzyxObkvrKChz2YcHpu3aVj2fH.rPxEGWrDq', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:43', '2026-05-28 14:05:43', NULL),
(15, 'e2797102-a244-4b09-ad97-6ce1ddae3a12', 'aurorastar11', 'aurorastar11@example.com', '$2y$12$MfUnkBaisgz/9mkY.T1sievniK2S8uYrluoay3Jq.z9cON5QRLw9q', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:44', '2026-05-28 14:05:44', NULL),
(16, '91fc9d55-2e70-456e-b45d-a5fc3eafc9ca', 'lexiwild12', 'lexiwild12@example.com', '$2y$12$AjtAPajxfReVOqGQFNo41OnS9npmgNIhQCnLvgAvwJy1OZhQhEwOq', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:44', '2026-05-28 14:05:44', NULL),
(17, '7cbf704b-af04-4f40-bc7a-397f092aefe9', 'carmenstone13', 'carmenstone13@example.com', '$2y$12$5dW//SK1pYZj7W3GaKxwZebUHmrhVrqJJAIaPI5ibn.vJzaVkPRa.', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:44', '2026-05-28 14:05:44', NULL),
(18, 'f3116eab-9727-4b91-ab33-ac780af402f3', 'rubymonroe14', 'rubymonroe14@example.com', '$2y$12$cA/u1JwwIUR0WKybqWtbHOip.1xMOu1EwO/pqi7ZVVBvotGN2TXHC', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:44', '2026-05-28 14:05:44', NULL),
(19, '3ac3abad-051f-4cd7-a49b-2ad9fdbb8cfd', 'scarlettpierce15', 'scarlettpierce15@example.com', '$2y$12$39hCspD6ZmwrukgJXpypbO5YNvTc2HZSUoHYVFDckDKC1JPjcWhQG', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:44', '2026-05-28 14:05:44', NULL),
(20, '1bd782f6-87e7-4968-8ed8-db7e69e13893', 'lexihayes16', 'lexihayes16@example.com', '$2y$12$G9fjVFVGWCGFhsdMDM5iAeu6cbfjxV0nZx.N3KYkPfNC/ad.ZMJsu', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:45', '2026-05-28 14:05:45', NULL),
(21, 'f2c4784d-b22d-47bc-8608-9928a8f97e08', 'autumnhayes17', 'autumnhayes17@example.com', '$2y$12$q/3bVs2nQBNLHZtaC7cu/uzAZXT0MhO1U05g99ttNAVJtCcMGUuEK', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:45', '2026-05-28 14:05:45', NULL),
(22, 'fbbd9fca-fb69-4afa-9f69-fdccb422ec23', 'summerrose18', 'summerrose18@example.com', '$2y$12$YXqVZUJdixwrF7liksNL1.Qv7g3gfD90y94fx3wjYb9TaPegp9BNG', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:45', '2026-05-28 14:05:45', NULL),
(23, 'f441b5e4-6489-4392-a22d-033111a388e0', 'aurorabrooks19', 'aurorabrooks19@example.com', '$2y$12$rRusz46tYA/hsXnls42DxOGKEBaJsigb4WLWAtvWT5PxHSi/qme32', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:45', '2026-05-28 14:05:45', NULL),
(24, '7e701957-0fce-40ae-91f0-de8434c8998f', 'skyfox20', 'skyfox20@example.com', '$2y$12$JeeTbpGZRET.9KEJypkRFu0jEc5ALanLPaASrckmwCQ4992sQjSkG', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:46', '2026-05-28 14:05:46', NULL),
(25, '1f03b7cc-bd40-4ce8-80c9-38368d4d1400', 'penelopeknight21', 'penelopeknight21@example.com', '$2y$12$LdG9TdFPX33/mmXqHQx34.GCysHayfULkWQZTErps6BQvg4/Zs/j.', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:46', '2026-05-28 14:05:46', NULL),
(26, 'b132f193-75f1-4ba5-a390-a7362d0ea76e', 'serenasummers22', 'serenasummers22@example.com', '$2y$12$xbndvDz.rtFpWSI.ah6nrOxfLUH8ltX7N1p1aToiiMnAH2Ec6ih22', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:46', '2026-05-28 14:05:46', NULL),
(27, '003a8118-5709-462a-b467-ff44f8f730c9', 'summerwinters23', 'summerwinters23@example.com', '$2y$12$.mR01H9QpstSeIljnn4jKujN5qXB6H/d2/m.dSTIOrTwxpQEBPvgi', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:46', '2026-05-28 14:05:46', NULL),
(28, '4b2598fa-5ef7-4fc3-92a0-b77dfc978e39', 'ninapierce24', 'ninapierce24@example.com', '$2y$12$K/YrfmlYgwLRluyCMsFf3OnWeGbXRL//X9HOuHQ1Tqre3zJbVeKXG', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:46', '2026-05-28 14:05:46', NULL),
(29, '29139da8-41d2-4c5c-ba0f-ac813c1c7a3c', 'ninavance25', 'ninavance25@example.com', '$2y$12$gHpve8Yc6RNWXP1bswGcPeG70q7kRDMDKjrof9RNndVdRHKLdUbSq', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:47', '2026-05-28 14:05:47', NULL),
(30, '5ea8f4dc-bc40-4270-982f-c2c2bee178b0', 'ambercole26', 'ambercole26@example.com', '$2y$12$irPe8r1HKgiRDzvHwj6r5uUnB0y2OqqJEGWVgbwlf/LkJ9dyQ/Sqa', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:47', '2026-05-28 14:05:47', NULL),
(31, '961670dd-1403-4b05-893a-0eec2070ee22', 'nataliamonroe27', 'nataliamonroe27@example.com', '$2y$12$EH1Ej3NKO/G5V1CLCMjGGuI6ChgsYbYSDZzAC2eBJKudDmpImfotq', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:47', '2026-05-28 14:05:47', NULL),
(32, '6aa86d3e-f035-448e-b827-279d40e88417', 'penelopepierce28', 'penelopepierce28@example.com', '$2y$12$FBc20Mq6upMT.MS7PgG.4uAar/Rq7FmgXOUlVIeH8giGJ5GDSjlT6', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:47', '2026-05-28 14:05:47', NULL),
(33, 'f174f9d7-c86f-498c-ad7f-f9a300318c5b', 'summersterling29', 'summersterling29@example.com', '$2y$12$ZPNLPqVh9iVjyEGM5EWIiOSYXykUoa6UqpUCotZkyTWeX3u23n.vy', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:47', '2026-05-28 14:05:47', NULL),
(34, '4b7468ee-3716-4038-b34a-e9aa5342e321', 'penelopesummers30', 'penelopesummers30@example.com', '$2y$12$yXw.zw341RxMM8b4M9Lfj.YxzHWYu4qQmcA9aAwfAcE.2mbBlM9Uu', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:48', '2026-05-28 14:05:48', NULL),
(35, '5c4d114e-e802-4a98-8555-5be464431038', 'skywinters31', 'skywinters31@example.com', '$2y$12$FWQgb8bkObLnqE2y7yqBF.hJvJhMOOTZWbC3Olha6BAOaRpRnKaY2', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:48', '2026-05-28 14:05:48', NULL),
(36, '2e649bc5-4045-40b6-89ec-238a6271957f', 'nataliastar32', 'nataliastar32@example.com', '$2y$12$Nqgt2HOD./NghWalFq50bu/XBv9aJCss/bL7.akVAbDl8pwe6QrhC', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:48', '2026-05-28 14:05:48', NULL),
(37, 'cdc669e6-901e-4399-ad0b-ed2cb0e3cc76', 'lunapierce33', 'lunapierce33@example.com', '$2y$12$tpFj9Yw3NRmNRlgC0VBTwOGxMZ4BHL/aYF6JhwIqcVoXvJ20oNn8W', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:48', '2026-05-28 14:05:48', NULL),
(38, '5ee8ca4d-2a69-482a-abba-e3dc39bb6f45', 'miacole34', 'miacole34@example.com', '$2y$12$ZcV/8f4TTjKRTPOigvx/J.vZ/paI/fm1o9JvOuPnk9u9P8AvSqMU2', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:48', '2026-05-28 14:05:48', NULL),
(39, 'f60517bb-ab51-4279-83a1-6f93afe1dcdc', 'serenawild35', 'serenawild35@example.com', '$2y$12$AIYJ1wrWbKIs0wDkd8DxLe6NC6bqzhyA7oulfIsr1ln5hC2TScMDi', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:49', '2026-05-28 14:05:49', NULL),
(40, 'c603b6e3-4ae7-46e6-b9f5-e8b6408772f5', 'ninadelaney36', 'ninadelaney36@example.com', '$2y$12$CfhADJ3MK5zE5UDtoFJLau88trVj7XxSQPljHrRsBcYa5c.J6kSge', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:49', '2026-05-28 14:05:49', NULL),
(41, 'edadba75-48d0-4696-b993-a4b17dacb8af', 'penelopeblair37', 'penelopeblair37@example.com', '$2y$12$gO22JtPWBqguAHFQzi9vFuA6/imtiliAAgCwMB2Z8lE6ZJ.hdYINK', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:49', '2026-05-28 14:05:49', NULL),
(42, 'c5431c49-7a40-4d97-b09c-f3153aa01ea1', 'bellavance38', 'bellavance38@example.com', '$2y$12$y.uNdJHG0Nk7RyvuDZX0BOdaPb6k6F4vx0j.DhN0YJCt6z4cReiIu', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:49', '2026-05-28 14:05:49', NULL),
(43, '6bfc29fb-4349-4eee-abd2-502cbfa73777', 'jadeknight39', 'jadeknight39@example.com', '$2y$12$z./l6XJocKdotWYRTgyU6uV7UUOOdb3/BJDzJj9cR5aGg5c1ckhqy', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:49', '2026-05-28 14:05:49', NULL),
(44, 'a0795dd0-ef53-4871-ad04-31315b36966c', 'carmenvance40', 'carmenvance40@example.com', '$2y$12$sZZgW2klsQPzyCucWKPsku1Nj.zB4nmpPkdab/.ygnqArc.h1wY/q', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:50', '2026-05-28 14:05:50', NULL),
(45, 'c16d5c8d-9144-40a7-ad67-acc0905927a3', 'ivydelaney41', 'ivydelaney41@example.com', '$2y$12$JlnwvXQR6BqT2dBncSkKFu5/71iVBYlJoSxtdsvLPByGqX9JA7Dt2', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:50', '2026-05-28 14:05:50', NULL),
(46, 'e137b6c6-be58-4988-8bcf-3dcaa233341c', 'ivybrooks42', 'ivybrooks42@example.com', '$2y$12$ri/yOFWpaDXmsGEzdmqXrec2QrimIJCUykMWFNsHrairhE8ziWQiu', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:50', '2026-05-28 14:05:50', NULL),
(47, '3563ea52-4c17-4517-a1e4-e8c189ec9915', 'parissummers43', 'parissummers43@example.com', '$2y$12$nbY9RvLTTEEvjkdFiwJBIORDsx6p5aT7dQQGo2qUJ5Thz7zmDlwQS', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:50', '2026-05-28 14:05:50', NULL),
(48, '77d84990-357f-4582-903f-ee07172cdad1', 'scarlettbrooks44', 'scarlettbrooks44@example.com', '$2y$12$qUfdjmBulriW8RIKcEbw/uhr6Faa2DqVRJjsMZ3g0/REPhhj1I.o2', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:51', '2026-05-28 14:05:51', NULL),
(49, 'b8eebcf8-c529-4935-989d-bc58de2bfd5a', 'lilyrose45', 'lilyrose45@example.com', '$2y$12$FEdXc4Oh6SAEqv/Fs7mfru4vX1SgUIoZ3QFjJ0pDwhsts54penZOq', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:51', '2026-05-28 14:05:51', NULL),
(50, 'ae7018fd-071e-4555-9546-b1a41a2907da', 'lunavelvet46', 'lunavelvet46@example.com', '$2y$12$B3QFRKe25ReVyVV9sPO82.4x2Tk69/6KEVoiTi8XurrCj5WIO5RzK', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:51', '2026-05-28 14:05:51', NULL),
(51, 'c6d61e5b-fcb8-4935-b5fc-8abff767bb86', 'roxyfox47', 'roxyfox47@example.com', '$2y$12$.XhivZL/g6lDM07FbGJ6n.bsddF/oekDCLo1RJutUurbsdXtVCoJy', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:51', '2026-05-28 14:05:51', NULL),
(52, '6af01f80-9022-4cc3-a8a1-a37bfb461bb9', 'serenapierce48', 'serenapierce48@example.com', '$2y$12$pJ5mjyeNVW1WHAR1jgYpxudyrfHDqT/gZ/Znp.N.884PCMBoyxona', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:51', '2026-05-28 14:05:51', NULL),
(53, '52ab569f-86ce-4b01-85ac-49310581b277', 'penelopedelaney49', 'penelopedelaney49@example.com', '$2y$12$GMMacGloVqQpAbq04zBCPOmkBqsi1Q8RcALNk0NmbrGoO/gE7WGeS', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:52', '2026-05-28 14:05:52', NULL),
(54, 'ed9e2186-7d30-49ad-a9f6-0ed4f2884250', 'angelsummers50', 'angelsummers50@example.com', '$2y$12$dsYNBRWQUrdySHO1LiG.3OV5RLGbZdC92Cfi8UUrlyG9LerSa6UFa', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:52', '2026-05-28 14:05:52', NULL),
(55, '1d0ffe7b-e87f-46a2-aec9-3ae63f6a5352', 'summerwinters51', 'summerwinters51@example.com', '$2y$12$/ZJs/GxHN6BGEUw.AuV1TeOD0RHuDVap3Nvgd0xqU5m1bycDo1SAi', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:52', '2026-05-28 14:05:52', NULL),
(56, 'c3be4454-b0c9-47ce-a431-b9f4cf6a0339', 'lunafox52', 'lunafox52@example.com', '$2y$12$YXYZY5LLdHWF698XdqY5j.o4ySMtHws39lN/tyrbGR/tHy1yk.W8e', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:52', '2026-05-28 14:05:52', NULL),
(57, '41690d56-acab-4d28-979f-357bdc4bac65', 'stellasummers53', 'stellasummers53@example.com', '$2y$12$I1Sc0L0gPcj3oGRUCyCtjuptFY6miew4dCETePxWs7AjVO.HfAlZK', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:53', '2026-05-28 14:05:53', NULL),
(58, 'b454fd55-3941-49c0-9203-cb964c95fe97', 'sophiapierce54', 'sophiapierce54@example.com', '$2y$12$mPh8bej.a2D34.VVFYYyq.dfpFjzyrz/KqMw4/.DczRNPo4GcpPVy', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:53', '2026-05-28 14:05:53', NULL),
(59, '7054ae01-dbe3-4aa7-a983-7b82acd97f40', 'elenastar55', 'elenastar55@example.com', '$2y$12$.Spgxa035Eorwa./84UA/eYUUuwb7qL1kbbXFWbMkW3qa.ueTxslO', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:53', '2026-05-28 14:05:53', NULL),
(60, '3929dfdf-6773-4473-8e21-f35aa0620b0e', 'skywinters56', 'skywinters56@example.com', '$2y$12$BsKPe9Z8t5y.BOnE7OFiLO/5ADCJ7ueA.EXumTIObt9AzZUDViRam', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:53', '2026-05-28 14:05:53', NULL),
(61, '8f1cd388-47fe-42d5-aa6b-6d354a9a9cff', 'peneloperose57', 'peneloperose57@example.com', '$2y$12$2D6S6ZCmzqvUAnPV.34iG.iWjngnn2fzqlqzwGy9MWQdd3C/o909W', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:53', '2026-05-28 14:05:53', NULL),
(62, 'b502f0fa-9e7f-4940-87b9-200813ef71a1', 'chloesummers58', 'chloesummers58@example.com', '$2y$12$CooaiyqXV1lxZ0QMElXlYeN0k2gqiOIOgBRFibtuPNfPeH2sx1Gfm', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:54', '2026-05-28 14:05:54', NULL),
(63, '84b1f957-0d56-459a-840d-3d4eccd1e314', 'serenacole59', 'serenacole59@example.com', '$2y$12$wC0jMbo3HQD7eyggWUr4x.UR0CiEn3h8KwlnGeJ.oOGufnL7B8Dr2', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:54', '2026-05-28 14:05:54', NULL),
(64, 'd25ec8f9-11c5-4166-801f-1d3115f40d0f', 'carmenwinters60', 'carmenwinters60@example.com', '$2y$12$AruV8C1Kwq3fkwajQCuBLeboYo2XlvuhaqZe5ROk.fWoWskuu4NAC', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:54', '2026-05-28 14:05:54', NULL),
(65, '1274da60-0c52-4119-88e0-cd9f9863e559', 'lunadelaney61', 'lunadelaney61@example.com', '$2y$12$uOmFhwI6khsAURhAFCzFwuFqPvQIjMcr7xnWaCOsC6GgN/mvHxPe6', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:54', '2026-05-28 14:05:54', NULL),
(66, '6c538dbf-a90d-4686-8a3f-e2071bba966d', 'elenathorne62', 'elenathorne62@example.com', '$2y$12$iW7PkRKYhdFeNGCckXwSZ.w69kS2IDP8oEwb9gEuBSIp6SqLBe4Ha', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:54', '2026-05-28 14:05:54', NULL),
(67, 'c1f254b6-b6cb-4948-b980-0babdf7403d3', 'angelsterling63', 'angelsterling63@example.com', '$2y$12$K9gpMTJoIVHrVWViuCHsT.YgMc.LqdtUvvXL1kUCFFsgBqNYQ80Oi', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:55', '2026-05-28 14:05:55', NULL),
(68, 'c45ad8f3-f56b-4825-9296-7d7b7489f622', 'violetvelvet64', 'violetvelvet64@example.com', '$2y$12$qWYA104PHGkMjwAVKE7nU.pkSdbpiUoYC/o9k80byLMdtJ1b7QgYG', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:55', '2026-05-28 14:05:55', NULL),
(69, '20d8566b-6495-4032-aaad-71a289798b9c', 'ninafox65', 'ninafox65@example.com', '$2y$12$bDvqyal16TAN85meLe/HH..Ogkk44XaVNvqvJnDSko0R7qXmWuBsq', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:55', '2026-05-28 14:05:55', NULL),
(70, 'e327cfef-5925-4afa-9428-aad3f6026ef9', 'skychase66', 'skychase66@example.com', '$2y$12$bLC4uadcAzus20q0zUNRVup5rPLpGbQi.xCox1P/tt.pEHYqbNaCi', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:55', '2026-05-28 14:05:55', NULL),
(71, '81fae25a-62e7-4c93-b301-c7af9f329587', 'chloevelvet67', 'chloevelvet67@example.com', '$2y$12$7T3oledRWpW30WuyD4DNZO6S1Z2UHE9yQhoy63KGHWEsuRmi.zaYu', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:55', '2026-05-28 14:05:55', NULL),
(72, 'b2ffab39-dd77-4c87-948b-14155c7485fe', 'siennarose68', 'siennarose68@example.com', '$2y$12$i0TOapU8pbYccv0Bp8BGNexv1XvKVLItyUjX/.O2SRaFxTVTbZT5a', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:56', '2026-05-28 14:05:56', NULL),
(73, '22affe9b-9e45-463d-a3ab-4da6be869f12', 'elenamonroe69', 'elenamonroe69@example.com', '$2y$12$z0Ead9UlAPsnLIOmtIVIVO/LsZzCAHXVZ2L40m5E1Zh2MIXiWmdZu', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:56', '2026-05-28 14:05:56', NULL),
(74, '997549fc-c83c-41ab-939e-72761f8f75ab', 'biancavance70', 'biancavance70@example.com', '$2y$12$NW8HpUP9Lu2BhE2DG4V03.N8gfoxbMY0iG5EazfzsH2tMi9BbPpSq', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:56', '2026-05-28 14:05:56', NULL),
(75, '3145e254-2707-465b-b041-b04c5db9e4f6', 'nataliavance71', 'nataliavance71@example.com', '$2y$12$sRvMcRiCLaBbzyKtbnL9/OLycqaWmc1.uu4hS8g8qlHfshx0qBmEu', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:56', '2026-05-28 14:05:56', NULL),
(76, '055fd990-ae65-4114-a254-772654f47886', 'nicoleknight72', 'nicoleknight72@example.com', '$2y$12$obVz0sqEIzPlFazd7mysOeYcVrS4ByoUTNq0jmi0nU.QeCAA0id8.', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:57', '2026-05-28 14:05:57', NULL),
(77, '38a2596a-4813-4c23-8b38-1abd36b67a74', 'brookevance73', 'brookevance73@example.com', '$2y$12$SF6S39zBRXky7zzlV53Pue7aOFwf78HZ5LScLce5j9OCgyN8w8KgO', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:57', '2026-05-28 14:05:57', NULL),
(78, '506d0e14-7a5d-4c78-af62-e6cf1c51ebaf', 'amberpierce74', 'amberpierce74@example.com', '$2y$12$gVAtcQAKpbM0QhlCq/quGOzMUvuUgXFEIA7YiSSlUDsm.aMcAB1IK', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:57', '2026-05-28 14:05:57', NULL),
(79, 'b19258f5-588e-4f68-b589-5cf26ff775cd', 'parisvelvet75', 'parisvelvet75@example.com', '$2y$12$AZwLS333/w6W2h.kYjQkseaWD6Ue/Et9KngzGvpXxqsBUC4Ir6hIi', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:57', '2026-05-28 14:05:57', NULL),
(80, 'c2e4d66f-2f71-4192-bd8c-b572d706e415', 'scarlettstone76', 'scarlettstone76@example.com', '$2y$12$MLGoPL2SoA4U.H9BuVEgWuYTPN3sQNKtJ/otfv5nb7v34wM6kyhV6', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:57', '2026-05-28 14:05:57', NULL),
(81, 'ba0a60ea-4f57-4f58-a9a8-1cbd6630e21a', 'miastone77', 'miastone77@example.com', '$2y$12$sZYX5qTTd0RRmiIKqifhM.l0uwtSn26RKzihAiC8diFhuCkp88.6G', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:58', '2026-05-28 14:05:58', NULL),
(82, '129c84d0-59f8-4c94-a3ce-7dcbf4c894cf', 'gracethorne78', 'gracethorne78@example.com', '$2y$12$pmZs/kEQdsYeYOIvD2YIve3fzHOiMYqYfxCmt7BGP0WJQJ3yTAKGq', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:58', '2026-05-28 14:05:58', NULL),
(83, '8ad57c04-d7de-413a-b6cf-53dfbe8d679f', 'ivybrooks79', 'ivybrooks79@example.com', '$2y$12$vppTVIOjRMlJEke/xWlrAeHSy2xzowvOsUWbGrmZXYO9/0dyzhvVi', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:58', '2026-05-28 14:05:58', NULL),
(84, 'cb2d611e-c631-4f21-a3a9-3fb9148cd921', 'carmendelaney80', 'carmendelaney80@example.com', '$2y$12$fuJZHNzbpYp/SinZt6fV2OoXMC0BLy2uZw7OTiKoLl/n8nD6vX.bC', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:58', '2026-05-28 14:05:58', NULL),
(85, '749804e3-13f2-4477-9043-660b3df27a82', 'amberchase81', 'amberchase81@example.com', '$2y$12$jRq33VKtT0QCf3IeqFReIePMzfR0YlR2rzJHuo2ZIOajcw62Ryhv6', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:58', '2026-05-28 14:05:58', NULL),
(86, 'ee0a7e46-8334-471c-9a70-063e74d77892', 'elenavelvet82', 'elenavelvet82@example.com', '$2y$12$5azO2bZe0hcGRlUDe2byCOuQCbRbA/9j9W69ceSswX9pBfJmwx98G', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:59', '2026-05-28 14:05:59', NULL),
(87, 'fd74e052-2c5c-4f09-a24d-f2c0b388df22', 'jadechase83', 'jadechase83@example.com', '$2y$12$qkwEL3xY7AB/oWPrHSqsT.gwnUZ6TfwK0PPKNoNdWlnnal4Y.Aj7a', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:59', '2026-05-28 14:05:59', NULL),
(88, '426023ef-bc58-4007-87fe-46e37b90afdc', 'daisyhayes84', 'daisyhayes84@example.com', '$2y$12$YHB/rWd45Iewp5M2RbDF3uAoVfZbq5UYN348jnXJVhJvhNJFQT2dm', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:59', '2026-05-28 14:05:59', NULL),
(89, '967c62b8-1aff-4763-9259-40a177c0dd1a', 'mayastone85', 'mayastone85@example.com', '$2y$12$D8Iyl6AtYf53HyOTTdPsz.3vNE556XNQ3UYHhkfrdWlsJ8SR5G3Xm', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:59', '2026-05-28 14:05:59', NULL),
(90, '6a662020-0533-4c03-a56a-a648312405a7', 'graceknight86', 'graceknight86@example.com', '$2y$12$ikVMeYilVRSJ5zXueL8wNubkUiIQFulV4Grtqqk8yniI4nT4kcHGa', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:05:59', '2026-05-28 14:05:59', NULL),
(91, '07630dd3-f0b1-4dc0-83e1-ebe891c0cf08', 'serenastone87', 'serenastone87@example.com', '$2y$12$ph1uz8xpetJejSku4VPKaOYRi7grwt.V1XQtkM2SeHed9KCbrmSmC', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:06:00', '2026-05-28 14:06:00', NULL),
(92, 'fc2ccde4-8c88-481b-ad11-ccc2b0cc9e33', 'parisdelaney88', 'parisdelaney88@example.com', '$2y$12$9iinIQqDEaN2rvPcrKLJ9.RKaHwr9HwPq7uGsl8oHXsPQ0avHYqjq', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:06:00', '2026-05-28 14:06:00', NULL),
(93, '2cd7aacd-f9f1-4396-a13d-e3a5b7f72e50', 'jasminestar89', 'jasminestar89@example.com', '$2y$12$F3NYlpJzyudX08E/jr48SOfGBODE9EIVC5O./uDyb/CGFOIGkbl9a', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:06:00', '2026-05-28 14:06:00', NULL),
(94, '3f7b276b-b50d-4ced-a644-bd5d07167e24', 'carmenthorne90', 'carmenthorne90@example.com', '$2y$12$cVC/LPyRWX.GaPSq3SYDie9nYlgEJijCpMd6jGwb4YSK8DViF9y3W', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:06:00', '2026-05-28 14:06:00', NULL),
(95, '1d4318e7-8e61-4f31-867f-29a614b7bac2', 'lunahayes91', 'lunahayes91@example.com', '$2y$12$nrCQ4zIOEBo/nmyYpEdp.Ohku1LCklVGOQpoXOQL0Gonw.7SXV0IG', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:06:00', '2026-05-28 14:06:00', NULL),
(96, '4d719794-8fa9-4c5d-b32e-6818ebc8f2b4', 'jasminedelaney92', 'jasminedelaney92@example.com', '$2y$12$oJQKTyScdtO1uO7ouolu/ORTpPwNlfzZHlwyJUoVmX0RruwH2RtTG', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:06:01', '2026-05-28 14:06:01', NULL),
(97, 'ba9af7a5-6345-4b5a-9c1b-99b386aa2d32', 'roxymonroe93', 'roxymonroe93@example.com', '$2y$12$CGEhhqDD50CYUCG2D1eMb.2TvaARDLeLMfCv9HYAo7za8c0iiLZCm', NULL, '0000-00-00', 1, NULL, NULL, 0.0000, 'active', 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-05-28 14:06:01', '2026-05-28 14:06:01', NULL),
(98, 'ff487092-808d-4ff6-b60f-3018b480e2f4', 'Zander', 'zander@gmail.com', '$2y$12$yU5ilrj/NMPsWKIP7bsfxexN0y/b4A1fzk1TMCRs3QkG7NNQKQYDG', NULL, '1997-01-16', 1, '2026-06-02 05:38:40', 'dob_declaration', 0.0000, 'active', 1, '125fd3c8f3ca81ec27a63cc08ab0bfdeb772f538812124ec2a4fca28c869f3f3', '2026-06-03 05:38:40', NULL, NULL, 0, NULL, NULL, NULL, '2026-06-02 08:38:40', '2026-06-02 08:39:22', NULL),
(99, '08b4bf72-2bdb-44d7-90f1-593c6cd9e5c4', 'Alex', 'info@lexdigitals.co.za', '$2y$12$Tw5dja0cYRh8OFY.Z/wukek8pzaQGET0zazkvrOMaTZhYewMJF/Ve', NULL, '1998-03-12', 1, '2026-06-02 05:41:06', 'dob_declaration', 91.0000, 'active', 1, '2cb4be95c429ac72e9b11d629aa7caf578eaa178c272c3ce9bbfaa5f1f893d3f', '2026-06-03 05:41:06', NULL, NULL, 0, NULL, '2026-06-03 09:13:13', '168.210.241.18', '2026-06-02 08:41:06', '2026-06-03 09:17:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `session_id` char(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payload` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `expires_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_actor` (`actor_type`,`actor_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `calls`
--
ALTER TABLE `calls`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD KEY `call_link_id` (`call_link_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_performer` (`performer_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_started` (`started_at`);

--
-- Indexes for table `call_links`
--
ALTER TABLE `call_links`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `performer_id` (`performer_id`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_user_status` (`user_id`,`status`);

--
-- Indexes for table `credit_ledger`
--
ALTER TABLE `credit_ledger`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `credit_packages`
--
ALTER TABLE `credit_packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_queue`
--
ALTER TABLE `email_queue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_read` (`user_id`,`is_read`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `performers`
--
ALTER TABLE `performers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_online` (`online_status`);

--
-- Indexes for table `performer_payouts`
--
ALTER TABLE `performer_payouts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_performer` (`performer_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `performer_photos`
--
ALTER TABLE `performer_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_performer` (`performer_id`);

--
-- Indexes for table `rate_limits`
--
ALTER TABLE `rate_limits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ident_action` (`identifier`,`action`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `call_id` (`call_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_performer` (`performer_id`),
  ADD KEY `idx_approved` (`is_approved`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `streams`
--
ALTER TABLE `streams`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD KEY `performer_id` (`performer_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD UNIQUE KEY `payfast_pf_payment_id` (`payfast_pf_payment_id`),
  ADD KEY `package_id` (`package_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_merchant_ref` (`merchant_reference`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_uuid` (`uuid`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_expires` (`expires_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `calls`
--
ALTER TABLE `calls`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `call_links`
--
ALTER TABLE `call_links`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `credit_ledger`
--
ALTER TABLE `credit_ledger`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `credit_packages`
--
ALTER TABLE `credit_packages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `email_queue`
--
ALTER TABLE `email_queue`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `performers`
--
ALTER TABLE `performers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `performer_payouts`
--
ALTER TABLE `performer_payouts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `performer_photos`
--
ALTER TABLE `performer_photos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rate_limits`
--
ALTER TABLE `rate_limits`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `streams`
--
ALTER TABLE `streams`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `calls`
--
ALTER TABLE `calls`
  ADD CONSTRAINT `calls_ibfk_1` FOREIGN KEY (`call_link_id`) REFERENCES `call_links` (`id`),
  ADD CONSTRAINT `calls_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `calls_ibfk_3` FOREIGN KEY (`performer_id`) REFERENCES `performers` (`id`);

--
-- Constraints for table `call_links`
--
ALTER TABLE `call_links`
  ADD CONSTRAINT `call_links_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `call_links_ibfk_2` FOREIGN KEY (`performer_id`) REFERENCES `performers` (`id`);

--
-- Constraints for table `credit_ledger`
--
ALTER TABLE `credit_ledger`
  ADD CONSTRAINT `credit_ledger_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `performers`
--
ALTER TABLE `performers`
  ADD CONSTRAINT `performers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `performer_payouts`
--
ALTER TABLE `performer_payouts`
  ADD CONSTRAINT `performer_payouts_ibfk_1` FOREIGN KEY (`performer_id`) REFERENCES `performers` (`id`);

--
-- Constraints for table `performer_photos`
--
ALTER TABLE `performer_photos`
  ADD CONSTRAINT `performer_photos_ibfk_1` FOREIGN KEY (`performer_id`) REFERENCES `performers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`call_id`) REFERENCES `calls` (`id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`performer_id`) REFERENCES `performers` (`id`);

--
-- Constraints for table `streams`
--
ALTER TABLE `streams`
  ADD CONSTRAINT `streams_ibfk_1` FOREIGN KEY (`performer_id`) REFERENCES `performers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`package_id`) REFERENCES `credit_packages` (`id`);

--
-- Constraints for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

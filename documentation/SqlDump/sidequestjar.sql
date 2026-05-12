-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 12, 2026 at 03:38 AM
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
-- Database: `sidequestjar`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE `friends` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `friend_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('pending','accepted') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jars`
--

CREATE TABLE `jars` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL,
  `type` enum('public_jar','private_jar','friends_only_jar','invite_only_jar','official_jar') NOT NULL DEFAULT 'public_jar',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `picture` varchar(255) NOT NULL DEFAULT 'images/default-jar.png',
  `current_daily_quest_id` bigint(20) UNSIGNED DEFAULT NULL,
  `previous_daily_quest_id` bigint(20) UNSIGNED DEFAULT NULL,
  `current_daily_quest_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jars`
--

INSERT INTO `jars` (`id`, `name`, `description`, `type`, `created_by`, `picture`, `current_daily_quest_id`, `previous_daily_quest_id`, `current_daily_quest_date`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Today\'s Adventure', NULL, 'official_jar', 1, 'images/default-jar.png', NULL, NULL, NULL, '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(2, 'Bookworm\'s Journey', 'Track your progress and discover new worlds through reading.', 'friends_only_jar', 43, 'images/default-jar.png', NULL, NULL, NULL, '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(3, 'Daily Hustle', 'Delicious challenges to spice up your culinary skills.', 'invite_only_jar', 44, 'images/default-jar.png', NULL, NULL, NULL, '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(4, 'Nature Explorer', 'Small actions to help you stay present and grounded.', 'public_jar', 45, 'images/default-jar.png', NULL, NULL, NULL, '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(5, 'Tech Explorer', 'Delicious challenges to spice up your culinary skills.', 'public_jar', 46, 'images/default-jar.png', NULL, NULL, NULL, '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(6, 'Financial Freedom', 'Delicious challenges to spice up your culinary skills.', 'friends_only_jar', 47, 'images/default-jar.png', NULL, NULL, NULL, '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(7, 'Tech Explorer', 'Delicious challenges to spice up your culinary skills.', 'invite_only_jar', 48, 'images/default-jar.png', NULL, NULL, NULL, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(8, 'Mindful Moments', 'Track your progress and discover new worlds through reading.', 'public_jar', 49, 'images/default-jar.png', NULL, NULL, NULL, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(9, 'Tech Explorer', 'Quests designed to keep your body moving and healthy.', 'public_jar', 50, 'images/default-jar.png', NULL, NULL, NULL, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(10, 'Financial Freedom', 'Daily prompts to ignite your creativity and passion projects.', 'public_jar', 51, 'images/default-jar.png', NULL, NULL, NULL, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(11, 'Language Learner', 'Small actions to help you stay present and grounded.', 'private_jar', 52, 'images/default-jar.png', NULL, NULL, NULL, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(12, 'Nature Explorer', 'Fun ways to connect with friends and meet new people.', 'public_jar', 53, 'images/default-jar.png', NULL, NULL, NULL, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(13, 'Nature Explorer', 'Track your progress and discover new worlds through reading.', 'private_jar', 54, 'images/default-jar.png', NULL, NULL, NULL, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(14, 'Kindness Jar', 'Delicious challenges to spice up your culinary skills.', 'private_jar', 55, 'images/default-jar.png', NULL, NULL, NULL, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(15, 'Kitchen Wizard', 'Quests designed to keep your body moving and healthy.', 'public_jar', 56, 'images/default-jar.png', NULL, NULL, NULL, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(16, 'Kindness Jar', 'Get outside and appreciate the beauty of the natural world.', 'private_jar', 57, 'images/default-jar.png', NULL, NULL, NULL, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(17, 'Tech Explorer', 'A collection of tasks to keep you productive and motivated every day.', 'public_jar', 58, 'images/default-jar.png', NULL, NULL, NULL, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(18, 'Kitchen Wizard', 'Get outside and appreciate the beauty of the natural world.', 'private_jar', 59, 'images/default-jar.png', NULL, NULL, NULL, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(19, 'Language Learner', 'Small actions to help you stay present and grounded.', 'invite_only_jar', 60, 'images/default-jar.png', NULL, NULL, NULL, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(20, 'Financial Freedom', 'Small actions to help you stay present and grounded.', 'private_jar', 61, 'images/default-jar.png', NULL, NULL, NULL, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(21, 'Social Butterfly', 'Get outside and appreciate the beauty of the natural world.', 'friends_only_jar', 62, 'images/default-jar.png', NULL, NULL, NULL, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `jar_comments`
--

CREATE TABLE `jar_comments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `jar_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jar_invitations`
--

CREATE TABLE `jar_invitations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `jar_id` bigint(20) UNSIGNED NOT NULL,
  `inviter_id` bigint(20) UNSIGNED NOT NULL,
  `invitee_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('pending','accepted','declined') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jar_quests`
--

CREATE TABLE `jar_quests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `jar_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL,
  `picture` varchar(255) NOT NULL DEFAULT 'images/default-quest.png',
  `completions` int(11) NOT NULL DEFAULT 0,
  `xp_gain` int(11) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jar_quests`
--

INSERT INTO `jar_quests` (`id`, `jar_id`, `title`, `description`, `picture`, `completions`, `xp_gain`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Hug a tree', 'Go outside and hug a tree', 'images/default-quest.png', 0, NULL, NULL, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(2, 1, 'RopeAJump', 'Go outside and rope a jump', 'images/default-quest.png', 0, NULL, NULL, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(3, 16, 'Healthy Snack', 'Eat a piece of fruit or some nuts instead of processed snacks.', 'images/default-quest.png', 0, NULL, 56, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(4, 9, 'Hydration Hero', 'Drink a full glass of water right now.', 'images/default-quest.png', 0, NULL, 4, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(5, 8, 'Mindful Breathing', 'Take 10 deep, controlled breaths.', 'images/default-quest.png', 0, NULL, 10, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(6, 1, 'Healthy Snack', 'Eat a piece of fruit or some nuts instead of processed snacks.', 'images/default-quest.png', 0, NULL, 24, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(7, 18, 'Hydration Hero', 'Drink a full glass of water right now.', 'images/default-quest.png', 0, NULL, 24, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(8, 16, 'Digital Detox', 'Stay away from social media for the next hour.', 'images/default-quest.png', 0, NULL, 41, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(9, 21, 'Learn Something New', 'Look up a random fact or a new word.', 'images/default-quest.png', 0, NULL, 28, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(10, 4, 'Gratitude Journal', 'Write down three things you are grateful for today.', 'images/default-quest.png', 0, NULL, 47, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(11, 21, 'Tidy Up', 'Spend 2 minutes decluttering your immediate workspace.', 'images/default-quest.png', 0, NULL, 38, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(12, 20, 'Tidy Up', 'Spend 2 minutes decluttering your immediate workspace.', 'images/default-quest.png', 0, NULL, 24, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(13, 18, 'Digital Detox', 'Stay away from social media for the next hour.', 'images/default-quest.png', 0, NULL, 14, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(14, 2, 'Hydration Hero', 'Drink a full glass of water right now.', 'images/default-quest.png', 0, NULL, 49, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(15, 17, 'Digital Detox', 'Stay away from social media for the next hour.', 'images/default-quest.png', 0, NULL, 30, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(16, 18, 'Healthy Snack', 'Eat a piece of fruit or some nuts instead of processed snacks.', 'images/default-quest.png', 0, NULL, 37, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(17, 4, 'Mindful Breathing', 'Take 10 deep, controlled breaths.', 'images/default-quest.png', 0, NULL, 29, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(18, 7, 'Morning Stretch', 'Start your day with a 5-minute full-body stretch.', 'images/default-quest.png', 0, NULL, 20, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(19, 5, 'Learn Something New', 'Look up a random fact or a new word.', 'images/default-quest.png', 0, NULL, 35, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(20, 8, 'Walk It Out', 'Go for a quick 10-minute walk around the block.', 'images/default-quest.png', 0, NULL, 9, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(21, 4, 'Learn Something New', 'Look up a random fact or a new word.', 'images/default-quest.png', 0, NULL, 8, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(22, 18, 'Digital Detox', 'Stay away from social media for the next hour.', 'images/default-quest.png', 0, NULL, 51, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(23, 7, 'Mindful Breathing', 'Take 10 deep, controlled breaths.', 'images/default-quest.png', 0, NULL, 16, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(24, 11, 'Hydration Hero', 'Drink a full glass of water right now.', 'images/default-quest.png', 0, NULL, 16, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(25, 15, 'Kindness Note', 'Send a quick thank-you message to someone you appreciate.', 'images/default-quest.png', 0, NULL, 39, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(26, 18, 'Morning Stretch', 'Start your day with a 5-minute full-body stretch.', 'images/default-quest.png', 0, NULL, 58, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(27, 6, 'Digital Detox', 'Stay away from social media for the next hour.', 'images/default-quest.png', 0, NULL, 46, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(28, 2, 'Tidy Up', 'Spend 2 minutes decluttering your immediate workspace.', 'images/default-quest.png', 0, NULL, 3, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(29, 8, 'Kindness Note', 'Send a quick thank-you message to someone you appreciate.', 'images/default-quest.png', 0, NULL, 12, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(30, 21, 'Learn Something New', 'Look up a random fact or a new word.', 'images/default-quest.png', 0, NULL, 40, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(31, 12, 'Tidy Up', 'Spend 2 minutes decluttering your immediate workspace.', 'images/default-quest.png', 0, NULL, 13, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(32, 7, 'Hydration Hero', 'Drink a full glass of water right now.', 'images/default-quest.png', 0, NULL, 9, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(33, 10, 'Kindness Note', 'Send a quick thank-you message to someone you appreciate.', 'images/default-quest.png', 0, NULL, 53, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(34, 4, 'Morning Stretch', 'Start your day with a 5-minute full-body stretch.', 'images/default-quest.png', 0, NULL, 52, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(35, 15, 'Digital Detox', 'Stay away from social media for the next hour.', 'images/default-quest.png', 0, NULL, 17, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(36, 9, 'Tidy Up', 'Spend 2 minutes decluttering your immediate workspace.', 'images/default-quest.png', 0, NULL, 2, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(37, 20, 'Learn Something New', 'Look up a random fact or a new word.', 'images/default-quest.png', 0, NULL, 21, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(38, 21, 'Kindness Note', 'Send a quick thank-you message to someone you appreciate.', 'images/default-quest.png', 0, NULL, 46, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(39, 16, 'Kindness Note', 'Send a quick thank-you message to someone you appreciate.', 'images/default-quest.png', 0, NULL, 32, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(40, 17, 'Learn Something New', 'Look up a random fact or a new word.', 'images/default-quest.png', 0, NULL, 32, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(41, 12, 'Digital Detox', 'Stay away from social media for the next hour.', 'images/default-quest.png', 0, NULL, 11, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(42, 21, 'Morning Stretch', 'Start your day with a 5-minute full-body stretch.', 'images/default-quest.png', 0, NULL, 18, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(43, 3, 'Mindful Breathing', 'Take 10 deep, controlled breaths.', 'images/default-quest.png', 0, NULL, 9, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(44, 16, 'Kindness Note', 'Send a quick thank-you message to someone you appreciate.', 'images/default-quest.png', 0, NULL, 27, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(45, 19, 'Morning Stretch', 'Start your day with a 5-minute full-body stretch.', 'images/default-quest.png', 0, NULL, 61, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(46, 13, 'Kindness Note', 'Send a quick thank-you message to someone you appreciate.', 'images/default-quest.png', 0, NULL, 46, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(47, 18, 'Walk It Out', 'Go for a quick 10-minute walk around the block.', 'images/default-quest.png', 0, NULL, 49, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(48, 12, 'Morning Stretch', 'Start your day with a 5-minute full-body stretch.', 'images/default-quest.png', 0, NULL, 48, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(49, 16, 'Morning Stretch', 'Start your day with a 5-minute full-body stretch.', 'images/default-quest.png', 0, NULL, 4, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(50, 9, 'Learn Something New', 'Look up a random fact or a new word.', 'images/default-quest.png', 0, NULL, 46, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(51, 13, 'Gratitude Journal', 'Write down three things you are grateful for today.', 'images/default-quest.png', 0, NULL, 9, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(52, 6, 'Hydration Hero', 'Drink a full glass of water right now.', 'images/default-quest.png', 0, NULL, 1, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(53, 17, 'Healthy Snack', 'Eat a piece of fruit or some nuts instead of processed snacks.', 'images/default-quest.png', 0, NULL, 37, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(54, 8, 'Kindness Note', 'Send a quick thank-you message to someone you appreciate.', 'images/default-quest.png', 0, NULL, 59, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(55, 20, 'Hydration Hero', 'Drink a full glass of water right now.', 'images/default-quest.png', 0, NULL, 24, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(56, 20, 'Kindness Note', 'Send a quick thank-you message to someone you appreciate.', 'images/default-quest.png', 0, NULL, 60, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(57, 15, 'Learn Something New', 'Look up a random fact or a new word.', 'images/default-quest.png', 0, NULL, 36, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(58, 12, 'Walk It Out', 'Go for a quick 10-minute walk around the block.', 'images/default-quest.png', 0, NULL, 62, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(59, 2, 'Gratitude Journal', 'Write down three things you are grateful for today.', 'images/default-quest.png', 0, NULL, 7, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(60, 20, 'Tidy Up', 'Spend 2 minutes decluttering your immediate workspace.', 'images/default-quest.png', 0, NULL, 12, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(61, 21, 'Healthy Snack', 'Eat a piece of fruit or some nuts instead of processed snacks.', 'images/default-quest.png', 0, NULL, 56, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(62, 9, 'Mindful Breathing', 'Take 10 deep, controlled breaths.', 'images/default-quest.png', 0, NULL, 53, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(63, 7, 'Digital Detox', 'Stay away from social media for the next hour.', 'images/default-quest.png', 0, NULL, 51, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(64, 3, 'Kindness Note', 'Send a quick thank-you message to someone you appreciate.', 'images/default-quest.png', 0, NULL, 26, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(65, 7, 'Walk It Out', 'Go for a quick 10-minute walk around the block.', 'images/default-quest.png', 0, NULL, 3, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(66, 11, 'Digital Detox', 'Stay away from social media for the next hour.', 'images/default-quest.png', 0, NULL, 11, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(67, 9, 'Tidy Up', 'Spend 2 minutes decluttering your immediate workspace.', 'images/default-quest.png', 0, NULL, 8, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(68, 1, 'Healthy Snack', 'Eat a piece of fruit or some nuts instead of processed snacks.', 'images/default-quest.png', 0, NULL, 25, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(69, 11, 'Healthy Snack', 'Eat a piece of fruit or some nuts instead of processed snacks.', 'images/default-quest.png', 0, NULL, 4, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(70, 1, 'Gratitude Journal', 'Write down three things you are grateful for today.', 'images/default-quest.png', 0, NULL, 2, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(71, 6, 'Mindful Breathing', 'Take 10 deep, controlled breaths.', 'images/default-quest.png', 0, NULL, 39, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(72, 11, 'Gratitude Journal', 'Write down three things you are grateful for today.', 'images/default-quest.png', 0, NULL, 31, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(73, 17, 'Morning Stretch', 'Start your day with a 5-minute full-body stretch.', 'images/default-quest.png', 0, NULL, 26, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(74, 3, 'Hydration Hero', 'Drink a full glass of water right now.', 'images/default-quest.png', 0, NULL, 36, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(75, 17, 'Gratitude Journal', 'Write down three things you are grateful for today.', 'images/default-quest.png', 0, NULL, 20, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(76, 17, 'Hydration Hero', 'Drink a full glass of water right now.', 'images/default-quest.png', 0, NULL, 42, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(77, 10, 'Gratitude Journal', 'Write down three things you are grateful for today.', 'images/default-quest.png', 0, NULL, 57, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(78, 13, 'Mindful Breathing', 'Take 10 deep, controlled breaths.', 'images/default-quest.png', 0, NULL, 30, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(79, 2, 'Digital Detox', 'Stay away from social media for the next hour.', 'images/default-quest.png', 0, NULL, 51, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(80, 4, 'Gratitude Journal', 'Write down three things you are grateful for today.', 'images/default-quest.png', 0, NULL, 33, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(81, 20, 'Walk It Out', 'Go for a quick 10-minute walk around the block.', 'images/default-quest.png', 0, NULL, 31, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(82, 17, 'Digital Detox', 'Stay away from social media for the next hour.', 'images/default-quest.png', 0, NULL, 5, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(83, 9, 'Hydration Hero', 'Drink a full glass of water right now.', 'images/default-quest.png', 0, NULL, 11, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(84, 19, 'Tidy Up', 'Spend 2 minutes decluttering your immediate workspace.', 'images/default-quest.png', 0, NULL, 13, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(85, 15, 'Digital Detox', 'Stay away from social media for the next hour.', 'images/default-quest.png', 0, NULL, 25, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(86, 5, 'Hydration Hero', 'Drink a full glass of water right now.', 'images/default-quest.png', 0, NULL, 12, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(87, 5, 'Healthy Snack', 'Eat a piece of fruit or some nuts instead of processed snacks.', 'images/default-quest.png', 0, NULL, 21, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(88, 8, 'Morning Stretch', 'Start your day with a 5-minute full-body stretch.', 'images/default-quest.png', 0, NULL, 14, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(89, 6, 'Hydration Hero', 'Drink a full glass of water right now.', 'images/default-quest.png', 0, NULL, 33, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(90, 21, 'Walk It Out', 'Go for a quick 10-minute walk around the block.', 'images/default-quest.png', 0, NULL, 2, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(91, 7, 'Walk It Out', 'Go for a quick 10-minute walk around the block.', 'images/default-quest.png', 0, NULL, 33, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(92, 10, 'Gratitude Journal', 'Write down three things you are grateful for today.', 'images/default-quest.png', 0, NULL, 18, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(93, 3, 'Morning Stretch', 'Start your day with a 5-minute full-body stretch.', 'images/default-quest.png', 0, NULL, 17, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(94, 17, 'Healthy Snack', 'Eat a piece of fruit or some nuts instead of processed snacks.', 'images/default-quest.png', 0, NULL, 48, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(95, 8, 'Kindness Note', 'Send a quick thank-you message to someone you appreciate.', 'images/default-quest.png', 0, NULL, 55, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(96, 4, 'Kindness Note', 'Send a quick thank-you message to someone you appreciate.', 'images/default-quest.png', 0, NULL, 61, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(97, 18, 'Mindful Breathing', 'Take 10 deep, controlled breaths.', 'images/default-quest.png', 0, NULL, 38, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(98, 2, 'Kindness Note', 'Send a quick thank-you message to someone you appreciate.', 'images/default-quest.png', 0, NULL, 61, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(99, 19, 'Healthy Snack', 'Eat a piece of fruit or some nuts instead of processed snacks.', 'images/default-quest.png', 0, NULL, 6, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(100, 4, 'Learn Something New', 'Look up a random fact or a new word.', 'images/default-quest.png', 0, NULL, 25, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(101, 9, 'Digital Detox', 'Stay away from social media for the next hour.', 'images/default-quest.png', 0, NULL, 10, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(102, 17, 'Tidy Up', 'Spend 2 minutes decluttering your immediate workspace.', 'images/default-quest.png', 0, NULL, 6, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `jar_users`
--

CREATE TABLE `jar_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `jar_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `streak` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jar_users`
--

INSERT INTO `jar_users` (`id`, `jar_id`, `user_id`, `streak`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 10, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(2, 1, 3, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(3, 1, 4, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(4, 1, 5, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(5, 1, 6, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(6, 1, 7, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(7, 1, 8, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(8, 1, 9, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(9, 1, 10, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(10, 1, 11, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(11, 1, 12, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(12, 1, 13, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(13, 1, 14, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(14, 1, 15, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(15, 1, 16, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(16, 1, 17, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(17, 1, 18, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(18, 1, 19, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(19, 1, 20, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(20, 1, 21, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(21, 1, 22, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(22, 1, 23, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(23, 1, 24, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(24, 1, 25, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(25, 1, 26, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(26, 1, 27, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(27, 1, 28, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(28, 1, 29, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(29, 1, 30, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(30, 1, 31, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(31, 1, 32, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(32, 1, 33, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(33, 1, 34, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(34, 1, 35, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(35, 1, 36, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(36, 1, 37, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(37, 1, 38, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(38, 1, 39, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(39, 1, 40, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(40, 1, 41, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(41, 1, 42, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(42, 1, 43, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(43, 1, 44, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(44, 1, 45, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(45, 1, 46, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(46, 1, 47, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(47, 1, 48, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(48, 1, 49, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(49, 1, 50, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(50, 1, 51, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(51, 1, 52, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(52, 1, 53, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(53, 1, 54, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(54, 1, 55, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(55, 1, 56, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(56, 1, 57, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(57, 1, 58, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(58, 1, 59, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(59, 1, 60, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(60, 1, 61, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(61, 1, 62, 0, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(62, 2, 14, 172, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(63, 2, 2, 367, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(64, 2, 37, 239, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(65, 2, 36, 254, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(66, 2, 27, 31, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(67, 2, 33, 435, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(68, 2, 53, 104, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(69, 3, 18, 195, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(70, 3, 56, 203, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(71, 3, 23, 394, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(72, 3, 36, 227, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(73, 3, 46, 415, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(74, 3, 8, 339, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(75, 3, 38, 311, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(76, 3, 7, 275, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(77, 4, 57, 233, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(78, 4, 17, 403, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(79, 4, 5, 338, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(80, 5, 18, 276, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(81, 5, 31, 260, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(82, 5, 43, 237, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(83, 5, 15, 150, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(84, 5, 28, 422, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(85, 5, 34, 444, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(86, 5, 56, 159, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(87, 5, 5, 490, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(88, 5, 16, 19, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(89, 5, 59, 12, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(90, 6, 36, 68, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(91, 6, 22, 360, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(92, 6, 44, 230, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(93, 6, 32, 433, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(94, 6, 23, 451, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(95, 6, 59, 237, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(96, 6, 7, 253, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(97, 6, 41, 17, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(98, 6, 8, 255, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(99, 6, 40, 252, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(100, 7, 52, 28, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(101, 7, 61, 344, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(102, 7, 19, 136, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(103, 7, 58, 417, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(104, 7, 12, 284, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(105, 7, 33, 291, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(106, 7, 60, 480, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(107, 7, 47, 429, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(108, 7, 43, 186, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(109, 7, 5, 323, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(110, 8, 60, 177, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(111, 8, 38, 310, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(112, 8, 59, 124, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(113, 8, 44, 27, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(114, 8, 61, 343, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(115, 8, 37, 242, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(116, 9, 27, 369, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(117, 9, 54, 464, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(118, 9, 5, 470, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(119, 10, 40, 389, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(120, 10, 22, 283, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(121, 11, 23, 367, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(122, 11, 51, 442, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(123, 11, 41, 165, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(124, 11, 11, 332, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(125, 11, 7, 341, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(126, 12, 62, 116, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(127, 12, 18, 406, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(128, 12, 45, 430, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(129, 12, 43, 60, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(130, 12, 2, 490, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(131, 12, 10, 383, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(132, 12, 31, 305, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(133, 12, 57, 293, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(134, 12, 40, 101, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(135, 12, 54, 11, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(136, 13, 29, 463, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(137, 13, 62, 414, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(138, 13, 61, 283, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(139, 13, 12, 112, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(140, 13, 21, 245, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(141, 13, 30, 252, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(142, 13, 37, 165, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(143, 13, 27, 461, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(144, 13, 19, 396, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(145, 13, 44, 207, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(146, 14, 22, 326, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(147, 14, 44, 65, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(148, 14, 11, 497, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(149, 14, 59, 264, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(150, 14, 53, 175, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(151, 14, 31, 163, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(152, 14, 56, 111, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(153, 14, 25, 480, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(154, 15, 3, 465, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(155, 15, 51, 109, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(156, 15, 23, 245, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(157, 15, 19, 308, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(158, 15, 18, 21, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(159, 15, 38, 341, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(160, 15, 56, 187, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(161, 15, 55, 422, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(162, 15, 34, 477, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(163, 16, 11, 170, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(164, 16, 35, 233, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(165, 17, 37, 12, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(166, 17, 48, 65, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(167, 17, 8, 124, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(168, 17, 57, 314, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(169, 17, 13, 385, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(170, 17, 55, 286, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(171, 17, 34, 121, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(172, 17, 59, 151, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(173, 17, 21, 80, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(174, 18, 25, 491, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(175, 18, 24, 333, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(176, 18, 43, 263, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(177, 18, 48, 382, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(178, 19, 61, 256, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(179, 19, 38, 209, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(180, 19, 41, 279, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(181, 19, 15, 225, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(182, 19, 37, 34, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(183, 20, 55, 330, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(184, 20, 54, 320, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(185, 20, 11, 273, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(186, 20, 56, 13, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(187, 20, 6, 181, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(188, 20, 59, 174, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(189, 21, 8, 202, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(190, 21, 46, 263, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(191, 21, 56, 56, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(192, 21, 57, 128, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(193, 21, 48, 183, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(194, 21, 27, 288, '2026-05-12 00:21:27', '2026-05-12 00:21:27'),
(195, 21, 44, 51, '2026-05-12 00:21:27', '2026-05-12 00:21:27');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `likeable_type` varchar(255) NOT NULL,
  `likeable_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `receiver_id` bigint(20) UNSIGNED NOT NULL,
  `content` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '0001_01_01_000003_create_jars_table', 1),
(5, '0001_01_01_000004_create_jar_quests_table', 1),
(6, '0001_01_01_000005_create_quest_completions_table', 1),
(7, '0001_01_01_000006_create_jar_users_table', 1),
(8, '2026_02_13_174113_create_friends_table', 1),
(9, '2026_02_13_183243_create_likes_table', 1),
(10, '2026_03_12_143845_create_personal_access_tokens_table', 1),
(11, '2026_04_22_051117_add_daily_quest_fields_to_jars_table', 1),
(12, '2026_05_04_202635_create_messages_table', 1),
(13, '2026_05_10_141750_create_jar_comments_table', 1),
(14, '2026_05_10_172245_create_jar_invitations_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quest_completions`
--

CREATE TABLE `quest_completions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `completion_date` datetime NOT NULL,
  `picture` varchar(255) NOT NULL DEFAULT '',
  `description` longtext DEFAULT NULL,
  `type` enum('public_post','private_post','friends_only_post') NOT NULL DEFAULT 'public_post',
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `jar_quest_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quest_completions`
--

INSERT INTO `quest_completions` (`id`, `completion_date`, `picture`, `description`, `type`, `user_id`, `jar_quest_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '2026-05-02 12:00:00', 'https://via.placeholder.com/640x480.png/00ee00?text=Quest+Completion', 'Seeded streak test completion for ZoliProli2 on 2026-05-02', 'public_post', 2, 70, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(2, '2026-05-03 12:00:00', 'https://via.placeholder.com/640x480.png/00ee00?text=Quest+Completion', 'Seeded streak test completion for ZoliProli2 on 2026-05-03', 'public_post', 2, 68, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(3, '2026-05-04 12:00:00', 'https://via.placeholder.com/640x480.png/00ee00?text=Quest+Completion', 'Seeded streak test completion for ZoliProli2 on 2026-05-04', 'public_post', 2, 6, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(4, '2026-05-05 12:00:00', 'https://via.placeholder.com/640x480.png/00ee00?text=Quest+Completion', 'Seeded streak test completion for ZoliProli2 on 2026-05-05', 'public_post', 2, 2, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(5, '2026-05-06 12:00:00', 'https://via.placeholder.com/640x480.png/00ee00?text=Quest+Completion', 'Seeded streak test completion for ZoliProli2 on 2026-05-06', 'public_post', 2, 1, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(6, '2026-05-07 12:00:00', 'https://via.placeholder.com/640x480.png/00ee00?text=Quest+Completion', 'Seeded streak test completion for ZoliProli2 on 2026-05-07', 'public_post', 2, 70, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(7, '2026-05-08 12:00:00', 'https://via.placeholder.com/640x480.png/00ee00?text=Quest+Completion', 'Seeded streak test completion for ZoliProli2 on 2026-05-08', 'public_post', 2, 68, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(8, '2026-05-09 12:00:00', 'https://via.placeholder.com/640x480.png/00ee00?text=Quest+Completion', 'Seeded streak test completion for ZoliProli2 on 2026-05-09', 'public_post', 2, 6, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(9, '2026-05-10 12:00:00', 'https://via.placeholder.com/640x480.png/00ee00?text=Quest+Completion', 'Seeded streak test completion for ZoliProli2 on 2026-05-10', 'public_post', 2, 2, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL),
(10, '2026-05-11 12:00:00', 'https://via.placeholder.com/640x480.png/00ee00?text=Quest+Completion', 'Seeded streak test completion for ZoliProli2 on 2026-05-11', 'public_post', 2, 1, '2026-05-12 00:21:27', '2026-05-12 00:21:27', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(255) NOT NULL DEFAULT 'images/default-avatar.png',
  `birth_date` date NOT NULL,
  `joined_date` datetime DEFAULT NULL,
  `user_xp` int(11) NOT NULL DEFAULT 0,
  `is_customized` tinyint(1) NOT NULL DEFAULT 0,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `nickname`, `fullname`, `email`, `password`, `profile_picture`, `birth_date`, `joined_date`, `user_xp`, `is_customized`, `is_admin`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'ZoliProli', NULL, 'Zoli Proli', 'ZoliProli@gmail.com', '$2y$12$d3GCQdKbyhlCuhRW7O7t7OYn.o/b9UqmuyX3OXN4oacMcgzlrSSeO', 'images/default-avatar.png', '2001-09-11', NULL, 0, 0, 1, NULL, '2026-05-12 00:21:25', '2026-05-12 00:21:25', NULL),
(2, 'ZoliProli2', NULL, 'Zoli Proli', 'ZoliProli2@gmail.com', '$2y$12$Jhq/PQtQyYxx5pUIAIL6ZeM6yib5v1BPOkph7OgKg7WC1o5zFmbEm', 'images/default-avatar.png', '2001-09-11', NULL, 0, 0, 0, NULL, '2026-05-12 00:21:25', '2026-05-12 00:21:25', NULL),
(3, 'selina.lueilwitz', 'Keegan', 'Bonita Block II', 'davonte93@example.com', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=selina.lueilwitz', '1986-09-25', '2025-10-15 22:01:19', 1024, 1, 0, '6r1DPezibh', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(4, 'tyrese.stiedemann', 'Daniela', 'Dr. Ervin Wunsch', 'mlarkin@example.net', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=tyrese.stiedemann', '2019-04-26', '2026-04-13 14:26:51', 2843, 0, 0, 'Dld1ehAKQy', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(5, 'sschulist', 'Grady', 'Orpha Mraz', 'bins.herminio@example.com', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=sschulist', '1989-09-19', '2025-12-21 04:35:19', 483, 0, 0, 'E2qgY7jDKF', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(6, 'alek.langworth', 'Hal', 'Dr. Myron Shanahan Jr.', 'gulgowski.makenzie@example.org', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=alek.langworth', '2000-06-12', '2026-01-22 03:43:11', 2437, 0, 0, 'EkztxMd240', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(7, 'laurence14', 'Hollie', 'Carlos Dietrich', 'mhodkiewicz@example.com', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=laurence14', '2008-11-19', '2026-04-01 18:06:00', 802, 0, 0, 'PyWDgpO3li', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(8, 'callie.hermann', 'Beau', 'Dr. Jay Marvin IV', 'edgardo.rau@example.org', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=callie.hermann', '2017-10-06', '2025-05-23 22:56:02', 3604, 0, 0, 'kpM4JvchCl', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(9, 'ggrady', 'Rupert', 'Mr. Darrion White', 'cedrick.kunze@example.org', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=ggrady', '1973-11-13', '2025-07-01 13:44:14', 1585, 0, 0, 'HJD0A0hcFP', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(10, 'major80', 'Frankie', 'Stanford Hilpert', 'herzog.wanda@example.org', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=major80', '1982-01-26', '2026-03-08 07:29:37', 3197, 0, 0, '9NtG1cp5Vn', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(11, 'tcole', 'Blaze', 'Miss Sydni Torphy II', 'lorenzo54@example.net', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=tcole', '2003-03-19', '2025-06-08 21:45:02', 1895, 0, 0, 'PV76V9DNRC', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(12, 'deja90', 'Akeem', 'Prof. Tracey Roberts', 'hoyt.tromp@example.com', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=deja90', '2009-10-16', '2025-05-12 06:47:45', 656, 1, 0, 'YgVWyiQsNH', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(13, 'vnienow', 'Camron', 'Ocie Welch', 'pattie29@example.net', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=vnienow', '1974-11-01', '2026-03-02 11:59:18', 4293, 1, 0, 'vWavj5R95u', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(14, 'leland.towne', 'Demario', 'Mr. Jorge Lind', 'lkrajcik@example.com', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=leland.towne', '2014-10-28', '2026-04-16 00:57:51', 3958, 0, 0, 'd5G2p6nNUV', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(15, 'wolf.katharina', 'Erika', 'Mr. Hazle Considine', 'adelia34@example.net', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=wolf.katharina', '1977-03-21', '2026-01-15 16:58:26', 3838, 0, 0, 'AjDtIiX9wz', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(16, 'lesly66', 'Hilbert', 'Harley Murazik', 'kyler07@example.net', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=lesly66', '1974-02-14', '2025-09-27 07:19:33', 2625, 0, 0, 'PAqpFzYnUP', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(17, 'sigmund.feest', 'Uriah', 'Kara Schaefer MD', 'freddie.mueller@example.com', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=sigmund.feest', '1981-10-18', '2025-11-04 04:28:17', 101, 0, 0, 'Zecil6zzM1', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(18, 'klein.ashton', 'Sandra', 'Johanna Rempel', 'lulu25@example.com', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=klein.ashton', '2004-07-27', '2025-12-27 20:25:47', 4461, 0, 0, 'ovVIEuICBU', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(19, 'boyd.gorczany', 'Cameron', 'Prof. Noe Corwin', 'roberto24@example.org', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=boyd.gorczany', '2007-05-22', '2025-10-29 08:17:44', 4253, 1, 0, 'LRIf8cXlbj', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(20, 'xheidenreich', 'Gabriel', 'Lorenzo Hyatt', 'bartoletti.dasia@example.net', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=xheidenreich', '1993-03-01', '2026-01-15 14:51:31', 3658, 0, 0, 'KFFVvx5RyI', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(21, 'stewart.cartwright', 'Kendra', 'Immanuel Schultz', 'devan.dicki@example.com', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=stewart.cartwright', '2025-09-13', '2025-08-17 12:36:51', 2505, 0, 0, 'UddjofubGG', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(22, 'ahamill', 'Maida', 'Eileen Quitzon V', 'wwelch@example.org', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=ahamill', '2018-05-12', '2025-08-09 03:49:45', 1033, 1, 0, 'gzLoBhPbSd', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(23, 'wluettgen', 'Christine', 'Ms. Madaline Ortiz PhD', 'cmurphy@example.org', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=wluettgen', '1977-04-04', '2025-07-16 17:35:59', 4565, 0, 0, 'V3yLRYxd65', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(24, 'rebeca.champlin', 'Tina', 'Izaiah Cronin I', 'qklein@example.com', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=rebeca.champlin', '2009-06-10', '2026-03-05 06:54:53', 2379, 1, 0, 'jAgAZsb20F', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(25, 'nicholas26', 'Lindsay', 'Dr. Jan McLaughlin Jr.', 'sbernhard@example.com', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=nicholas26', '2009-11-17', '2026-04-21 12:54:20', 4422, 0, 0, 'zCxDwKGdAC', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(26, 'wtreutel', 'Kaya', 'Misael Spinka', 'jaime.dach@example.com', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=wtreutel', '2015-10-03', '2026-05-11 12:58:39', 3951, 0, 0, 'qaazEPrlye', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(27, 'sidney29', 'Chelsie', 'Alex Gusikowski Sr.', 'elena.pfeffer@example.net', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=sidney29', '2009-02-20', '2026-05-10 15:44:53', 1334, 0, 0, 'OLRHXZDwtx', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(28, 'laurence73', 'Johanna', 'Jeremy Gusikowski Jr.', 'rodriguez.mikel@example.org', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=laurence73', '1977-02-22', '2026-04-08 05:59:48', 307, 0, 0, 'Hh1lnIv5gL', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(29, 'kreiger.danyka', 'Eldred', 'Blanche Block', 'drake66@example.com', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=kreiger.danyka', '2022-10-18', '2025-09-01 19:43:30', 166, 1, 0, 'tlpkfkE7XI', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(30, 'fkuhic', 'Aleen', 'Kyleigh Brown II', 'cronin.bettie@example.net', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=fkuhic', '2017-12-25', '2025-11-02 01:04:40', 644, 0, 0, 'mbmdIHa73r', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(31, 'xbruen', 'Winnifred', 'Myrtice Collier V', 'jbotsford@example.net', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=xbruen', '2004-04-15', '2025-10-25 04:04:34', 3160, 0, 0, 'sLkUMkUlkO', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(32, 'randy20', 'Arlene', 'Hallie O\'Keefe', 'jalen.mayer@example.org', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=randy20', '1973-02-28', '2025-11-22 17:25:20', 4374, 0, 0, 'ipCueRfOXO', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(33, 'ella.heidenreich', 'Everett', 'Collin Thiel II', 'jalon83@example.net', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=ella.heidenreich', '2012-11-15', '2025-09-18 17:24:03', 1959, 1, 0, 'L0kPBFofAK', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(34, 'mafalda.johnston', 'Marjolaine', 'Calista Howell', 'abernathy.elliot@example.org', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=mafalda.johnston', '1975-03-28', '2026-04-25 10:39:19', 2341, 0, 0, 'Dct2GevjXd', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(35, 'terrence34', 'Carmella', 'Steve Schmitt', 'kuhic.fay@example.org', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=terrence34', '2005-08-29', '2026-05-05 04:53:42', 2260, 0, 0, 'wra8OxJWQc', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(36, 'deondre15', 'Carter', 'Jaquelin Stehr V', 'russel.felton@example.org', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=deondre15', '2021-04-29', '2025-10-17 13:22:49', 3500, 1, 0, 'Dq2gIZH4he', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(37, 'jannie20', 'Damian', 'Dr. Brennon Kuvalis', 'uriah40@example.net', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=jannie20', '2004-11-17', '2026-02-12 04:12:48', 3175, 1, 0, '5oeNnrrvDr', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(38, 'welch.katrina', 'Hollie', 'Ms. Florida Conn PhD', 'klocko.clovis@example.org', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=welch.katrina', '1978-01-27', '2026-01-19 04:41:00', 2674, 0, 0, 'PU5ihEnQYL', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(39, 'csteuber', 'Kristin', 'Patience Quitzon', 'melvina11@example.net', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=csteuber', '2010-12-30', '2026-04-24 23:34:04', 3987, 1, 0, 'AzsEhdF9af', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(40, 'ufeeney', 'Dell', 'Jana Bogan IV', 'marjolaine60@example.com', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=ufeeney', '2009-08-12', '2025-12-23 00:14:26', 802, 0, 0, 'admxrtwVgh', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(41, 'zachariah08', 'Evalyn', 'Precious Morar', 'buster.mccullough@example.net', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=zachariah08', '1992-02-02', '2026-04-17 09:03:40', 2010, 0, 0, '00yLQWBfyE', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(42, 'catalina.ondricka', 'Dolores', 'Gideon Ward DDS', 'habbott@example.org', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=catalina.ondricka', '2024-04-04', '2025-07-17 20:33:46', 867, 0, 0, 'Y042LiWV37', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(43, 'jamal58', 'Twila', 'Piper Vandervort', 'jesse45@example.net', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=jamal58', '1988-11-19', '2025-07-23 18:52:23', 1642, 0, 0, 'ICvAGT8eM6', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(44, 'jbraun', 'Minerva', 'August Hills', 'bruen.foster@example.net', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=jbraun', '1970-03-02', '2026-03-17 09:01:03', 1605, 0, 0, 'Aql1ltQtq1', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(45, 'brown.dannie', 'Jan', 'Christopher Wolff', 'lindgren.edd@example.org', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=brown.dannie', '2007-09-12', '2026-03-11 23:39:47', 745, 0, 0, 'VZtVpW1lGs', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(46, 'tdenesik', 'Lamar', 'Orville Terry', 'jed.wyman@example.com', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=tdenesik', '1974-12-21', '2026-01-01 09:35:26', 3598, 0, 0, 'vhgWZgm8XT', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(47, 'eondricka', 'Frederique', 'Lela Stroman', 'williamson.justen@example.com', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=eondricka', '1997-08-30', '2025-05-27 02:37:17', 1697, 1, 0, 'TfZRozBqwv', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(48, 'luther70', 'Jane', 'Dr. Bill Hayes I', 'hrunte@example.org', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=luther70', '2001-03-29', '2025-12-29 06:16:02', 4505, 0, 0, 'AoCd05q3wy', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(49, 'oceane.bins', 'Colt', 'Llewellyn Braun', 'fjohns@example.com', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=oceane.bins', '2024-08-12', '2025-10-25 15:15:52', 223, 0, 0, 'RVZAMuVSvi', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(50, 'vshields', 'Kayden', 'Ryder Spinka', 'qjohnston@example.org', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=vshields', '2024-11-13', '2026-01-22 08:40:07', 3270, 0, 0, '0IaitRRiJM', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(51, 'donnell.ondricka', 'Francisca', 'Marielle Von', 'becker.jamal@example.com', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=donnell.ondricka', '2017-03-27', '2025-06-14 11:03:27', 1141, 1, 0, 'bnOnuvd4HG', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(52, 'kirsten.howe', 'Hobart', 'Sidney Heathcote', 'dach.rose@example.org', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=kirsten.howe', '2024-01-12', '2025-07-11 04:16:21', 1765, 0, 0, 'ZuVT1WXEp8', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(53, 'lauren.bartoletti', 'Jules', 'Korey Carroll', 'eebert@example.com', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=lauren.bartoletti', '1990-11-05', '2025-10-20 16:55:21', 1040, 0, 0, 'TjQ0UiUs5u', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(54, 'lconn', 'Izabella', 'Rosetta Bartell V', 'eloy.romaguera@example.com', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=lconn', '1983-11-29', '2025-11-16 01:33:07', 3320, 1, 0, '1WQYcHXrTW', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(55, 'sjacobson', 'Diego', 'Dr. Otis Stamm PhD', 'estel35@example.org', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=sjacobson', '2013-03-06', '2025-11-08 10:15:18', 689, 0, 0, 'XGNLSoPsB9', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(56, 'andre.hickle', 'Aliya', 'Prof. Luisa Mertz', 'cturner@example.org', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=andre.hickle', '2010-09-22', '2025-06-18 07:20:54', 1182, 0, 0, 'Q9LVb7t35U', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(57, 'aglae00', 'Mark', 'Jillian Volkman', 'sofia.pouros@example.net', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=aglae00', '1970-03-10', '2025-06-09 12:40:32', 2985, 1, 0, 'jBRHTQ2HHp', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(58, 'nicklaus.hettinger', 'Elroy', 'Mr. Buck Keeling', 'ernser.rod@example.org', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=nicklaus.hettinger', '2022-02-14', '2025-06-09 15:09:12', 2801, 0, 0, '1oil1OFUF3', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(59, 'berry.mclaughlin', 'Jerad', 'Madison Schaden', 'ariane54@example.net', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=berry.mclaughlin', '1977-12-25', '2026-02-20 00:54:44', 4043, 0, 0, 'MoKmOllaFI', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(60, 'frederick.champlin', 'Emmitt', 'Monica Mante', 'orn.baron@example.com', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=frederick.champlin', '1970-04-28', '2026-03-18 09:52:59', 3422, 0, 0, '1B9U2apcuC', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(61, 'lsmitham', 'Jerod', 'Mr. Christopher Walsh V', 'gwendolyn21@example.net', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=lsmitham', '2025-03-13', '2025-07-22 13:19:39', 3307, 0, 0, '6MmJBswWKE', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL),
(62, 'deanna34', 'Andreane', 'Dr. Ariel Murazik I', 'schuster.dayton@example.com', '$2y$12$yQ1KV6nxSWEW0xDBDddGqeY2rhf1b8CibdjTn4lM2qVmEN2uoHoRe', 'https://api.dicebear.com/7.x/avataaars/svg?seed=deanna34', '1987-04-24', '2026-04-12 10:45:10', 822, 1, 0, '44x7jmawV1', '2026-05-12 00:21:26', '2026-05-12 00:21:26', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `friends_user_id_friend_id_unique` (`user_id`,`friend_id`),
  ADD KEY `friends_friend_id_foreign` (`friend_id`);

--
-- Indexes for table `jars`
--
ALTER TABLE `jars`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jars_created_by_foreign` (`created_by`),
  ADD KEY `jars_current_daily_quest_id_foreign` (`current_daily_quest_id`),
  ADD KEY `jars_previous_daily_quest_id_foreign` (`previous_daily_quest_id`);

--
-- Indexes for table `jar_comments`
--
ALTER TABLE `jar_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jar_comments_jar_id_foreign` (`jar_id`),
  ADD KEY `jar_comments_user_id_foreign` (`user_id`);

--
-- Indexes for table `jar_invitations`
--
ALTER TABLE `jar_invitations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `jar_invitations_jar_id_invitee_id_unique` (`jar_id`,`invitee_id`),
  ADD KEY `jar_invitations_inviter_id_foreign` (`inviter_id`),
  ADD KEY `jar_invitations_invitee_id_foreign` (`invitee_id`);

--
-- Indexes for table `jar_quests`
--
ALTER TABLE `jar_quests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jar_quests_jar_id_foreign` (`jar_id`),
  ADD KEY `jar_quests_created_by_foreign` (`created_by`);

--
-- Indexes for table `jar_users`
--
ALTER TABLE `jar_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `jar_users_jar_id_user_id_unique` (`jar_id`,`user_id`),
  ADD KEY `jar_users_user_id_foreign` (`user_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `likes_user_id_likeable_id_likeable_type_unique` (`user_id`,`likeable_id`,`likeable_type`),
  ADD KEY `likes_likeable_type_likeable_id_index` (`likeable_type`,`likeable_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `messages_sender_id_foreign` (`sender_id`),
  ADD KEY `messages_receiver_id_foreign` (`receiver_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `quest_completions`
--
ALTER TABLE `quest_completions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quest_completions_user_id_foreign` (`user_id`),
  ADD KEY `quest_completions_jar_quest_id_foreign` (`jar_quest_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `friends`
--
ALTER TABLE `friends`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jars`
--
ALTER TABLE `jars`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `jar_comments`
--
ALTER TABLE `jar_comments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jar_invitations`
--
ALTER TABLE `jar_invitations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jar_quests`
--
ALTER TABLE `jar_quests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT for table `jar_users`
--
ALTER TABLE `jar_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=196;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quest_completions`
--
ALTER TABLE `quest_completions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `friends`
--
ALTER TABLE `friends`
  ADD CONSTRAINT `friends_friend_id_foreign` FOREIGN KEY (`friend_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `friends_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jars`
--
ALTER TABLE `jars`
  ADD CONSTRAINT `jars_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `jars_current_daily_quest_id_foreign` FOREIGN KEY (`current_daily_quest_id`) REFERENCES `jar_quests` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `jars_previous_daily_quest_id_foreign` FOREIGN KEY (`previous_daily_quest_id`) REFERENCES `jar_quests` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `jar_comments`
--
ALTER TABLE `jar_comments`
  ADD CONSTRAINT `jar_comments_jar_id_foreign` FOREIGN KEY (`jar_id`) REFERENCES `jars` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jar_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jar_invitations`
--
ALTER TABLE `jar_invitations`
  ADD CONSTRAINT `jar_invitations_invitee_id_foreign` FOREIGN KEY (`invitee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jar_invitations_inviter_id_foreign` FOREIGN KEY (`inviter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jar_invitations_jar_id_foreign` FOREIGN KEY (`jar_id`) REFERENCES `jars` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jar_quests`
--
ALTER TABLE `jar_quests`
  ADD CONSTRAINT `jar_quests_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `jar_quests_jar_id_foreign` FOREIGN KEY (`jar_id`) REFERENCES `jars` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jar_users`
--
ALTER TABLE `jar_users`
  ADD CONSTRAINT `jar_users_jar_id_foreign` FOREIGN KEY (`jar_id`) REFERENCES `jars` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jar_users_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quest_completions`
--
ALTER TABLE `quest_completions`
  ADD CONSTRAINT `quest_completions_jar_quest_id_foreign` FOREIGN KEY (`jar_quest_id`) REFERENCES `jar_quests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quest_completions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

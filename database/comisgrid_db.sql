-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 16, 2026 at 06:11 AM
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
-- Database: `comisgrid_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `artworks`
--

CREATE TABLE `artworks` (
  `artwork_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `category` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `status` varchar(30) DEFAULT 'Available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `artworks`
--

INSERT INTO `artworks` (`artwork_id`, `user_id`, `title`, `category`, `description`, `price`, `image_path`, `status`, `created_at`) VALUES
(1, 3, 'test 1', 'Digital Art', 'banyo', 99999999.99, '../uploads/users/user_3/artworks/artwork_1778840071_2855.jpg', 'Available', '2026-05-15 10:14:31');

-- --------------------------------------------------------

--
-- Table structure for table `bookmarks`
--

CREATE TABLE `bookmarks` (
  `bookmark_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_members`
--

CREATE TABLE `chat_members` (
  `member_id` int(11) NOT NULL,
  `thread_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_members`
--

INSERT INTO `chat_members` (`member_id`, `thread_id`, `user_id`, `created_at`) VALUES
(1, 1, 3, '2026-05-15 15:23:48'),
(2, 1, 4, '2026-05-15 15:23:48'),
(3, 2, 3, '2026-05-15 20:19:14'),
(4, 2, 5, '2026-05-15 20:19:14');

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `message_id` int(11) NOT NULL,
  `thread_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `message_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`message_id`, `thread_id`, `sender_id`, `message_text`, `created_at`) VALUES
(1, 1, 3, 'palita ko ice', '2026-05-15 15:23:58'),
(2, 1, 3, 'oi', '2026-05-15 15:24:49'),
(3, 1, 4, 'saman bogo', '2026-05-15 15:25:02'),
(4, 2, 3, 'Hello', '2026-05-15 20:19:20'),
(5, 1, 4, 'hello', '2026-05-16 04:09:38');

-- --------------------------------------------------------

--
-- Table structure for table `chat_threads`
--

CREATE TABLE `chat_threads` (
  `thread_id` int(11) NOT NULL,
  `thread_type` varchar(20) DEFAULT 'direct',
  `thread_name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_threads`
--

INSERT INTO `chat_threads` (`thread_id`, `thread_type`, `thread_name`, `created_at`) VALUES
(1, 'direct', 'Direct Chat', '2026-05-15 15:23:48'),
(2, 'direct', 'Direct Chat', '2026-05-15 20:19:14');

-- --------------------------------------------------------

--
-- Table structure for table `company_profits`
--

CREATE TABLE `company_profits` (
  `profit_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_profits`
--

INSERT INTO `company_profits` (`profit_id`, `order_id`, `amount`, `created_at`) VALUES
(1, 1, 2.50, '2026-05-15 11:55:48'),
(2, 2, 5.00, '2026-05-15 14:06:56'),
(3, 3, 10.00, '2026-05-15 16:10:33');

-- --------------------------------------------------------

--
-- Table structure for table `follows`
--

CREATE TABLE `follows` (
  `follow_id` int(11) NOT NULL,
  `follower_id` int(11) NOT NULL,
  `following_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `follows`
--

INSERT INTO `follows` (`follow_id`, `follower_id`, `following_id`, `created_at`) VALUES
(3, 3, 4, '2026-05-15 18:02:32'),
(5, 3, 5, '2026-05-16 03:59:09');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `actor_id` int(11) DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `actor_id`, `type`, `message`, `link`, `is_read`, `created_at`) VALUES
(1, 4, 3, 'follow', '@ralphy followed you.', 'profile.php?user_id=3', 0, '2026-05-15 18:02:32'),
(2, 5, 3, 'follow', '@ralphy followed you.', 'profile.php?user_id=3', 0, '2026-05-15 20:19:06'),
(3, 5, 3, 'follow', '@ralphy followed you.', 'profile.php?user_id=3', 0, '2026-05-16 03:59:09');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `platform_fee` decimal(10,2) NOT NULL,
  `seller_earning` decimal(10,2) NOT NULL,
  `status` varchar(30) DEFAULT 'Paid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `buyer_id`, `seller_id`, `product_id`, `total_price`, `platform_fee`, `seller_earning`, `status`, `created_at`) VALUES
(1, 4, 3, 1, 50.00, 2.50, 47.50, 'Paid', '2026-05-15 11:55:48'),
(2, 4, 3, 2, 100.00, 5.00, 95.00, 'Paid', '2026-05-15 14:06:56'),
(3, 3, 4, 3, 200.00, 10.00, 190.00, 'Paid', '2026-05-15 16:10:33');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `category` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `cover_image` varchar(255) NOT NULL,
  `status` varchar(30) DEFAULT 'Available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `seller_id`, `title`, `category`, `description`, `price`, `cover_image`, `status`, `created_at`) VALUES
(1, 3, 'test 2', 'Digital Art', 'nice view', 50.00, '../uploads/users/user_3/artworks/product_1778845361_3348.jpg', 'Sold', '2026-05-15 11:42:41'),
(2, 3, 'test 3', 'Digital Art', 'testing', 100.00, '../uploads/users/user_3/products/product_1778852911_6323.png', 'Sold', '2026-05-15 13:48:31'),
(3, 4, 'test 4', 'Portrait', 'testing 4', 200.00, '../uploads/users/user_4/products/product_1778856933_2759.png', 'Sold', '2026-05-15 14:55:33'),
(4, 5, 'Demon Slayer Tanjiro', 'Anime', 'Kimetsu No Yaiba Anime', 615.00, '../uploads/users/user_5/products/product_1778875782_4395.jpeg', 'Available', '2026-05-15 20:09:42'),
(5, 5, 'Girl Smoking cool', 'Digital Art', 'Pretty cool girl smoking with good graphics design. Worth the price for your mobile or phone devices', 350.00, '../uploads/users/user_5/products/product_1778875909_1451.jpg', 'Available', '2026-05-15 20:11:49'),
(6, 5, 'Sunset', 'Digital Art', 'High Definition - Sunset and calm wallpaper', 416.00, '../uploads/users/user_5/products/product_1778875979_7628.jpg', 'Available', '2026-05-15 20:12:59'),
(7, 5, 'Romantic Sunset', 'Digital Art', 'HD - Romantice Sunset Couple', 623.00, '../uploads/users/user_5/products/product_1778876046_2898.jpg', 'Available', '2026-05-15 20:14:06'),
(8, 5, 'HD- Anime Village Girl', 'Anime', 'HD Wallpaper Anime Girl', 780.00, '../uploads/users/user_5/products/product_1778876091_2809.jpg', 'Available', '2026-05-15 20:14:51');

-- --------------------------------------------------------

--
-- Table structure for table `product_likes`
--

CREATE TABLE `product_likes` (
  `like_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT 5,
  `comment` text DEFAULT NULL,
  `likes` int(11) DEFAULT 0,
  `dislikes` int(11) DEFAULT 0,
  `seller_reply` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `buyer_id`, `seller_id`, `product_id`, `order_id`, `rating`, `comment`, `likes`, `dislikes`, `seller_reply`, `created_at`) VALUES
(1, 3, 4, 3, 3, 4, 'very good u are awsum', 0, 0, NULL, '2026-05-15 18:03:09');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `user_id`, `order_id`, `type`, `amount`, `description`, `created_at`) VALUES
(1, 4, 1, 'Purchase', -50.00, 'Purchased product: test 2', '2026-05-15 11:55:48'),
(2, 3, 1, 'Sale Earning', 47.50, 'Sold product: test 2', '2026-05-15 11:55:48'),
(3, 0, 1, 'Platform Fee', 2.50, 'Platform fee from product: test 2', '2026-05-15 11:55:48'),
(4, 4, 2, 'Purchase', -100.00, 'Purchased product: test 3', '2026-05-15 14:06:56'),
(5, 3, 2, 'Sale Earning', 95.00, 'Sold product: test 3', '2026-05-15 14:06:56'),
(6, 0, 2, 'Platform Fee', 5.00, 'Platform fee from product: test 3', '2026-05-15 14:06:56'),
(7, 3, NULL, 'Top Up', 100.00, 'Wallet top up via GCash simulation', '2026-05-15 14:23:51'),
(8, 3, NULL, 'Withdraw', -50.00, 'Wallet withdrawal to GCash simulation', '2026-05-15 14:23:57'),
(9, 3, NULL, 'Top Up', 100.00, 'Wallet top up via GCash simulation', '2026-05-15 16:10:10'),
(10, 3, 3, 'Purchase', -200.00, 'Purchased product: test 4', '2026-05-15 16:10:33'),
(11, 4, 3, 'Sale Earning', 190.00, 'Sold product: test 4', '2026-05-15 16:10:33'),
(12, 0, 3, 'Platform Fee', 10.00, 'Platform fee from product: test 4', '2026-05-15 16:10:33'),
(13, 5, NULL, 'Top Up', 780.00, 'Wallet top up via GCash simulation', '2026-05-15 20:07:49');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `bio` text DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT '../assets/images/default-profile.png',
  `balance` decimal(10,2) DEFAULT 0.00,
  `total_earned` decimal(10,2) DEFAULT 0.00,
  `total_spent` decimal(10,2) DEFAULT 0.00,
  `total_sales` int(11) DEFAULT 0,
  `profile_views` int(11) DEFAULT 0,
  `is_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `facebook_link` varchar(255) DEFAULT NULL,
  `instagram_link` varchar(255) DEFAULT NULL,
  `x_link` varchar(255) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `this_month_earned` decimal(10,2) DEFAULT 0.00,
  `member_since` timestamp NOT NULL DEFAULT current_timestamp(),
  `gcash_number` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `username`, `email`, `password`, `bio`, `profile_image`, `balance`, `total_earned`, `total_spent`, `total_sales`, `profile_views`, `is_verified`, `created_at`, `facebook_link`, `instagram_link`, `x_link`, `tags`, `this_month_earned`, `member_since`, `gcash_number`) VALUES
(3, 'Ralph Navidad', 'ralphy', 'ralphy12345@mail.com', '$2y$10$Al8cQi6uGcJre4yEMAHlYurpKAZepzgfiIVvBrg/JT1cGacR0BrI2', '', '../uploads/users/user_3/profile/profile_3.png', 92.50, 142.50, 200.00, 2, 0, 0, '2026-05-15 10:06:20', 'https://www.facebook.com/rnavidad16', 'https://www.instagram.com/ralph.navidad1/', 'https://www.x.com/RalphNavidad1', NULL, 142.50, '2026-05-15 10:22:28', '09392824457'),
(4, 'Marvin Bonghanoy', 'Marvy', 'marvy12345@mail.com', '$2y$10$WGc5kALnCciJPMydsaZtk.NOMD1CrNmwreO6A8rUiwoMBp10GR2mK', '', '../uploads/users/user_4/profile/profile_4.png', 10040.00, 190.00, 150.00, 1, 0, 0, '2026-05-15 11:39:12', '', '', '', NULL, 190.00, '2026-05-15 11:39:12', '09212626010'),
(5, 'Joseph Torrefalma', 'jtorz', 'joseph143@mail.com', '$2y$10$VnPGVI1JuClGKvfS2odDPO1oFmRsdl7RBwjL8ca0zQRoLpBL3ccvq', '', '../uploads/users/user_5/profile/profile_5.gif', 780.00, 0.00, 0.00, 0, 0, 0, '2026-05-15 20:07:29', 'https://www.facebook.com/jtorz.summit99', 'http://instagram.com/jtorzvanta/', '', NULL, 0.00, '2026-05-15 20:07:29', '09770757711');

-- --------------------------------------------------------

--
-- Table structure for table `withdrawals`
--

CREATE TABLE `withdrawals` (
  `withdrawal_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `status` varchar(30) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `artworks`
--
ALTER TABLE `artworks`
  ADD PRIMARY KEY (`artwork_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD PRIMARY KEY (`bookmark_id`),
  ADD UNIQUE KEY `unique_bookmark` (`user_id`,`product_id`);

--
-- Indexes for table `chat_members`
--
ALTER TABLE `chat_members`
  ADD PRIMARY KEY (`member_id`),
  ADD UNIQUE KEY `unique_member` (`thread_id`,`user_id`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `chat_threads`
--
ALTER TABLE `chat_threads`
  ADD PRIMARY KEY (`thread_id`);

--
-- Indexes for table `company_profits`
--
ALTER TABLE `company_profits`
  ADD PRIMARY KEY (`profit_id`);

--
-- Indexes for table `follows`
--
ALTER TABLE `follows`
  ADD PRIMARY KEY (`follow_id`),
  ADD UNIQUE KEY `unique_follow` (`follower_id`,`following_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- Indexes for table `product_likes`
--
ALTER TABLE `product_likes`
  ADD PRIMARY KEY (`like_id`),
  ADD UNIQUE KEY `unique_like` (`user_id`,`product_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD PRIMARY KEY (`withdrawal_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `artworks`
--
ALTER TABLE `artworks`
  MODIFY `artwork_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookmarks`
--
ALTER TABLE `bookmarks`
  MODIFY `bookmark_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_members`
--
ALTER TABLE `chat_members`
  MODIFY `member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `chat_threads`
--
ALTER TABLE `chat_threads`
  MODIFY `thread_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `company_profits`
--
ALTER TABLE `company_profits`
  MODIFY `profit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `follows`
--
ALTER TABLE `follows`
  MODIFY `follow_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `product_likes`
--
ALTER TABLE `product_likes`
  MODIFY `like_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `withdrawals`
--
ALTER TABLE `withdrawals`
  MODIFY `withdrawal_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `artworks`
--
ALTER TABLE `artworks`
  ADD CONSTRAINT `artworks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD CONSTRAINT `withdrawals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

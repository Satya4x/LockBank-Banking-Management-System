CREATE DATABASE IF NOT EXISTS `lockbank`;
USE `lockbank`;

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `admin_email` varchar(100) NOT NULL,
  `phone` varchar(15) DEFAULT '8691883158'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `admins` (`id`, `name`, `username`, `password`, `admin_email`, `phone`) VALUES
(5, 'Manager', 'satya_4x', '$2y$10$a69ma5JfQGDasPVqcEN/CeJHVZqRJUCEdNg8C0MvEpqOSTgXQM4UK', 'helpcenter@lockbank.com', '+1800 000 000');

CREATE TABLE `notices` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `notices` (`id`, `user_id`, `title`, `content`, `created_at`) VALUES
(28, NULL, 'Welcome to LOCKBANK!', 'Dear Customer,\r\n\r\nWe’re thrilled to welcome you to LOCKBANK – your trusted partner in secure and seamless digital banking. Your account has been successfully created, and you\'re now ready to explore our services.\r\n\r\nStay updated with your transactions, receive timely notifications, and manage your finances with ease.\r\n\r\nThank you for choosing LOCKBANK.\r\n\r\nWarm regards,  \r\n\r\n\r\nTeam LOCKBANK ', '2025-04-14 15:57:42');

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `account_number` bigint(12) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `transaction_type` enum('deposit','withdrawal','transfer_sent','transfer_received') NOT NULL,
  `recipient_account` bigint(12) DEFAULT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  `balance_after` decimal(15,2) NOT NULL DEFAULT 0.00,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `sender_account` bigint(12) DEFAULT NULL,
  `sender_name` varchar(100) DEFAULT NULL,
  `recipient_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- [All INSERT INTO transactions entries kept unchanged]

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `account_number` bigint(12) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `aadhar` bigint(12) NOT NULL,
  `pan_number` varchar(10) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `email` varchar(100) NOT NULL,
  `mobile` bigint(10) NOT NULL,
  `address` text NOT NULL,
  `pincode` int(6) NOT NULL,
  `dob` date NOT NULL,
  `account_type` enum('Saving','Current') NOT NULL,
  `occupation` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `lpin` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`id`, `account_number`, `full_name`, `aadhar`, `pan_number`, `gender`, `email`, `mobile`, `address`, `pincode`, `dob`, `account_type`, `occupation`, `password`, `photo`, `created_at`, `balance`, `lpin`) VALUES
(2, 224214954850, 'SUKHDEV PARIDA', 123412341234, 'ABCDP1234S', 'Male', 'dev@gmail.com', 9876543210, 'INDIRA NAGAR THANE (W)', 400604, '2004-11-19', 'Saving', 'DOCTOR', '$2y$10$upuZTLD1sZVydx6tQp/TJ.Dbc2YV23Y.QwywvdgThz5GKgmMOEe72', '1742313034_02.jfif', '2025-03-09 20:14:01', 946600.00, 12345),
(3, 451976661637, 'SATYAM JAISWAL', 492899672676, 'ABCDJ1234M', 'Male', 'sj04@gmail.com', 8649572655, 'INDIRA NAGAR', 400604, '2004-10-30', 'Saving', 'WEB DEVELOPER', '$2y$10$e7e4Xdw/YzA7XcmuXxHKVuzaQIUuU02FppWzFjF6VgdhPipfH20HK', '1742313019_01.jfif', '2025-03-15 05:35:38', 1390000.00, 54321),
(4, 288608635318, 'NIRAJ DHUM', 456789955555, 'ABCDD1234N', 'Male', 'nd@gmail.com', 8628482448, 'SITAPUR\r\n', 405245, '2000-09-25', 'Saving', 'BUSINESS ', '$2y$10$t65Q..CvoImEIcyzf6E5Ee4rhX27FIRHGUr3MvAuxA7Hoc53x14fO', '1742487641_03.jpg', '2025-03-20 10:50:41', 120000.00, 45678),
(5, 826886514045, 'SWETA  ANIL YADAV', 888866669999, 'ABCDY1234K', 'Female', 'sweta@gmail.com', 8845245454, 'SITA NAGAR', 400606, '2000-11-19', 'Saving', 'STUDENT', '$2y$10$QXExAeMKDq6t9xrrge.0HeloI5NEsgSaF.0up0bTmXyqn6.pbohW.', '1744639983_00.jpg', '2025-04-14 14:13:03', 0.00, 12345);

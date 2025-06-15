-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 14, 2025 at 10:07 AM
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
-- Database: creavote
--

-- --------------------------------------------------------

--
-- Table structure for table applications
--

CREATE TABLE applications (
  application_id int(11) NOT NULL,
  user_id varchar(26) NOT NULL,
  offer_id varchar(26) NOT NULL,
  applied_at timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table applications
--

INSERT INTO applications (application_id, user_id, offer_id, applied_at) VALUES
(1, 'usr684cbbf217144', 'off2', '2025-06-14 00:24:01'),
(2, 'usr684cbbf217144', 'off1', '2025-06-14 00:27:51'),
(3, 'usr684cc3422b4c0', 'off1', '2025-06-14 00:33:11');

-- --------------------------------------------------------

--
-- Table structure for table comments
--

CREATE TABLE comments (
  comment_id int(11) NOT NULL,
  design_id varchar(26) NOT NULL,
  user_id varchar(26) NOT NULL,
  comment text NOT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table comments
--

INSERT INTO comments (comment_id, design_id, user_id, comment, created_at) VALUES
(3, 'dsn2', 'usr684cc3422b4c0', 'good', '2025-06-14 01:25:42');

-- --------------------------------------------------------

--
-- Table structure for table designs
--

CREATE TABLE designs (
  design_id varchar(26) NOT NULL,
  offer_id varchar(26) DEFAULT NULL,
  designer_id varchar(26) DEFAULT NULL,
  file_url varchar(255) DEFAULT NULL,
  description text DEFAULT NULL,
  rating float DEFAULT 0,
  votes_count int(11) DEFAULT 0,
  submitted_at timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table designs
--

INSERT INTO designs (design_id, offer_id, designer_id, file_url, description, rating, votes_count, submitted_at) VALUES
('', 'off1', 'usr684cc3422b4c0', '/uploads/design_684cc384599ca4.54949645_groupeiki.png', '', 0, 0, '2025-06-14 00:34:12'),
('dsn1', 'off1', 'usr1', '/uploads/design1.png', 'Minimalist logo concept', 4.7, 7, '2025-06-13 23:54:00'),
('dsn2', 'off1', 'usr3', '/uploads/design2.png', 'Bold logo with tech vibe', 8, 1, '2025-06-13 23:54:00'),
('dsn3', 'off2', 'usr1', '/uploads/design3.png', 'Colorful event flyer', 4.9, 0, '2025-06-13 23:54:00');

-- --------------------------------------------------------

--
-- Table structure for table notifications
--

CREATE TABLE notifications (
  notification_id int(11) NOT NULL,
  user_id varchar(26) NOT NULL,
  type enum('saved','vote','message') NOT NULL,
  design_id varchar(26) DEFAULT NULL,
  message text DEFAULT NULL,
  is_read tinyint(1) DEFAULT 0,
  created_at timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table notifications
--

INSERT INTO notifications (notification_id, user_id, type, design_id, message, is_read, created_at) VALUES
(1, 'usr2', 'saved', 'dsn1', 'Your design was saved by Hafsa_Elmalki!', 0, '2025-06-14 00:07:21'),
(2, 'usr3', 'vote', 'dsn3', 'Your design received a new vote!', 0, '2025-06-14 00:07:21'),
(3, 'usr1', 'message', NULL, 'You have a new message from a client.', 0, '2025-06-14 00:07:21');

-- --------------------------------------------------------

--
-- Table structure for table offers
--

CREATE TABLE offers (
  offer_id varchar(26) NOT NULL,
  user_id varchar(26) DEFAULT NULL,
  offer_title varchar(50) NOT NULL,
  category varchar(25) DEFAULT NULL,
  description text DEFAULT NULL,
  tags varchar(25) DEFAULT NULL,
  offer_start date DEFAULT NULL,
  offer_end date DEFAULT NULL,
  offer_budget float NOT NULL,
  is_payed tinyint(1) DEFAULT 0,
  date_payment timestamp NULL DEFAULT NULL,
  amount float DEFAULT NULL,
  vote_status int(1) DEFAULT 0,
  total_votes int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table offers
--

INSERT INTO offers (offer_id, user_id, offer_title, category, description, tags, offer_start, offer_end, offer_budget, is_payed, date_payment, amount, vote_status, total_votes) VALUES
('off1', 'usr2', 'Logo Design for Startup', 'Logo', 'Design a modern logo for a tech startup.', 'logo,tech', '2025-06-01', '2025-06-30', 500, 1, '2025-06-01 09:00:00', 500, 1, 10),
('off2', 'usr2', 'Flyer for Event', 'Print', 'Create a flyer for our upcoming event.', 'flyer,event', '2025-06-10', '2025-06-20', 200, 0, NULL, NULL, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table prizes
--

CREATE TABLE prizes (
  prize_id int(11) NOT NULL,
  user_id varchar(26) DEFAULT NULL,
  offer_id varchar(26) DEFAULT NULL,
  amount float NOT NULL,
  type enum('designer','voter') NOT NULL,
  awarded_date timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table saves
--

CREATE TABLE saves (
  save_id int(11) NOT NULL,
  user_id varchar(26) DEFAULT NULL,
  design_id varchar(26) DEFAULT NULL,
  saved_at timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table saves
--

INSERT INTO saves (save_id, user_id, design_id, saved_at) VALUES
(1, 'usr1', 'dsn2', '2025-06-14 00:05:00'),
(2, 'usr2', 'dsn1', '2025-06-14 00:05:00'),
(3, 'usr3', 'dsn1', '2025-06-14 00:05:00'),
(4, 'usr3', 'dsn3', '2025-06-14 00:05:00');

-- --------------------------------------------------------

--
-- Table structure for table users
--

CREATE TABLE users (
  user_id varchar(26) NOT NULL,
  firstname varchar(35) NOT NULL,
  lastname varchar(35) NOT NULL,
  email varchar(255) NOT NULL,
  country varchar(56) DEFAULT NULL,
  phone varchar(15) DEFAULT NULL,
  username varchar(30) NOT NULL,
  password varchar(60) NOT NULL,
  type int(1) DEFAULT 0,
  role enum('client','designer','voter') NOT NULL,
  rating_accuracy float DEFAULT 0,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  profile_picture varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table users
--

INSERT INTO users (user_id, firstname, lastname, email, country, phone, username, password, type, role, rating_accuracy, created_at, profile_picture) VALUES
('usr1', 'Alice', 'Smith', 'alice@creavote.com', 'Morocco', '+212612345678', 'alice', '$2y$10$abcdefghijklmnopqrstuv', 0, 'designer', 4.8, '2025-06-13 23:54:00', NULL),
('usr2', 'Bob', 'Jones', 'bob@creavote.com', 'France', '+33612345678', 'bobj', '$2y$10$abcdefghijklmnopqrstuv', 0, 'client', 4.5, '2025-06-13 23:54:00', NULL),
('usr3', 'Carol', 'Lee', 'carol@creavote.com', 'USA', '+15551234567', 'caroll', '$2y$10$abcdefghijklmnopqrstuv', 0, 'voter', 4.9, '2025-06-13 23:54:00', NULL),
('usr684cbbf217144', 'mohamed', 'elachri', 'satayman41@gmail.com', 'Morocco', '707407425', 'mohamedelachri', '$2y$10$.YbtLlCZxCRCTcVAOm4KYe1AxpajpYvcpBgL6oK2fVPzIvVx1GjoK', 0, 'designer', 0, '2025-06-14 00:01:54', NULL),
('usr684cc3422b4c0', 'moha', 'ela', 'satayman@gmail.com', 'us', '+212707407425', 'mohaela', '$2y$10$cR.fpmpRbvT3XyaZh.xVFuyH7Ox2l900EYOe9uLN8ocXaZOm1Hipe', 0, 'designer', 0, '2025-06-14 00:33:06', '/uploads/profile_usr684cc3422b4c0_1749865605.jpg');

-- --------------------------------------------------------

--
-- Table structure for table votes
--

CREATE TABLE votes (
  vote_id int(11) NOT NULL,
  voter_id varchar(26) DEFAULT NULL,
  design_id varchar(26) DEFAULT NULL,
  rating int(11) DEFAULT NULL CHECK (rating between 1 and 10),
  vote_date timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table votes
--

INSERT INTO votes (vote_id, voter_id, design_id, rating, vote_date) VALUES
(1, 'usr684cc3422b4c0', 'dsn2', 8, '2025-06-14 00:50:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table applications
--
ALTER TABLE applications
  ADD PRIMARY KEY (application_id),
  ADD UNIQUE KEY unique_application (user_id,offer_id),
  ADD KEY offer_id (offer_id);

--
-- Indexes for table comments
--
ALTER TABLE comments
  ADD PRIMARY KEY (comment_id),
  ADD KEY design_id (design_id),
  ADD KEY user_id (user_id);

--
-- Indexes for table designs
--
ALTER TABLE designs
  ADD PRIMARY KEY (design_id),
  ADD KEY offer_id (offer_id),
  ADD KEY designer_id (designer_id);

--
-- Indexes for table notifications
--
ALTER TABLE notifications
  ADD PRIMARY KEY (notification_id),
  ADD KEY user_id (user_id),
  ADD KEY design_id (design_id);

--
-- Indexes for table offers
--
ALTER TABLE offers
  ADD PRIMARY KEY (offer_id),
  ADD KEY user_id (user_id);

--
-- Indexes for table prizes
--
ALTER TABLE prizes
  ADD PRIMARY KEY (prize_id),
  ADD KEY user_id (user_id),
  ADD KEY offer_id (offer_id);

--
-- Indexes for table saves
--
ALTER TABLE saves
  ADD PRIMARY KEY (save_id),
  ADD UNIQUE KEY unique_save (user_id,design_id),
  ADD KEY design_id (design_id);

--
-- Indexes for table users
--
ALTER TABLE users
  ADD PRIMARY KEY (user_id),
  ADD UNIQUE KEY email (email),
  ADD UNIQUE KEY username (username);

--
-- Indexes for table votes
--
ALTER TABLE votes
  ADD PRIMARY KEY (vote_id),
  ADD KEY voter_id (voter_id),
  ADD KEY design_id (design_id);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table applications
--
ALTER TABLE applications
  MODIFY application_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table comments
--
ALTER TABLE comments
  MODIFY comment_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table notifications
--
ALTER TABLE notifications
  MODIFY notification_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table prizes
--
ALTER TABLE prizes
  MODIFY prize_id int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table saves
--
ALTER TABLE saves
  MODIFY save_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table votes
--
ALTER TABLE votes
  MODIFY vote_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table applications
--
ALTER TABLE applications
  ADD CONSTRAINT applications_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE,
  ADD CONSTRAINT applications_ibfk_2 FOREIGN KEY (offer_id) REFERENCES offers (offer_id) ON DELETE CASCADE;

--
-- Constraints for table comments
--
ALTER TABLE comments
  ADD CONSTRAINT comments_ibfk_1 FOREIGN KEY (design_id) REFERENCES designs (design_id) ON DELETE CASCADE,
  ADD CONSTRAINT comments_ibfk_2 FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE;

--
-- Constraints for table designs
--
ALTER TABLE designs
  ADD CONSTRAINT designs_ibfk_1 FOREIGN KEY (offer_id) REFERENCES offers (offer_id) ON DELETE CASCADE,
  ADD CONSTRAINT designs_ibfk_2 FOREIGN KEY (designer_id) REFERENCES users (user_id) ON DELETE CASCADE;

--
-- Constraints for table notifications
--
ALTER TABLE notifications
  ADD CONSTRAINT notifications_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE,
  ADD CONSTRAINT notifications_ibfk_2 FOREIGN KEY (design_id) REFERENCES designs (design_id) ON DELETE SET NULL;

--
-- Constraints for table offers
--
ALTER TABLE offers
  ADD CONSTRAINT offers_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE SET NULL;

--
-- Constraints for table prizes
--
ALTER TABLE prizes
  ADD CONSTRAINT prizes_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE,
  ADD CONSTRAINT prizes_ibfk_2 FOREIGN KEY (offer_id) REFERENCES offers (offer_id) ON DELETE CASCADE;

--
-- Constraints for table saves
--
ALTER TABLE saves
  ADD CONSTRAINT saves_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE,
  ADD CONSTRAINT saves_ibfk_2 FOREIGN KEY (design_id) REFERENCES designs (design_id) ON DELETE CASCADE;

--
-- Constraints for table votes
--
ALTER TABLE votes
  ADD CONSTRAINT votes_ibfk_1 FOREIGN KEY (voter_id) REFERENCES users (user_id) ON DELETE CASCADE,
  ADD CONSTRAINT votes_ibfk_2 FOREIGN KEY (design_id) REFERENCES designs (design_id) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
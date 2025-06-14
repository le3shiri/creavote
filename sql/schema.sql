-- Creavote Database Schema
CREATE DATABASE IF NOT EXISTS creavote DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE creavote;

CREATE TABLE IF NOT EXISTS users (
    user_id VARCHAR(26) PRIMARY KEY,
    firstname VARCHAR(35) NOT NULL,
    lastname VARCHAR(35) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    country VARCHAR(56),
    phone VARCHAR(15),
    username VARCHAR(30) NOT NULL UNIQUE,
    password VARCHAR(60) NOT NULL,
    profile_picture VARCHAR(255) DEFAULT NULL,
    type INT(1) DEFAULT 0,
    role ENUM('client', 'designer', 'voter') NOT NULL,
    rating_accuracy FLOAT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS offers (
    offer_id VARCHAR(26) PRIMARY KEY,
    user_id VARCHAR(26),
    offer_title VARCHAR(50) NOT NULL,
    category VARCHAR(25),
    description TEXT,
    tags VARCHAR(25),
    offer_start DATE,
    offer_end DATE,
    offer_budget FLOAT NOT NULL,
    is_payed BOOLEAN DEFAULT 0,
    date_payment TIMESTAMP NULL,
    amount FLOAT,
    vote_status INT(1) DEFAULT 0,
    total_votes INT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS designs (
    design_id VARCHAR(26) PRIMARY KEY,
    offer_id VARCHAR(26),
    designer_id VARCHAR(26),
    file_url VARCHAR(255),
    description TEXT,
    rating FLOAT DEFAULT 0,
    votes_count INT DEFAULT 0,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (offer_id) REFERENCES offers(offer_id) ON DELETE CASCADE,

-- Saves (Favorites) Table
CREATE TABLE IF NOT EXISTS saves (
    save_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(26),
    design_id VARCHAR(26),
    saved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_save (user_id, design_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (design_id) REFERENCES designs(design_id) ON DELETE CASCADE
);

-- Applications Table
CREATE TABLE IF NOT EXISTS applications (
    application_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(26) NOT NULL,
    offer_id VARCHAR(26) NOT NULL,
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_application (user_id, offer_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (offer_id) REFERENCES offers(offer_id) ON DELETE CASCADE
);

-- Notifications Table
CREATE TABLE IF NOT EXISTS notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(26) NOT NULL,
    type ENUM('saved','vote','message') NOT NULL,
    design_id VARCHAR(26),
    message TEXT,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (design_id) REFERENCES designs(design_id) ON DELETE SET NULL
);

-- Sample Notifications
INSERT INTO notifications (user_id, type, design_id, message) VALUES
('usr2', 'saved', 'dsn1', 'Your design was saved by Hafsa_Elmalki!'),
('usr3', 'vote', 'dsn3', 'Your design received a new vote!'),
('usr1', 'message', NULL, 'You have a new message from a client.');


-- Sample Saves
INSERT INTO saves (user_id, design_id) VALUES
('usr1', 'dsn2'),
('usr2', 'dsn1'),
('usr3', 'dsn1'),
('usr3', 'dsn3');


-- Sample Data
-- Users
INSERT INTO users (user_id, firstname, lastname, email, country, phone, username, password, type, role, rating_accuracy)
VALUES
('usr1', 'Alice', 'Smith', 'alice@creavote.com', 'Morocco', '+212612345678', 'alice', '$2y$10$abcdefghijklmnopqrstuv', 0, 'designer', 4.8),
('usr2', 'Bob', 'Jones', 'bob@creavote.com', 'France', '+33612345678', 'bobj', '$2y$10$abcdefghijklmnopqrstuv', 0, 'client', 4.5),
('usr3', 'Carol', 'Lee', 'carol@creavote.com', 'USA', '+15551234567', 'caroll', '$2y$10$abcdefghijklmnopqrstuv', 0, 'voter', 4.9);

-- Offers
INSERT INTO offers (offer_id, user_id, offer_title, category, description, tags, offer_start, offer_end, offer_budget, is_payed, date_payment, amount, vote_status, total_votes)
VALUES
('off1', 'usr2', 'Logo Design for Startup', 'Logo', 'Design a modern logo for a tech startup.', 'logo,tech', '2025-06-01', '2025-06-30', 500, 1, '2025-06-01 10:00:00', 500, 1, 10),
('off2', 'usr2', 'Flyer for Event', 'Print', 'Create a flyer for our upcoming event.', 'flyer,event', '2025-06-10', '2025-06-20', 200, 0, NULL, NULL, 0, 0);

-- Designs
INSERT INTO designs (design_id, offer_id, designer_id, file_url, description, rating, votes_count)
VALUES
('dsn1', 'off1', 'usr1', '/uploads/design1.png', 'Minimalist logo concept', 4.7, 7),
('dsn2', 'off1', 'usr3', '/uploads/design2.png', 'Bold logo with tech vibe', 4.5, 3),
('dsn3', 'off2', 'usr1', '/uploads/design3.png', 'Colorful event flyer', 4.9, 0);

    FOREIGN KEY (designer_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS votes (
    vote_id INT AUTO_INCREMENT PRIMARY KEY,
    voter_id VARCHAR(26),
    design_id VARCHAR(26),
    rating INT CHECK (rating BETWEEN 1 AND 10),
    vote_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (voter_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (design_id) REFERENCES designs(design_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS prizes (
    prize_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(26),
    offer_id VARCHAR(26),
    amount FLOAT NOT NULL,
    type ENUM('designer', 'voter') NOT NULL,
    awarded_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (offer_id) REFERENCES offers(offer_id) ON DELETE CASCADE
);

-- Admin fees (5%) can be logged as a special user or with a note in prizes table.

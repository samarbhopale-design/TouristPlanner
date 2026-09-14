-- ========================================================================
-- Tourist Place Visit Planner - Database Setup Script
-- Compatible with MySQL 5.7+, MySQL 8.0+, MariaDB, and InfinityFree Hosting
-- ========================================================================

-- ------------------------------------------------------------------------
-- Database Selection:
-- On InfinityFree, select your database in the left sidebar of phpMyAdmin 
-- before importing. (Do NOT create database here to avoid MySQL Error 1044).
-- If running on local XAMPP/WAMP from scratch, you may uncomment these two lines:
-- ------------------------------------------------------------------------
-- CREATE DATABASE IF NOT EXISTS `tourist_planner` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE `tourist_planner`;

-- ------------------------------------------------------------------------
-- Table structure for table `users`
-- ------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------
-- Table structure for table `trips`
-- ------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `trips` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `destination` VARCHAR(100) NOT NULL,
    `travel_date` DATE NOT NULL,
    `persons` INT NOT NULL DEFAULT 1,
    `budget` DECIMAL(10,2) NOT NULL,
    `visitor_email` VARCHAR(100) NOT NULL,
    `notes` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_trips_user`
        FOREIGN KEY (`user_id`)
        REFERENCES `users`(`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------
-- Seed Data: Dummy User
-- Default Credentials:
--   Username: john_doe
--   Email:    john@example.com
--   Password: password123
-- Password hash generated using PHP password_hash('password123', PASSWORD_DEFAULT)
-- ------------------------------------------------------------------------
INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`)
VALUES (
    1,
    'john_doe',
    'john@example.com',
    '$2y$10$Vyxmhfaa4nau7IKyq.nFNeSQv00KeyKYYnhexuJPCVKCWXnT76/pK',
    NOW()
)
ON DUPLICATE KEY UPDATE `id`=`id`;

-- ------------------------------------------------------------------------
-- Seed Data: Raw Trip Data (10 Realistic Trips in INR Rupees) for john_doe
-- ------------------------------------------------------------------------
INSERT INTO `trips` (`user_id`, `destination`, `travel_date`, `persons`, `budget`, `visitor_email`, `notes`, `created_at`)
VALUES
(
    1,
    'Goa Beaches & Waterfalls, India',
    DATE_ADD(CURDATE(), INTERVAL 12 DAY),
    2,
    28500.00,
    'john@example.com',
    'North Goa beach hopping (Baga, Anjuna, Vagator), Dudhsagar Waterfalls day trek, sunset cruise on Mandovi river, and seafood at Fisherman\'s Wharf.',
    NOW()
),
(
    1,
    'Manali & Rohtang Pass, Himachal Pradesh',
    DATE_ADD(CURDATE(), INTERVAL 28 DAY),
    3,
    36000.00,
    'john@example.com',
    'Solang Valley paragliding, snow scooter adventure at Rohtang Pass, natural hot springs in Vashisht, and riverside café hopping in Old Manali.',
    NOW()
),
(
    1,
    'Jaipur & Udaipur, Rajasthan',
    DATE_ADD(CURDATE(), INTERVAL 45 DAY),
    4,
    45000.00,
    'john@example.com',
    'Amber Fort light and sound show, Hawa Mahal photography, sunset boat ride on Lake Pichola, and authentic Dal Baati Churma dinner at Chokhi Dhani.',
    NOW()
),
(
    1,
    'Munnar & Alleppey Backwaters, Kerala',
    DATE_ADD(CURDATE(), INTERVAL 65 DAY),
    2,
    42500.00,
    'john@example.com',
    'Tea garden plantation trail in Munnar, Eravikulam National Park Nilgiri Tahr safari, and overnight stay in a traditional Alleppey houseboat with local fish curry.',
    NOW()
),
(
    1,
    'Leh-Ladakh & Pangong Tso, India',
    DATE_ADD(CURDATE(), INTERVAL 90 DAY),
    2,
    68000.00,
    'john@example.com',
    'Motorbike expedition to Khardung La pass (17,582 ft), overnight camping at Pangong Tso lake, Magnetic Hill optical illusion, and Thiksey Monastery morning chant.',
    NOW()
),
(
    1,
    'Varanasi & Sarnath, Uttar Pradesh',
    DATE_ADD(CURDATE(), INTERVAL 115 DAY),
    1,
    22000.00,
    'john@example.com',
    'Subah-e-Banaras sunrise boat ride on the holy Ganges, mesmerizing evening Ganga Aarti at Dashashwamedh Ghat, Banarasi silk shopping, and visiting the historic Dhamek Stupa in Sarnath.',
    NOW()
),
(
    1,
    'Dubai & Abu Dhabi, UAE',
    DATE_ADD(CURDATE(), INTERVAL 140 DAY),
    2,
    115000.00,
    'john@example.com',
    'Burj Khalifa 148th floor At The Top observatory, evening desert safari with dune bashing and BBQ buffet, Sheikh Zayed Grand Mosque tour in Abu Dhabi, and Marina dinner cruise.',
    NOW()
),
(
    1,
    'Bali & Nusa Penida, Indonesia',
    DATE_ADD(CURDATE(), INTERVAL 170 DAY),
    2,
    92000.00,
    'john@example.com',
    'Sunrise hike to Mount Batur caldera, Sacred Monkey Forest sanctuary in Ubud, speed boat excursion to Kelingking T-Rex cliff on Nusa Penida, and relaxing beach clubs in Seminyak.',
    NOW()
),
(
    1,
    'Tokyo & Kyoto, Japan',
    DATE_ADD(CURDATE(), INTERVAL 205 DAY),
    1,
    185000.00,
    'john@example.com',
    'Walk through thousands of vermilion torii gates at Fushimi Inari, experience a traditional tea ceremony in Gion, ride the Shinkansen bullet train to Tokyo, and explore Shibuya crossing.',
    NOW()
),
(
    1,
    'Paris & Swiss Alps, Europe',
    DATE_ADD(CURDATE(), INTERVAL 240 DAY),
    2,
    265000.00,
    'john@example.com',
    'Louvre Museum and Eiffel Tower summit in Paris, scenic Glacier Express panoramic train through the Swiss Alps, and cable car up to the Matterhorn Glacier Paradise in Zermatt.',
    NOW()
);


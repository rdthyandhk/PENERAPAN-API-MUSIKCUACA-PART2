CREATE DATABASE IF NOT EXISTS dtzy_profile CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE dtzy_profile;

CREATE TABLE IF NOT EXISTS profile (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    bio TEXT NOT NULL,
    skills VARCHAR(255) NOT NULL,
    hobby VARCHAR(255) NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO profile (name, bio, skills, hobby) VALUES
('Radithya Andhika',
 'Saya adalah pelajar yang tertarik dengan teknologi, pemrograman, dan pengembangan website. Saya suka belajar hal baru, membuat project, dan mencoba berbagai teknologi.',
 'HTML, CSS, JavaScript, PHP, Bootstrap, CodeIgniter, MySQL',
 'Coding, membuat website, mencoba teknologi baru, dan mengembangkan project sederhana.');

CREATE TABLE IF NOT EXISTS search_history (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type ENUM('music','weather') NOT NULL,
    query_text VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_history_created_at (created_at)
);


CREATE TABLE IF NOT EXISTS favorite_music (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    deezer_id BIGINT UNSIGNED NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    artist VARCHAR(255) NOT NULL,
    album VARCHAR(255) DEFAULT '',
    cover VARCHAR(500) DEFAULT '',
    preview VARCHAR(500) DEFAULT '',
    deezer_link VARCHAR(500) DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Database creation script for trip_journey
CREATE DATABASE IF NOT EXISTS trip_journey;
USE trip_journey;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Journeys table
CREATE TABLE journeys (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Media table
CREATE TABLE media (
    id INT AUTO_INCREMENT PRIMARY KEY,
    journey_id INT NOT NULL,
    type ENUM('photo', 'video', 'gpx') NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    FOREIGN KEY (journey_id) REFERENCES journeys(id) ON DELETE CASCADE
);

-- Comments table
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    journey_id INT NOT NULL,
    user_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (journey_id) REFERENCES journeys(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Likes table
CREATE TABLE likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    journey_id INT NOT NULL,
    user_id INT NOT NULL,
    UNIQUE KEY unique_like (journey_id, user_id),
    FOREIGN KEY (journey_id) REFERENCES journeys(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Sample data
INSERT INTO users (username, password, email) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@example.com'), -- password: password
('demo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'demo@example.com'); -- password: password

INSERT INTO journeys (user_id, title, description) VALUES
(1, 'Trip to Paris', 'A wonderful journey through the city of lights.'),
(2, 'Mountain Hiking', 'An adventurous hike in the mountains.');

INSERT INTO media (journey_id, type, file_path) VALUES
(1, 'photo', 'uploads/paris1.jpg'),
(1, 'video', 'uploads/paris_video.mp4'),
(2, 'gpx', 'uploads/hike_track.gpx');

INSERT INTO comments (journey_id, user_id, comment) VALUES
(1, 2, 'Looks amazing!'),
(2, 1, 'Great adventure!');

INSERT INTO likes (journey_id, user_id) VALUES
(1, 2),
(2, 1);
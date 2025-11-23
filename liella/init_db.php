<?php
// AUTO DATABASE + TABLE CREATION + EVENT SEEDING

$host = "localhost";
$user = "root";
$pass = "";

// 1. CONNECT TO MYSQL (no DB yet)
$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. CREATE DATABASE IF NOT EXISTS
$conn->query("CREATE DATABASE IF NOT EXISTS liella_events_db");

// Switch to this database
$conn->select_db("liella_events_db");


// 3. CREATE TABLES IF NOT EXISTS
$conn->query("
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    profile_image VARCHAR(255) DEFAULT 'assets/default-profile.png'
)
");

$conn->query("
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(255),
    event_date DATE,
    venue VARCHAR(255),
    description TEXT
)
");

$conn->query("
CREATE TABLE IF NOT EXISTS event_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT,
    user_name VARCHAR(100),
    email VARCHAR(255),
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
)
");


// PRELOAD DEFAULT EVENTS IF TABLE IS EMPTY
$check = $conn->query("SELECT COUNT(*) AS total FROM events");
$row = $check->fetch_assoc();

if ($row['total'] == 0) {
    $conn->query("
    INSERT INTO events (event_name, event_date, venue, description) VALUES

    -- JANUARY / NEW YEAR BLOCK
    ('Liella! New Year Countdown Live 2026 ~Start Line Again~',
     '2025-12-31', 'Tokyo Dome',
     'A grand live celebrating the new year with Liella!.'
    ),

    -- FEBRUARY
    ('Liella! Fan Meet 2026',
     '2026-02-20', 'Shibuya Culture Center',
     'A warm and cozy fan meet with talks, games, and mini performances.'
    ),

    -- APRIL (3 EVENTS IN THE SAME MONTH)
    ('Liella! Spring Festival 2026 ~Blooming Dreams~',
     '2026-04-05', 'Osaka Hall',
     'A refreshing spring-themed concert full of bright, uplifting songs.'
    ),

    ('Liella! Cherry Blossom Mini Live 2026',
     '2026-04-12', 'Ueno Park Open Stage',
     'An outdoor live surrounded by sakura blossoms, featuring gentle acoustic songs.'
    ),

    ('Liella! Shiny Workshop ~Dance Focus~',
     '2026-04-26', 'Kanda Community Studio',
     'A special fan-participation workshop focusing on choreography and performance.'
    ),

    -- JUNE
    ('Liella! Acoustic Showcase 2026 ~Starry Notes~',
     '2026-06-12', 'Yokohama Pavilion',
     'A quiet, intimate acoustic performance with exclusive arrangements.'
    ),

    -- SEPTEMBER
    ('Liella! 6th Love Live! Tour ~Next Stage Radiance~',
     '2026-09-18', 'Nagoya Live Arena',
     'A powerful arena tour marking the next chapter of Liella!.'
    ),

    -- OCTOBER
    ('Liella! Halloween Special 2026 ~Twilight Parade~',
     '2026-10-30', 'Shinjuku Heritage Hall',
     'A themed Halloween show featuring costumes, skits, and limited-edition performances.'
    )
    ");
}


?>

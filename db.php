<?php
$host = '127.0.0.1';
$db_name = 'jotrorox_db';
$user = 'jotrorox'; // Change this to your MariaDB user
$pass = 'password';     // Change this to your MariaDB password

try {
    // Connect without database first to create it if it doesn't exist
    $db = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Create database
    $db->exec("CREATE DATABASE IF NOT EXISTS `$db_name`");
    $db->exec("USE `$db_name`");

    // Create guestbook table
    $db->exec("CREATE TABLE IF NOT EXISTS entries (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage() . "<br><br>Please check README.md for setup instructions.");
}
?>
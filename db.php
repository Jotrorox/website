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

    // Create blog posts table
    $db->exec("CREATE TABLE IF NOT EXISTS posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL UNIQUE,
        excerpt TEXT NOT NULL,
        content TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Insert a dummy post if the table is empty
    $stmt = $db->query("SELECT COUNT(*) FROM posts");
    if ($stmt->fetchColumn() == 0) {
        $db->exec("INSERT INTO posts (title, slug, excerpt, content) VALUES (
            'Welcome to my new blog!', 
            'welcome', 
            'This is the first post on my new custom PHP blog.', 
            'Hello world! I decided to build my own blog from scratch using PHP and MariaDB. It is simple, fast, and exactly what I need. Stay tuned for more posts about backend development, embedded systems, and Minecraft plugins!'
        )");
    }

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage() . "<br><br>Please check README.md for setup instructions.");
}
?>
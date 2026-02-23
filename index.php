<?php
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$allowed_pages = ['home', 'about', 'projects', 'contact', 'guestbook', 'blog', 'post', 'games', 'snake'];

if (!in_array($page, $allowed_pages)) {
    $page = 'home';
}

include 'layout.php';
?>
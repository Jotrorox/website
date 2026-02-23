<?php
require_once __DIR__ . '/../db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $db->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    echo "<h2>Post not found</h2><p>Sorry, the requested post does not exist.</p>";
    return;
}
?>

<div class="post-content">
    <a href="?page=blog" class="back-link">&larr; Back to Blog</a>
    <h2><?php echo htmlspecialchars($post['title']); ?></h2>
    <span class="date"><i class="ph ph-calendar-blank"></i> Posted on <?php echo date('F j, Y', strtotime($post['created_at'])); ?></span>
    
    <div class="post-body">
        <?php echo nl2br(htmlspecialchars($post['content'])); ?>
    </div>
</div>
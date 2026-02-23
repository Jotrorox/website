<?php
require_once __DIR__ . '/../db.php';

$stmt = $db->query("SELECT * FROM posts ORDER BY created_at DESC");
$posts = $stmt->fetchAll();
?>

<h2>Blog</h2>
<p>Thoughts, tutorials, and updates on my projects.</p>

<div class="blog-grid">
    <?php if (empty($posts)): ?>
        <p>No posts yet. Check back later!</p>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <div class="blog-card">
                <h3><a href="?page=post&id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></h3>
                <span class="date"><i class="ph ph-calendar-blank"></i> <?php echo date('M j, Y', strtotime($post['created_at'])); ?></span>
                <p><?php echo htmlspecialchars($post['excerpt']); ?></p>
                <a href="?page=post&id=<?php echo $post['id']; ?>" class="read-more">Read more &rarr;</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
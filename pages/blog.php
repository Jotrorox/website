<?php
require_once __DIR__ . '/../blog.php';

$posts = blog_get_all_posts();
?>

<h2>Blog</h2>

<div class="blog-grid">
    <?php if (empty($posts)): ?>
        <p>No posts yet. Check back later!</p>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <div class="blog-card">
                <h3><a href="?page=post&slug=<?php echo urlencode($post['slug']); ?>"><?php echo htmlspecialchars($post['title']); ?></a></h3>
                <span class="date"><i class="ph ph-calendar-blank"></i> <?php echo date('M j, Y', strtotime($post['created_at'])); ?></span>
                <p><?php echo htmlspecialchars($post['excerpt']); ?></p>
                <a href="?page=post&slug=<?php echo urlencode($post['slug']); ?>" class="read-more">Read more &rarr;</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
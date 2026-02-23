<?php
require_once __DIR__ . '/../blog.php';

$slug = isset($_GET['slug']) ? trim((string) $_GET['slug']) : '';
$post = $slug !== '' ? blog_get_post_by_slug($slug) : null;

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
        <?php echo $post['content_html']; ?>
    </div>
</div>
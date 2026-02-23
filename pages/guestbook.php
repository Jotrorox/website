<?php
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name']) && isset($_POST['message'])) {
    $name = htmlspecialchars(trim($_POST['name']));
    $message = htmlspecialchars(trim($_POST['message']));
    
    if (!empty($name) && !empty($message)) {
        $stmt = $db->prepare("INSERT INTO entries (name, message) VALUES (:name, :message)");
        $stmt->execute([':name' => $name, ':message' => $message]);
        header("Location: ?page=guestbook");
        exit;
    }
}

$stmt = $db->query("SELECT * FROM entries ORDER BY created_at DESC LIMIT 50");
$entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Guestbook</h2>

<div class="guestbook-layout">
    <div class="guestbook-form-container">
        <form method="POST" action="?page=guestbook" class="guestbook-form">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required placeholder="Your name or alias">
            </div>
            <div class="form-group">
                <label for="message">Message:</label>
                <textarea id="message" name="message" required placeholder="What's on your mind?"></textarea>
            </div>
            <button type="submit" class="btn">Sign Guestbook</button>
        </form>
    </div>

    <div class="guestbook-entries">
        <?php if (empty($entries)): ?>
            <p class="no-entries">No entries yet. Be the first!</p>
        <?php else: ?>
            <?php foreach ($entries as $entry): ?>
                <div class="entry">
                    <div class="entry-header">
                        <strong><?php echo $entry['name']; ?></strong>
                        <span class="date"><?php echo date('M j, Y H:i', strtotime($entry['created_at'])); ?></span>
                    </div>
                    <div class="entry-body">
                        <?php echo nl2br($entry['message']); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
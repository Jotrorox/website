<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>jotrorox | <?php echo ucfirst($page); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&family=Pixelify+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
    <div class="site-wrapper">
        <header class="site-header">
            <div class="logo">
                <h1>jotrorox</h1>
                <span class="subtitle">Backend Developer & Embedded Enthusiast</span>
            </div>
        </header>

        <div class="main-container">
            <aside class="sidebar-left">
                <nav class="main-nav">
                    <a href="?page=home" class="<?php echo $page == 'home' ? 'active' : ''; ?>">
                        <i class="ph ph-house icon"></i> Home
                    </a>
                    <a href="?page=about" class="<?php echo $page == 'about' ? 'active' : ''; ?>">
                        <i class="ph ph-user icon"></i> About
                    </a>
                    <a href="?page=projects" class="<?php echo $page == 'projects' ? 'active' : ''; ?>">
                        <i class="ph ph-rocket-launch icon"></i> Projects
                    </a>
                    <a href="?page=guestbook" class="<?php echo $page == 'guestbook' ? 'active' : ''; ?>">
                        <i class="ph ph-book-open-text icon"></i> Guestbook
                    </a>
                    <a href="?page=contact" class="<?php echo $page == 'contact' ? 'active' : ''; ?>">
                        <i class="ph ph-mailbox icon"></i> Contact
                    </a>
                </nav>
            </aside>

            <main class="content-area">
                <div class="content-inner">
                    <?php include "pages/{$page}.php"; ?>
                </div>
            </main>

            <aside class="sidebar-right">
                <div class="widget info-widget">
                    <h3>Status Info</h3>
                    <p>Currently doing my Abitur in Germany.</p>
                    <p><strong>Open to hire & projects!</strong></p>
                </div>
                <div class="widget news-widget">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="https://github.com/Jotrorox" target="_blank">GitHub</a></li>
                        <li><a href="https://discord.jotrorox.com" target="_blank">Discord</a></li>
                        <li><a href="https://fosstodon.org/@Jotrorox" target="_blank">Fosstodon</a></li>
                        <li><a href="https://x.com/jotrorox" target="_blank">Twitter/X</a></li>
                    </ul>
                </div>
                <div class="widget decorative-widget">
                    <div class="pixel-art-placeholder">
                        <i class="ph ph-game-controller"></i>
                        <i class="ph ph-cpu"></i>
                    </div>
                </div>
            </aside>
        </div>

        <footer class="site-footer">
            <div class="badges">
                <span class="badge">PHP Powered</span>
                <span class="badge">Indie Web</span>
                <span class="badge">Made with <i class="ph-fill ph-heart" style="color: #e74c3c;"></i></span>
            </div>
        </footer>
    </div>
</body>
</html>
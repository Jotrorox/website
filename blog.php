<?php

function blog_posts_dir(): string
{
    return __DIR__ . '/content/posts';
}

function blog_parse_frontmatter(string $contents): array
{
    $metadata = [];
    $body = $contents;

    if (preg_match('/\A---\R(.*?)\R---\R?/s', $contents, $matches)) {
        $frontmatter = $matches[1];
        $body = substr($contents, strlen($matches[0]));

        foreach (preg_split('/\R/', $frontmatter) as $line) {
            if (!preg_match('/^([A-Za-z0-9_\-]+)\s*:\s*(.*)$/', trim($line), $parts)) {
                continue;
            }

            $key = strtolower(trim($parts[1]));
            $value = trim($parts[2]);
            $metadata[$key] = $value;
        }
    }

    return [$metadata, $body];
}

function blog_markdown_inline(string $text): string
{
    $escaped = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

    $escaped = preg_replace_callback('/`([^`]+)`/', function ($matches) {
        return '<code>' . $matches[1] . '</code>';
    }, $escaped);

    $escaped = preg_replace_callback('/\[([^\]]+)\]\(([^\s)]+)\)/', function ($matches) {
        $label = $matches[1];
        $url = trim($matches[2]);

        if (!preg_match('/^(?:https?:|mailto:|\/|#|\.\/|\.\.\/)/i', $url)) {
            return $label;
        }

        $safeUrl = htmlspecialchars($url, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        return '<a href="' . $safeUrl . '" target="_blank" rel="noopener noreferrer">' . $label . '</a>';
    }, $escaped);

    $escaped = preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', $escaped);
    $escaped = preg_replace('/~~([^~]+)~~/', '<del>$1</del>', $escaped);
    $escaped = preg_replace('/\*([^*]+)\*/', '<em>$1</em>', $escaped);

    return $escaped;
}

function blog_markdown_to_html(string $markdown): string
{
    $markdown = str_replace(["\r\n", "\r"], "\n", trim($markdown));
    if ($markdown === '') {
        return '';
    }

    $lines = explode("\n", $markdown);
    $html = [];
    $listType = null;
    $inCodeBlock = false;
    $codeLanguage = '';
    $codeLines = [];
    $paragraphLines = [];
    $inBlockquote = false;

    $flushParagraph = function () use (&$paragraphLines, &$html) {
        if (empty($paragraphLines)) {
            return;
        }

        $parts = array_map('blog_markdown_inline', $paragraphLines);
        $html[] = '<p>' . implode('<br>', $parts) . '</p>';
        $paragraphLines = [];
    };

    $closeList = function () use (&$listType, &$html) {
        if ($listType !== null) {
            $html[] = '</' . $listType . '>';
            $listType = null;
        }
    };

    $closeBlockquote = function () use (&$inBlockquote, &$html) {
        if ($inBlockquote) {
            $html[] = '</blockquote>';
            $inBlockquote = false;
        }
    };

    foreach ($lines as $line) {
        if (preg_match('/^```\s*([A-Za-z0-9_+\-]*)\s*$/', $line, $fenceMatches)) {
            $flushParagraph();
            $closeList();
            $closeBlockquote();

            if (!$inCodeBlock) {
                $inCodeBlock = true;
                $codeLines = [];
                $codeLanguage = strtolower($fenceMatches[1] ?? '');
            } else {
                $classAttribute = $codeLanguage !== ''
                    ? ' class="language-' . htmlspecialchars($codeLanguage, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '"'
                    : '';
                $html[] = '<pre><code' . $classAttribute . '>' . htmlspecialchars(implode("\n", $codeLines), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</code></pre>';
                $inCodeBlock = false;
                $codeLanguage = '';
                $codeLines = [];
            }
            continue;
        }

        if ($inCodeBlock) {
            $codeLines[] = $line;
            continue;
        }

        if (trim($line) === '') {
            $flushParagraph();
            $closeList();
            $closeBlockquote();
            continue;
        }

        if (preg_match('/^(#{1,6})\s+(.+)$/', $line, $matches)) {
            $flushParagraph();
            $closeList();
            $closeBlockquote();

            $level = strlen($matches[1]);
            $html[] = '<h' . $level . '>' . blog_markdown_inline(trim($matches[2])) . '</h' . $level . '>';
            continue;
        }

        if (preg_match('/^[-*]\s+(.+)$/', $line, $matches)) {
            $flushParagraph();
            $closeBlockquote();
            if ($listType !== 'ul') {
                $closeList();
                $html[] = '<ul>';
                $listType = 'ul';
            }

            $html[] = '<li>' . blog_markdown_inline(trim($matches[1])) . '</li>';
            continue;
        }

        if (preg_match('/^\d+\.\s+(.+)$/', $line, $matches)) {
            $flushParagraph();
            $closeBlockquote();
            if ($listType !== 'ol') {
                $closeList();
                $html[] = '<ol>';
                $listType = 'ol';
            }

            $html[] = '<li>' . blog_markdown_inline(trim($matches[1])) . '</li>';
            continue;
        }

        if (preg_match('/^>\s?(.*)$/', $line, $matches)) {
            $flushParagraph();
            $closeList();
            if (!$inBlockquote) {
                $html[] = '<blockquote>';
                $inBlockquote = true;
            }

            $quoteLine = trim($matches[1]);
            if ($quoteLine !== '') {
                $html[] = '<p>' . blog_markdown_inline($quoteLine) . '</p>';
            }
            continue;
        }

        $closeBlockquote();
        $closeList();

        $paragraphLines[] = trim($line);
    }

    if ($inCodeBlock) {
        $classAttribute = $codeLanguage !== ''
            ? ' class="language-' . htmlspecialchars($codeLanguage, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '"'
            : '';
        $html[] = '<pre><code' . $classAttribute . '>' . htmlspecialchars(implode("\n", $codeLines), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</code></pre>';
    }

    $flushParagraph();
    $closeList();
    $closeBlockquote();

    return implode("\n", $html);
}

function blog_excerpt_from_markdown(string $markdown, int $maxLength = 180): string
{
    $text = preg_replace('/```[\s\S]*?```/', '', $markdown);
    $text = preg_replace('/#{1,6}\s*/', '', $text);
    $text = preg_replace('/\[([^\]]+)\]\(([^\s)]+)\)/', '$1', $text);
    $text = preg_replace('/[*_`>-]/', '', $text);
    $text = trim(preg_replace('/\s+/', ' ', (string) $text));

    if ($text === '') {
        return '';
    }

    if (mb_strlen($text) <= $maxLength) {
        return $text;
    }

    return rtrim(mb_substr($text, 0, $maxLength - 1)) . '…';
}

function blog_load_posts(): array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }

    $dir = blog_posts_dir();
    if (!is_dir($dir)) {
        $cache = [];
        return $cache;
    }

    $files = glob($dir . '/*.md') ?: [];
    $posts = [];

    foreach ($files as $file) {
        $contents = file_get_contents($file);
        if ($contents === false) {
            continue;
        }

        [$metadata, $body] = blog_parse_frontmatter($contents);

        $title = $metadata['title'] ?? '';
        $slug = $metadata['slug'] ?? '';
        $createdAt = $metadata['created_at'] ?? '';
        $excerpt = $metadata['excerpt'] ?? '';
        $draft = strtolower($metadata['draft'] ?? 'false');

        if ($draft === 'true' || $draft === '1' || $draft === 'yes') {
            continue;
        }

        if ($title === '' || $slug === '') {
            continue;
        }

        $timestamp = strtotime($createdAt);
        if ($timestamp === false) {
            $filename = basename($file, '.md');
            if (preg_match('/^(\d{4}-\d{2}-\d{2})-/', $filename, $matches)) {
                $timestamp = strtotime($matches[1]);
                $createdAt = $matches[1];
            }
        }

        if ($timestamp === false) {
            continue;
        }

        if ($excerpt === '') {
            $excerpt = blog_excerpt_from_markdown($body);
        }

        $posts[] = [
            'title' => $title,
            'slug' => $slug,
            'excerpt' => $excerpt,
            'content_html' => blog_markdown_to_html($body),
            'created_at' => date('Y-m-d H:i:s', $timestamp),
            'timestamp' => $timestamp,
        ];
    }

    usort($posts, function ($a, $b) {
        return $b['timestamp'] <=> $a['timestamp'];
    });

    $cache = $posts;
    return $cache;
}

function blog_get_all_posts(): array
{
    return blog_load_posts();
}

function blog_get_post_by_slug(string $slug): ?array
{
    foreach (blog_load_posts() as $post) {
        if ($post['slug'] === $slug) {
            return $post;
        }
    }

    return null;
}

?>
<?php
header('Content-Type: application/xml; charset=UTF-8');
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/docs.php';

$pages = [
    '/',
    '/?page=data-analysis', // legacy parameter pages retained
    '/?page=chart',
    '/?page=database',
    '/?page=project_list',
    '/?page=article_list',
    '/?page=github',
    '/?page=joke',
    '/?page=search'
];

function listMarkdown($dir, $base = '') {
    $items = [];
    foreach (scandir($dir) as $f) {
        if ($f === '.' || $f === '..') continue;
        $path = $dir . '/' . $f;
        if (is_dir($path)) {
            $items = array_merge($items, listMarkdown($path, $base . $f . '/'));
        } elseif (pathinfo($path, PATHINFO_EXTENSION) === 'md') {
            $items[] = $base . $f;
        }
    }
    return $items;
}

$markdownDir = __DIR__ . '/assets/markdown';
$docFiles = is_dir($markdownDir) ? listMarkdown($markdownDir) : [];
$urls = [];
$now = date('c');
foreach ($pages as $p) {
    $urls[] = [ 'loc' => canonical_url($p), 'lastmod' => $now, 'changefreq' => 'weekly', 'priority' => '0.8' ];
}
foreach ($docFiles as $rel) {
    $pretty = doc_pretty_url($rel);
    $urls[] = [ 'loc' => canonical_url($pretty), 'lastmod' => $now, 'changefreq' => 'monthly', 'priority' => '0.55' ];
}

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($urls as $u): ?>
  <url>
    <loc><?= htmlspecialchars($u['loc']) ?></loc>
    <lastmod><?= $u['lastmod'] ?></lastmod>
    <changefreq><?= $u['changefreq'] ?></changefreq>
    <priority><?= $u['priority'] ?></priority>
  </url>
<?php endforeach; ?>
</urlset>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/docs.php';

// Handle path parameter from JavaScript redirect fallback
if (isset($_GET['path'])) {
    $_SERVER['REQUEST_URI'] = $_GET['path'] . (isset($_GET['query']) ? '?' . $_GET['query'] : '');
}

$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$requestPath = preg_replace('#/+#','/',$requestPath);

$page = $_GET['page'] ?? 'document_view';

// Fallback redirect: if legacy ?file= is present, redirect to canonical /doc/ slug
if (isset($_GET['file'])) {
    $legacy = ltrim($_GET['file'], '/');
    if (str_ends_with($legacy, '.md')) { $legacyNoExt = substr($legacy, 0, -3); } else { $legacyNoExt = $legacy; }
    $pretty = doc_pretty_url($legacyNoExt);
    $currPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
    if ($pretty !== $currPath) {
        header('Location: ' . $pretty, true, 301);
        exit;
    }
}

if (preg_match('#^/doc(?:/(.*))?$#i', $requestPath, $m)) {
    $slugPath = $m[1] ?? '';
    if ($slugPath === '') {
        $page = 'document_view';
    } else {
        // Fully decode any legacy double/triple-encoded URLs
        $tmp = $slugPath;
        while (strpos($tmp, '%') !== false) {
            $prev = $tmp;
            $tmp = rawurldecode($tmp);
            if ($prev === $tmp) break; // prevent infinite loop
        }
        // Convert to canonical slug format (spaces -> hyphens, lowercase)
        $segments = explode('/', $tmp);
        $slugSegments = array_map('doc_slugify_segment', $segments);
        $canonicalSlugPath = implode('/', $slugSegments);
        
        $fileRel = doc_lookup_file_by_slug($canonicalSlugPath);
        if ($fileRel) {
            // Determine canonical slug from file map
            $noExt = preg_replace('/\.md$/i','',$fileRel);
            global $DOCS_FILE_TO_SLUG; // use existing map
            $canonicalSlug = $DOCS_FILE_TO_SLUG[$noExt] ?? $canonicalSlugPath;
            $canonicalUrl = '/doc/' . $canonicalSlug;
            if ($canonicalUrl !== $requestPath) {
                header('Location: ' . $canonicalUrl, true, 301);
                exit;
            }
            $_GET['file'] = $fileRel;
            $page = 'document_view';
            $candidatePath = __DIR__ . '/assets/markdown/' . $fileRel;
            if (is_file($candidatePath)) {
                $lines = @file($candidatePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
                foreach ($lines as $ln) { if (preg_match('/^#\s+(.+)/', $ln, $hm)) { $docHeading = trim($hm[1]); break; } }
                if (!empty($docHeading)) {
                    $dynamicDocTitle = $docHeading . ' | ' . PHPSPARK_SHORT;
                    $raw = file_get_contents($candidatePath);
                    $clean = preg_replace('/[#>*`_\-]+/', ' ', $raw);
                    $clean = preg_replace('/\s+/', ' ', $clean);
                    $metaDescription = trim(substr($clean, 0, 160));
                }
                // Build breadcrumb segments for doc path
                $breadcrumbs = [ ['name' => 'Home', 'url' => canonical_url('/') ] ];
                $parts = explode('/', $canonicalSlug);
                $acc = '';
                foreach ($parts as $idx => $seg) {
                    $acc .= ($idx?'/':'') . $seg;
                    $label = ucwords(str_replace('-', ' ', $seg));
                    $breadcrumbs[] = ['name' => $label, 'url' => canonical_url('/doc/' . $acc)];
                }
            }
        }
    }
}

$validPages = [ 'document_view','data-analysis','database','project_list','article_list','github','joke','search','404' ];
if (!in_array($page, $validPages)) { $page = '404'; }

$pageTitles = [
    'document_view' => 'Document Viewer',
    'data-analysis' => 'CSV File Analysis',
    'database' => 'Database CRUD Operations',
    'project_list' => 'Projects List',
    'article_list' => 'Articles List',
    'github' => 'GitHub Repository Info',
    'joke' => 'Random Jokes',
    'search' => 'Search Documents',
    '404' => 'Page Not Found'
];

$pageTitle = $pageTitles[$page] . ' | ' . PHPSPARK_SHORT;
if (!empty($dynamicDocTitle)) { $pageTitle = $dynamicDocTitle; }

if (!isset($metaDescription)) {
    switch ($page) {
        case 'document_view': $metaDescription = 'Browse and render markdown documentation with the PHPDocSpark viewer and search the knowledge base.'; break;
        case 'data-analysis': $metaDescription = 'Analyze CSV datasets interactively with profiling, statistics, and visualizations using PHPDocSpark.'; break;
        case 'database': $metaDescription = 'Demonstration of SQLite CRUD operations with secure PHP patterns in PHPDocSpark.'; break;
        case 'project_list': $metaDescription = 'Project portfolio and examples curated by Mark Hazleton within the PHPDocSpark platform.'; break;
        case 'article_list': $metaDescription = 'Technical articles and insights from Mark Hazleton\'s RSS feed, covering software development, Azure, and project management.'; break;
        case 'github': $metaDescription = 'Live GitHub repository insights, commits, and contributors integrated into PHPDocSpark.'; break;
        case 'joke': $metaDescription = 'Sample external API integration (JokeAPI) demonstrating JSON fetch and display in PHPDocSpark.'; break;
        case 'search': $metaDescription = 'Full-text search across all markdown documentation within PHPDocSpark by Mark Hazleton.'; break;
        default: $metaDescription = $DEFAULT_META_DESCRIPTION; break;
    }
}

ob_start();
include 'pages/' . $page . '.php';
$pageContent = ob_get_clean();
include 'layout.php';
?>
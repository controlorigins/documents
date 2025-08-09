<?php
// SEO helper include. Requires config loaded (index.php loads config before layout)
if (!function_exists('canonical_url')) {
    require_once __DIR__ . '/config.php';
}

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$canonicalPath = preg_replace('/([&?])PHPSESSID=[^&]+(&|$)/i', '$1', $requestUri);
$canonicalPath = rtrim($canonicalPath, '&?');
$canonicalUrl = canonical_url($canonicalPath);

$breadcrumbs = $breadcrumbs ?? [];
if (empty($breadcrumbs)) {
    $breadcrumbs = [ ['name' => 'Home', 'url' => canonical_url('/') ] ];
    $pageParam = $_GET['page'] ?? 'document_view';
    $fileParam = $_GET['file'] ?? null;
    if ($pageParam && $pageParam !== 'document_view') {
        $breadcrumbs[] = ['name' => ucwords(str_replace(['-', '_'], ' ', $pageParam)), 'url' => $canonicalUrl];
    } elseif ($fileParam) {
        $breadcrumbs[] = ['name' => 'Document', 'url' => $canonicalUrl];
    } else {
        $breadcrumbs[0]['url'] = $canonicalUrl;
    }
    if ($fileParam) {
        $baseName = pathinfo(urldecode($fileParam), PATHINFO_FILENAME);
        $breadcrumbs[] = ['name' => $baseName, 'url' => $canonicalUrl];
    }
}

$metaDescription = $metaDescription ?? $DEFAULT_META_DESCRIPTION;
$socialImage = canonical_url('/assets/images/fallback-social.png');
if (!file_exists(__DIR__ . '/../assets/images/fallback-social.png')) {
    $socialImage = canonical_url('/favicon.ico');
}

$ldBreadcrumbItems = [];
foreach ($breadcrumbs as $i => $crumb) {
    $ldBreadcrumbItems[] = [
        '@type' => 'ListItem',
        'position' => $i + 1,
        'name' => $crumb['name'],
        'item' => $crumb['url']
    ];
}

$structuredData = [
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'Person',
            'name' => PHPSPARK_AUTHOR,
            'url' => PHPSPARK_AUTHOR_URL,
            'sameAs' => [
                'https://github.com/controlorigins',
                'https://www.linkedin.com/in/markhazleton'
            ]
        ],
        [
            '@type' => 'WebSite',
            'name' => PHPSPARK_SHORT,
            'url' => canonical_url('/'),
            'publisher' => [ '@type' => 'Person', 'name' => PHPSPARK_AUTHOR ],
            'description' => $metaDescription
        ],
        [
            '@type' => 'BreadcrumbList',
            'itemListElement' => $ldBreadcrumbItems
        ]
    ]
];
?>
    <link rel="canonical" href="<?= e($canonicalUrl) ?>">
    <meta name="description" content="<?= e($metaDescription) ?>">
    <meta name="author" content="<?= e(PHPSPARK_AUTHOR) ?>">
    <meta name="robots" content="index,follow">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= e($pageTitle) ?>">
    <meta property="og:description" content="<?= e($metaDescription) ?>">
    <meta property="og:url" content="<?= e($canonicalUrl) ?>">
    <meta property="og:site_name" content="<?= e(PHPSPARK_SHORT) ?>">
    <meta property="og:image" content="<?= e($socialImage) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($pageTitle) ?>">
    <meta name="twitter:description" content="<?= e($metaDescription) ?>">
    <meta name="twitter:image" content="<?= e($socialImage) ?>">
    <script type="application/ld+json"><?= json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
<?php // end seo include ?>

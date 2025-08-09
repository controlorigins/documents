<?php
// Global configuration for PHPDocSpark
$CANONICAL_BASE = 'https://phpdocspark.markhazleton.com'; // Long-term canonical domain

// Brand constants
define('PHPSPARK_BRAND', 'PHPDocSpark by Mark Hazleton');
define('PHPSPARK_SHORT', 'PHPDocSpark');
define('PHPSPARK_AUTHOR', 'Mark Hazleton');
define('PHPSPARK_AUTHOR_URL', 'https://markhazleton.com');
define('PHPSPARK_SUITE_NAME', 'WebSpark');
define('PHPSPARK_SUITE_URL', 'https://webspark.markhazleton.com');

// Default meta description (override per page if desired)
$DEFAULT_META_DESCRIPTION = 'PHPDocSpark is an open source PHP documentation and data experience platform by Mark Hazleton featuring markdown viewing, search, data analysis, GitHub integration, and educational examples as part of the WebSpark suite.';

function canonical_url(string $pathOrQuery = ''): string {
    global $CANONICAL_BASE;
    if ($pathOrQuery === '') return rtrim($CANONICAL_BASE, '/');
    if (str_starts_with($pathOrQuery, 'http')) return $pathOrQuery;
    if ($pathOrQuery[0] !== '/') $pathOrQuery = '/' . $pathOrQuery;
    return rtrim($CANONICAL_BASE, '/') . $pathOrQuery;
}

function e(string $v): string { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
?>

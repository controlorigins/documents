<?php

declare(strict_types=1);

// Centralized documentation utilities: slug generation, lookup, pretty URLs
// Builds bidirectional maps between physical markdown files and cleaned slugs.

if (!defined('PHPSPARK_DOCS_INITIALIZED')) {
    define('PHPSPARK_DOCS_INITIALIZED', true);

    $DOCS_ROOT_DIR = __DIR__ . '/../assets/markdown';
    $DOCS_FILE_TO_SLUG = [];   // relative file path without .md => slug path
    $DOCS_SLUG_TO_FILE = [];   // slug path => relative file path WITH .md

    function doc_slugify_segment(string $seg): string {
        $seg = trim($seg);
        $seg = preg_replace('/[\s_+]+/u', '-', $seg);            // spaces & underscores → hyphen
        $seg = preg_replace('/[^a-zA-Z0-9\-]+/u', '-', $seg);    // strip non-url-safe
        $seg = preg_replace('/-+/','-',$seg);                     // collapse hyphens
        $seg = trim($seg,'-');
        $seg = strtolower($seg);
        if ($seg === '') $seg = 'doc';
        return $seg;
    }

    function doc_build_maps(string $root): void {
        global $DOCS_FILE_TO_SLUG, $DOCS_SLUG_TO_FILE;
        if (!is_dir($root)) return;
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
        foreach ($it as $file) {
            if ($file->isFile() && strtolower($file->getExtension()) === 'md') {
                $relative = str_replace('\\','/', substr($file->getPathname(), strlen($root) + 1)); // includes filename.md
                $noExt = preg_replace('/\.md$/i','',$relative);
                $segments = explode('/', $noExt);
                $slugSegs = array_map('doc_slugify_segment', $segments);
                $slugPath = implode('/', $slugSegs);
                // ensure uniqueness
                $base = $slugPath; $i=2;
                while (isset($DOCS_SLUG_TO_FILE[$slugPath])) { $slugPath = $base . '-' . $i; $i++; }
                $DOCS_FILE_TO_SLUG[$noExt] = $slugPath;
                $DOCS_SLUG_TO_FILE[$slugPath] = $relative; // still with .md
            }
        }
    }

    doc_build_maps($DOCS_ROOT_DIR);

    function doc_pretty_url(string $relative): string {
        global $DOCS_FILE_TO_SLUG;
        $relative = ltrim($relative,'/');
        if (str_ends_with($relative,'.md')) $relative = substr($relative,0,-3);
        if (isset($DOCS_FILE_TO_SLUG[$relative])) return '/doc/' . $DOCS_FILE_TO_SLUG[$relative];
        $segments = explode('/', $relative);
        $slugSegs = array_map('doc_slugify_segment', $segments);
        return '/doc/' . implode('/', $slugSegs);
    }

    function doc_lookup_file_by_slug(string $slugPath): ?string {
        global $DOCS_SLUG_TO_FILE, $DOCS_ROOT_DIR;
        if (isset($DOCS_SLUG_TO_FILE[$slugPath])) return $DOCS_SLUG_TO_FILE[$slugPath];
        
        // Only do heuristic search if the exact slug path wasn't found
        // This prevents false matches like '/doc/seo' matching 'Project/SEO.md'
        $segments = explode('/', $slugPath);
        $searchBase = $DOCS_ROOT_DIR; 
        $relParts = [];
        
        foreach ($segments as $seg) {
            $found = null;
            if (!is_dir($searchBase)) return null; // Path doesn't exist
            
            foreach (scandir($searchBase) as $entry) {
                if ($entry==='.'||$entry==='..') continue;
                $full = $searchBase . '/' . $entry;
                $candidateSeg = is_dir($full)? $entry : preg_replace('/\.md$/i','',$entry);
                if (doc_slugify_segment($candidateSeg) === $seg) { 
                    $found = $entry; 
                    break; 
                }
            }
            if ($found===null) return null; // Segment not found at this level
            $relParts[] = $found;
            $searchBase .= '/' . $found;
        }
        
        $relative = implode('/', $relParts);
        if (!str_ends_with(strtolower($relative), '.md')) $relative .= '.md';
        
        // Verify the file actually exists before returning
        if (is_file($DOCS_ROOT_DIR . '/' . $relative)) {
            return $relative;
        }
        
        return null;
    }
}
?>

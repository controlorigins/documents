<?php
/**
 * Version and Build Information
 * This file is automatically updated during the build process
 * Last updated: 2025-08-11 01:36:36
 */

// Version number (from package.json)
define('SITE_VERSION', '1.2.0');

// Build timestamp (updated automatically by build process)
define('SITE_BUILD_TIMESTAMP', '2025-08-11 01:36:36');

// Build number (incremented automatically)
define('SITE_BUILD_NUMBER', '3');

// Git commit hash (if available)
define('SITE_GIT_COMMIT', '2719a2f1a2d7661c56e8d3f88df1e7dce321bfdb');

/**
 * Get formatted build information
 */
function getSiteBuildInfo(): array {
    return [
        'version' => SITE_VERSION,
        'build_number' => SITE_BUILD_NUMBER,
        'build_date' => SITE_BUILD_TIMESTAMP,
        'git_commit' => SITE_GIT_COMMIT,
        'formatted_date' => date('M j, Y \\a\\t g:i A T', strtotime(SITE_BUILD_TIMESTAMP)),
        'short_commit' => substr(SITE_GIT_COMMIT, 0, 7)
    ];
}

/**
 * Get version string for display
 */
function getSiteVersionString(): string {
    $info = getSiteBuildInfo();
    return "v{$info['version']}.{$info['build_number']}";
}

/**
 * Get full build string for footer
 */
function getSiteBuildString(): string {
    $info = getSiteBuildInfo();
    if ($info['git_commit'] !== 'unknown') {
        return "v{$info['version']}.{$info['build_number']} ({$info['short_commit']}) - Built on {$info['formatted_date']}";
    } else {
        return "v{$info['version']}.{$info['build_number']} - Built on {$info['formatted_date']}";
    }
}
?>
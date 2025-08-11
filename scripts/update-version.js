#!/usr/bin/env node

/**
 * Build Script - Updates version information
 * This script is run during the build process to increment build numbers
 * and track version history
 */

const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

// Configuration
const VERSION_FILE = path.join(__dirname, '../website/includes/version.php');
const PACKAGE_JSON = path.join(__dirname, '../package.json');
const BUILD_COUNTER_FILE = path.join(__dirname, '../build-counter.json');

// Read current version from package.json
let packageVersion = '1.2.0';
try {
    if (fs.existsSync(PACKAGE_JSON)) {
        const pkg = JSON.parse(fs.readFileSync(PACKAGE_JSON, 'utf8'));
        packageVersion = pkg.version || '1.2.0';
    }
} catch (error) {
    console.log('Warning: Could not read package.json version, using default');
}

// Handle build number increment
let buildNumber = process.env.BUILD_BUILDNUMBER || process.env.GITHUB_RUN_NUMBER;

if (!buildNumber) {
    // Local development - use and increment local counter
    let buildCounter = { count: 0, lastBuild: null };
    
    try {
        if (fs.existsSync(BUILD_COUNTER_FILE)) {
            buildCounter = JSON.parse(fs.readFileSync(BUILD_COUNTER_FILE, 'utf8'));
        }
    } catch (error) {
        console.log('Warning: Could not read build counter, starting from 0');
    }
    
    // Increment build number
    buildCounter.count = (buildCounter.count || 0) + 1;
    buildCounter.lastBuild = new Date().toISOString();
    
    // Save updated counter
    try {
        fs.writeFileSync(BUILD_COUNTER_FILE, JSON.stringify(buildCounter, null, 2));
    } catch (error) {
        console.log('Warning: Could not save build counter');
    }
    
    buildNumber = buildCounter.count.toString();
} else {
    // CI/CD environment - extract build number
    if (buildNumber.includes('.')) {
        // If it's a full build number like "20250809.1", extract the last part
        buildNumber = buildNumber.split('.').pop();
    }
}

// Get Git commit hash
let gitCommit = 'unknown';
try {
    gitCommit = execSync('git rev-parse HEAD', { encoding: 'utf8' }).trim();
} catch (error) {
    try {
        gitCommit = execSync('git rev-parse --short HEAD', { encoding: 'utf8' }).trim();
    } catch (error2) {
        console.log('Warning: Could not get git commit hash');
    }
}

// Generate timestamp
const buildTimestamp = new Date().toISOString().replace('T', ' ').replace(/\.\d{3}Z$/, '');

// Generate version.php content
const versionContent = `<?php
/**
 * Version and Build Information
 * This file is automatically updated during the build process
 * Last updated: ${buildTimestamp}
 */

// Version number (from package.json)
define('SITE_VERSION', '${packageVersion}');

// Build timestamp (updated automatically by build process)
define('SITE_BUILD_TIMESTAMP', '${buildTimestamp}');

// Build number (incremented automatically)
define('SITE_BUILD_NUMBER', '${buildNumber}');

// Git commit hash (if available)
define('SITE_GIT_COMMIT', '${gitCommit}');

/**
 * Get formatted build information
 */
function getSiteBuildInfo(): array {
    return [
        'version' => SITE_VERSION,
        'build_number' => SITE_BUILD_NUMBER,
        'build_date' => SITE_BUILD_TIMESTAMP,
        'git_commit' => SITE_GIT_COMMIT,
        'formatted_date' => date('M j, Y \\\\a\\\\t g:i A T', strtotime(SITE_BUILD_TIMESTAMP)),
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
?>`;

// Write the version file
try {
    fs.writeFileSync(VERSION_FILE, versionContent, 'utf8');
    console.log(`✅ Version file updated:`);
    console.log(`   Version: ${packageVersion}`);
    console.log(`   Build: ${buildNumber}`);
    console.log(`   Commit: ${gitCommit.substring(0, 7)}`);
    console.log(`   Timestamp: ${buildTimestamp}`);
} catch (error) {
    console.error('❌ Failed to write version file:', error.message);
    process.exit(1);
}

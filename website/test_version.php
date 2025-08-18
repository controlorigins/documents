<?php

declare(strict_types=1);

require_once 'includes/config.php';

echo "<h1>Version Information Test</h1>";

echo "<h2>Raw Version Data</h2>";
echo "<ul>";
echo "<li>SITE_VERSION: " . SITE_VERSION . "</li>";
echo "<li>SITE_BUILD_TIMESTAMP: " . SITE_BUILD_TIMESTAMP . "</li>";
echo "<li>SITE_BUILD_NUMBER: " . SITE_BUILD_NUMBER . "</li>";
echo "<li>SITE_GIT_COMMIT: " . SITE_GIT_COMMIT . "</li>";
echo "</ul>";

echo "<h2>Formatted Version Functions</h2>";
echo "<ul>";
echo "<li>getSiteVersionString(): " . getSiteVersionString() . "</li>";
echo "<li>getSiteBuildString(): " . getSiteBuildString() . "</li>";
echo "</ul>";

echo "<h2>Build Info Array</h2>";
$buildInfo = getSiteBuildInfo();
echo "<pre>";
print_r($buildInfo);
echo "</pre>";
?>

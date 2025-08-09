<?php
require_once 'includes/docs.php';

echo "Content-Type: text/html; charset=UTF-8\n\n";

if ($_POST['test_redirect'] ?? false) {
    $selectedFile = $_POST['test_redirect'];
    echo "<h1>Testing Redirect</h1>";
    echo "<p>Selected file: <code>" . htmlspecialchars($selectedFile) . "</code></p>";
    
    $prettyUrl = doc_pretty_url($selectedFile);
    echo "<p>Generated URL: <code>" . htmlspecialchars($prettyUrl) . "</code></p>";
    
    echo "<p>This would redirect to: <a href='" . htmlspecialchars($prettyUrl) . "'>" . htmlspecialchars($prettyUrl) . "</a></p>";
    
    // Show what the actual redirect would be
    echo "<script>
        console.log('Redirect URL: " . addslashes($prettyUrl) . "');
        setTimeout(function() {
            window.location.href = '" . addslashes($prettyUrl) . "';
        }, 2000);
    </script>";
    
    echo "<p>Redirecting in 2 seconds...</p>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Redirect Test</title>
</head>
<body>
    <h1>Test Dropdown Redirect</h1>
    <form method="POST">
        <select name="test_redirect" onchange="this.form.submit()">
            <option value="">-- Select --</option>
            <option value="Build/Build System Documentation.md">Build System Documentation</option>
            <option value="ChatGPT/1_ChatGPT Overview.md">ChatGPT Overview</option>
            <option value="Overview.md">Overview</option>
        </select>
    </form>
    
    <h2>Manual Tests</h2>
    <p><a href="/doc/build/build-system-documentation">Direct: build/build-system-documentation</a></p>
    <p><a href="/doc/build%2Fbuild-system-documentation">Encoded: build%2Fbuild-system-documentation</a></p>
    <p><a href="/doc/build-2fbuild-system-documentation">Broken: build-2fbuild-system-documentation</a></p>
</body>
</html>

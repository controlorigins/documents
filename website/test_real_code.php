<?php
require_once __DIR__ . '/includes/MarkdownProcessor.php';

// Test the exact content from the Create PHP Joke Page.md file
$testMarkdown = '1.  **Create a new PHP file (e.g., `index.php`):**

```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Joke Display</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>

<div id="joke-container">
    <!-- Joke will be displayed here -->
</div>

<button onclick="fetchJoke()">Fetch a new Joke</button>

<script type="text/javascript">
    function fetchJoke() {
        $.ajax({
            url: \'fetch_joke.php\',
            method: \'GET\',
            success: function(response) {
                $(\'#joke-container\').html(response);
            }
        });
    }

    // Fetch joke on page load
    $(document).ready(function() {
        fetchJoke();
    });
</script>

</body>
</html>
```
';

$processor = new MarkdownProcessor([
    'cache' => ['enabled' => false],
    'parser' => [
        'safeMode' => false,
        'markupEscaped' => false,
        'urlsLinked' => true
    ],
    'metadata' => ['enabled' => false],
    'debug' => true
]);

echo "<h1>Real Document Code Block Test</h1>\n";

echo "<h2>HTML Output:</h2>\n";
$htmlOutput = $processor->convertString($testMarkdown);
echo "<div style='border: 2px solid #28a745; padding: 15px; background: #f8f9fa;'>\n";
echo $htmlOutput;
echo "</div>\n";

echo "<h2>Raw HTML Structure:</h2>\n";
echo "<pre style='background: #e9ecef; padding: 15px; border: 1px solid #ced4da; font-size: 12px;'>";
echo htmlspecialchars($htmlOutput);
echo "</pre>\n";
?>

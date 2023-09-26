<?php
// Define the root folder
$rootFolder = 'assets/markdown';

// Initialize the search term
$searchTerm = '';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the search term from the user
    $searchTerm = isset($_POST['searchTerm']) ? $_POST['searchTerm'] : '';

    // Function to recursively search for files containing the search term
    function searchFiles($folder, $searchTerm, $relativePath = '')
    {
        $results = [];
        $files = scandir($folder);

        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $folder . '/' . $file;
                $currentRelativePath = $relativePath !== '' ? $relativePath . '/' . $file : $file;

                if (is_dir($filePath)) {
                    // If it's a directory, recursively search it
                    $results = array_merge($results, searchFiles($filePath, $searchTerm, $currentRelativePath));
                } elseif (pathinfo($file, PATHINFO_EXTENSION) == 'md') {
                    // If it's a markdown file, check if it contains the search term
                    $content = file_get_contents($filePath);
                    if (stripos($content, $searchTerm) !== false) {
                        // Construct the link with the query parameter
                        $results[] = [
                            'link' => '/?file=' . rawurlencode($currentRelativePath),
                            'text' => $file
                        ];
                    }
                }
            }
        }

        return $results;
    }

    // Search for files containing the search term
    $matchingFiles = searchFiles($rootFolder, $searchTerm);
}
?>
<div class='card'>
    <div class='card-header'>
        <h1>Search Documents</h1>  
        <a href='/?file=ChatGPT%2FSessions%2FSearch%20Markdown%20Files%20PHP.md'>
        How this page was created</a>
    </div>
    <div class="card-body">

    <div class="container-fluid mt-4">




    <h1>Search for Markdown Files</h1>
    <form method="POST">
        <label for="searchTerm">Enter Search Term:</label>
        <input type="text" name="searchTerm" id="searchTerm" value="<?= htmlspecialchars($searchTerm) ?>">
        <button type="submit">Search</button>
    </form>

    <?php if ($searchTerm !== '' && !empty($matchingFiles)): ?>
                <h2>Matching Files:</h2>
                <ul>
                    <?php foreach ($matchingFiles as $file): ?>
                                <li><a href="<?= htmlspecialchars($file['link']) ?>"><?= htmlspecialchars($file['text']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
    <?php elseif ($searchTerm === ''): ?>
                <p>Search term is required.</p>
    <?php else: ?>
                <p>No matching files found.</p>
    <?php endif; ?>

    </div>
    </div>
    <div class="card-footer">
        <br/>
        <br/>
    </div>
</div>
<br/><br/><br/><br/>


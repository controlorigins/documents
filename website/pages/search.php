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

    <div class="container mt-4">

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

<br/>

    </div>

<div class="container">
    <h3>Explanation</h3>
<h4>What We Did:</h4>
<p>
We've created a reusable component for your website that allows users to search for specific documents or content within a particular folder. Think of it like a search bar, similar to what you see on search engines like Google, but tailored to your website's needs.
    </p>
<h4>Why We Did It:</h4>
<p>
This component makes it easy for your website visitors to find relevant information quickly. It's particularly useful if you have a lot of documents or articles on your website, and users want to search for specific ones without manually browsing through everything.
</p>
<h4>How It Works:</h4>
<dl>
<dt>User Input:</dt>
<dd>Users enter a search term into a text box and click a "Search" button.</dd>
<dt>Search Process:</dt>
<dd>Behind the scenes, the component goes through all the documents (in this case, Markdown files) stored in a specific folder on your website.</dd>
<dt>Finding Matches:</dt>
<dd>It looks inside each document to see if the search term appears anywhere within them. If it does, it remembers those documents as matches.</dd>
<dt>Displaying Results:</dt>
<dd>The component then displays a list of matching documents to the user, along with clickable links. When users click on a link, it takes them directly to that document.</dd>

</dl>
<h4>Why It's Useful:</h4>
<dl>
<dt>Time-Saver:</dt>
<dd>Users don't need to manually hunt for documents; they can search by keywords.</dd>
<dt>Efficient Navigation:</dt>
<dd>Especially valuable if you have lots of content.</dd>
<dt>User-Friendly:</dt>
<dd>It makes your website more user-friendly and efficient, enhancing the overall experience.</dd>

</dl>
<h4>What You Need to Do:</h4>
<p>
You can integrate this component into different parts of your website, such as your homepage or specific sections. Whenever users need to search for documents, this tool will be readily available to assist them.
</p><p>
Overall, it's a user-focused feature that enhances your website's usability and helps visitors find what they're looking for more easily.
    </p>
</div>



    </div>
    <div class="card-footer">
        <br/>
        <br/>
    </div>
</div>
<br/><br/><br/><br/>


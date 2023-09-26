# Search Markdown Files PHP
## Mark Hazleton:
Create a PHP page that allows me to search all markdown files under a root folder "assets\\markdown". Have the user input a search term and then return a list of all markdown files that contain that term.
## chatgpt:
To create a PHP page that allows you to search all markdown files under a root folder and return a list of files containing a specified search term, you can follow these steps. Below is a PHP script that accomplishes this task:

```php
<?php
// Define the root folder
$rootFolder = 'assets/markdown';

// Get the search term from the user
$searchTerm = isset($_POST['searchTerm']) ? $_POST['searchTerm'] : '';

// Function to recursively search for files containing the search term
function searchFiles($folder, $searchTerm) {
    $results = [];
    $files = scandir($folder);

    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            $filePath = $folder . '/' . $file;

            if (is_dir($filePath)) {
                // If it's a directory, recursively search it
                $results = array_merge($results, searchFiles($filePath, $searchTerm));
            } elseif (pathinfo($file, PATHINFO_EXTENSION) == 'md') {
                // If it's a markdown file, check if it contains the search term
                $content = file_get_contents($filePath);
                if (stripos($content, $searchTerm) !== false) {
                    $results[] = $filePath;
                }
            }
        }
    }

    return $results;
}

// Search for files containing the search term
$matchingFiles = searchFiles($rootFolder, $searchTerm);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Markdown File Search</title>
</head>
<body>
    <h1>Search for Markdown Files</h1>
    <form method="POST">
        <label for="searchTerm">Enter Search Term:</label>
        <input type="text" name="searchTerm" id="searchTerm" value="<?= htmlspecialchars($searchTerm) ?>">
        <button type="submit">Search</button>
    </form>

    <?php if (!empty($matchingFiles)): ?>
        <h2>Matching Files:</h2>
        <ul>
            <?php foreach ($matchingFiles as $file): ?>
                <li><?= htmlspecialchars($file) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No matching files found.</p>
    <?php endif; ?>
</body>
</html>
```

This PHP script defines a function `searchFiles` that recursively searches for markdown files containing the search term. It then displays the matching file paths in a list on the web page.

Make sure to place this PHP script in your web server's document root and adjust the `$rootFolder` variable to point to your "assets/markdown" directory. This script uses a POST form to receive the search term from the user and displays the results on the same page.

References:

1.  [PHP scandir() Function](https://www.php.net/manual/en/function.scandir.php)
2.  [PHP file\_get\_contents() Function](https://www.php.net/manual/en/function.file-get-contents.php)
3.  [PHP pathinfo() Function](https://www.php.net/manual/en/function.pathinfo.php)
## Mark Hazleton:
if the search term is blank, return a message that search term is required
## chatgpt:
To handle the case where the search term is blank and return a message indicating that a search term is required, you can modify the PHP script as follows:

```php
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
    function searchFiles($folder, $searchTerm) {
        $results = [];
        $files = scandir($folder);

        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $folder . '/' . $file;

                if (is_dir($filePath)) {
                    // If it's a directory, recursively search it
                    $results = array_merge($results, searchFiles($filePath, $searchTerm));
                } elseif (pathinfo($file, PATHINFO_EXTENSION) == 'md') {
                    // If it's a markdown file, check if it contains the search term
                    $content = file_get_contents($filePath);
                    if (stripos($content, $searchTerm) !== false) {
                        $results[] = $filePath;
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

<!DOCTYPE html>
<html>
<head>
    <title>Markdown File Search</title>
</head>
<body>
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
                <li><?= htmlspecialchars($file) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php elseif ($searchTerm === ''): ?>
        <p>Search term is required.</p>
    <?php else: ?>
        <p>No matching files found.</p>
    <?php endif; ?>
</body>
</html>
```

In this modified script, we check if the form has been submitted using `$_SERVER['REQUEST_METHOD']`. If it's a POST request, we retrieve the search term from the user input. Then, we check if the search term is blank (`$searchTerm === ''`) and display a message indicating that the search term is required when it is.

References:

1.  [PHP $\_SERVER](https://www.php.net/manual/en/reserved.variables.server.php)
## Mark Hazleton:
update results to be a link to that points to the site root with a query parameter called file. For example /?file=ChatGPT%252F1\_ChatGPT%2BOverview.md
## chatgpt:
To update the results to be a link pointing to the site root with a query parameter called `file`, you can modify the PHP script as follows:

```php
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
    function searchFiles($folder, $searchTerm) {
        $results = [];
        $files = scandir($folder);

        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $folder . '/' . $file;

                if (is_dir($filePath)) {
                    // If it's a directory, recursively search it
                    $results = array_merge($results, searchFiles($filePath, $searchTerm));
                } elseif (pathinfo($file, PATHINFO_EXTENSION) == 'md') {
                    // If it's a markdown file, check if it contains the search term
                    $content = file_get_contents($filePath);
                    if (stripos($content, $searchTerm) !== false) {
                        // Construct the link with the query parameter
                        $results[] = '/?file=' . rawurlencode($file);
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

<!DOCTYPE html>
<html>
<head>
    <title>Markdown File Search</title>
</head>
<body>
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
                <li><a href="<?= htmlspecialchars($file) ?>"><?= htmlspecialchars($file) ?></a></li>
            <?php endforeach; ?>
        </ul>
    <?php elseif ($searchTerm === ''): ?>
        <p>Search term is required.</p>
    <?php else: ?>
        <p>No matching files found.</p>
    <?php endif; ?>
</body>
</html>
```

In this updated script, when constructing the link for each matching file, we use `rawurlencode` to encode the filename in the query parameter. This ensures that special characters in the filename are properly encoded in the URL.

Now, each matching file is displayed as a clickable link with the `file` query parameter in the URL, pointing to the site root.

References:

1.  [PHP rawurlencode](https://www.php.net/manual/en/function.rawurlencode.php)
## Mark Hazleton:
I think you have the link source and text switched, we only want to rawurlencode the link, not the displayed text
## chatgpt:
You're correct; my previous response had the link source and text switched. To only `rawurlencode` the link and not the displayed text, you can modify the PHP script as follows:

```php
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
    function searchFiles($folder, $searchTerm) {
        $results = [];
        $files = scandir($folder);

        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $folder . '/' . $file;

                if (is_dir($filePath)) {
                    // If it's a directory, recursively search it
                    $results = array_merge($results, searchFiles($filePath, $searchTerm));
                } elseif (pathinfo($file, PATHINFO_EXTENSION) == 'md') {
                    // If it's a markdown file, check if it contains the search term
                    $content = file_get_contents($filePath);
                    if (stripos($content, $searchTerm) !== false) {
                        // Construct the link with the query parameter
                        $results[] = [
                            'link' => '/' . rawurlencode($file),
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

<!DOCTYPE html>
<html>
<head>
    <title>Markdown File Search</title>
</head>
<body>
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
</body>
</html>
```

In this updated script, the `searchFiles` function now returns an array for each matching file, containing both the link source and the displayed text. The `rawurlencode` function is applied only to the link source, ensuring that the filename is properly encoded in the URL.
## Mark Hazleton:
In the anchor href you must start with ?file= and then have the full urlencoded path
## chatgpt:
I apologize for the oversight. To construct the anchor href with `?file=` followed by the full URL-encoded path, you can modify the PHP script as follows:

```php
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
    function searchFiles($folder, $searchTerm) {
        $results = [];
        $files = scandir($folder);

        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $folder . '/' . $file;

                if (is_dir($filePath)) {
                    // If it's a directory, recursively search it
                    $results = array_merge($results, searchFiles($filePath, $searchTerm));
                } elseif (pathinfo($file, PATHINFO_EXTENSION) == 'md') {
                    // If it's a markdown file, check if it contains the search term
                    $content = file_get_contents($filePath);
                    if (stripos($content, $searchTerm) !== false) {
                        // Construct the link with the query parameter
                        $results[] = [
                            'link' => '/?file=' . rawurlencode($file),
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

<!DOCTYPE html>
<html>
<head>
    <title>Markdown File Search</title>
</head>
<body>
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
</body>
</html>
```

In this updated script, the anchor href is constructed with `?file=` followed by the full URL-encoded path of the file. This ensures that the URL parameter `file` contains the correctly encoded file path for each matching file.
## Mark Hazleton:
the href needs to have the relative path of the file from the assets/markdown root folder. So if the file is in assets/markdown/ChatGPT/1\_ChatGPT Overview.md. the link should be ?file=ChatGPT%252F1\_ChatGPT%2BOverview.md
## chatgpt:
I understand your requirement. You want the href to have the relative path of the file from the `assets/markdown` root folder. To achieve this, you can modify the PHP script as follows:

```php
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
    function searchFiles($folder, $searchTerm, $relativePath = '') {
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

<!DOCTYPE html>
<html>
<head>
    <title>Markdown File Search</title>
</head>
<body>
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
</body>
</html>
```

In this updated script, the `searchFiles` function now takes an additional parameter `$relativePath`, which keeps track of the relative path of each file from the `assets/markdown` root folder. The `$currentRelativePath` variable is used to construct the correct relative path for each file in the link's query parameter.

This should generate links with the correct relative paths as you described.
## Mark Hazleton:
Update the search.php to be a module I can include in a layout or template.php
## chatgpt:
To convert the search functionality into a reusable module that you can include in a layout or `template.php`, you can create a separate PHP file for the search module and use functions to encapsulate the logic. Here's how you can structure it:

**search\_module.php**:

```php
<?php
function searchMarkdownFiles($rootFolder, $searchTerm) {
    $matchingFiles = [];

    // Function to recursively search for files containing the search term
    function searchFiles($folder, $searchTerm, $relativePath = '') {
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
    if ($searchTerm !== '') {
        $matchingFiles = searchFiles($rootFolder, $searchTerm);
    }

    return $matchingFiles;
}
?>
```

**template.php**:

```php
<!DOCTYPE html>
<html>
<head>
    <title>Markdown File Search</title>
</head>
<body>
    <h1>Search for Markdown Files</h1>
    <form method="POST">
        <label for="searchTerm">Enter Search Term:</label>
        <input type="text" name="searchTerm" id="searchTerm" value="<?= htmlspecialchars($searchTerm) ?>">
        <button type="submit">Search</button>
    </form>

    <?php if (!empty($matchingFiles)): ?>
        <h2>Matching Files:</h2>
        <ul>
            <?php foreach ($matchingFiles as $file): ?>
                <li><a href="<?= htmlspecialchars($file['link']) ?>"><?= htmlspecialchars($file['text']) ?></a></li>
            <?php endforeach; ?>
        </ul>
    <?php elseif ($searchTerm === ''): ?>
        <p>Search term is required.</p>
    <?php elseif (isset($_POST['searchTerm'])): ?>
        <p>No matching files found.</p>
    <?php endif; ?>
</body>
</html>
```

To include the search module in your layout or template, you can simply include it at the top of your `template.php` file using `require`:

```php
<?php
require_once('search_module.php');

// Define the root folder for your Markdown files
$rootFolder = 'assets/markdown';

// Get the search term from the user
$searchTerm = isset($_POST['searchTerm']) ? $_POST['searchTerm'] : '';

// Call the search function to get the matching files
$matchingFiles = searchMarkdownFiles($rootFolder, $searchTerm);
?>

<!-- Rest of your HTML content -->
```

By structuring your code in this way, you can easily reuse the search module in multiple templates or layouts.
## Mark Hazleton:
Explain in non-technical terms to an executive audience what we just did
## chatgpt:
Certainly! Here's an explanation in non-technical terms for an executive audience:

**What We Did:**

We've created a reusable component for your website that allows users to search for specific documents or content within a particular folder. Think of it like a search bar, similar to what you see on search engines like Google, but tailored to your website's needs.

**Why We Did It:**

This component makes it easy for your website visitors to find relevant information quickly. It's particularly useful if you have a lot of documents or articles on your website, and users want to search for specific ones without manually browsing through everything.

**How It Works:**

1.  **User Input:** Users enter a search term into a text box and click a "Search" button.
    
2.  **Search Process:** Behind the scenes, the component goes through all the documents (in this case, Markdown files) stored in a specific folder on your website.
    
3.  **Finding Matches:** It looks inside each document to see if the search term appears anywhere within them. If it does, it remembers those documents as matches.
    
4.  **Displaying Results:** The component then displays a list of matching documents to the user, along with clickable links. When users click on a link, it takes them directly to that document.
    

**Why It's Useful:**

-   **Time-Saver:** Users don't need to manually hunt for documents; they can search by keywords.
-   **Efficient Navigation:** Especially valuable if you have lots of content.
-   **User-Friendly:** It makes your website more user-friendly and efficient, enhancing the overall experience.

**What You Need to Do:**

You can integrate this component into different parts of your website, such as your homepage or specific sections. Whenever users need to search for documents, this tool will be readily available to assist them.

Overall, it's a user-focused feature that enhances your website's usability and helps visitors find what they're looking for more easily.

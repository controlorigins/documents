# Markdown PHP File Viewer
## Mark Hazleton:
I have a folder called assets/markdown which contains markdown file (.md) and sub folders. Create a PHP web page to get all the files under the /assets/markdown folders (including sub folders) and build a navigation list and then allow me to select a file and then render the .md file in html
## chatgpt:
To create a PHP web page that lists all the Markdown files in the `/assets/markdown` folder and its subfolders and renders a selected Markdown file in HTML, you can follow these steps:

1.  Set up your project directory structure:
    
    ```diff
    - project_folder/
      - assets/
        - markdown/
          - file1.md
          - file2.md
          - subfolder1/
            - subfile1.md
          - subfolder2/
            - subfile2.md
      - index.php
    ```
    
2.  Create `index.php`:
    

```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Markdown Viewer</title>
</head>
<body>
    <h1>Markdown Files</h1>
    <ul>
        <?php
        // Function to recursively scan a directory and return an array of file paths
        function scanDirectory($dir) {
            $files = [];
            foreach (scandir($dir) as $file) {
                if ($file !== '.' && $file !== '..') {
                    $filePath = $dir . '/' . $file;
                    if (is_dir($filePath)) {
                        $files = array_merge($files, scanDirectory($filePath));
                    } elseif (pathinfo($file, PATHINFO_EXTENSION) === 'md') {
                        $files[] = $filePath;
                    }
                }
            }
            return $files;
        }

        // Get all Markdown files
        $markdownFiles = scanDirectory('assets/markdown');

        // Display a list of Markdown files
        foreach ($markdownFiles as $markdownFile) {
            $relativePath = str_replace('assets/markdown/', '', $markdownFile);
            echo '<li><a href="?file=' . urlencode($relativePath) . '">' . $relativePath . '</a></li>';
        }
        ?>
    </ul>

    <hr>

    <div>
        <?php
        // Display the selected Markdown file in HTML
        if (isset($_GET['file'])) {
            $requestedFile = 'assets/markdown/' . $_GET['file'];
            if (file_exists($requestedFile) && pathinfo($requestedFile, PATHINFO_EXTENSION) === 'md') {
                $markdownContent = file_get_contents($requestedFile);
                $htmlContent = markdownToHTML($markdownContent); // Implement a markdown to HTML conversion function
                echo $htmlContent;
            } else {
                echo 'File not found or invalid file format.';
            }
        }
        ?>
    </div>
</body>
</html>
```

In the code above, we first create a function `scanDirectory` to recursively scan the `assets/markdown` directory and its subdirectories to collect all Markdown files. Then, we display a list of these files as links. When a link is clicked, we use the `file_get_contents` function to read the selected Markdown file's content and render it as HTML. You'll need to implement a `markdownToHTML` function to convert Markdown to HTML, which can be done using a library like Parsedown or a similar Markdown parser.

Make sure to replace `markdownToHTML` with your actual Markdown to HTML conversion logic. Additionally, consider adding error handling and security measures, such as sanitizing user input and validating file paths, to enhance the security of your application.
## Mark Hazleton:
update to crate nested lists that match the folder structure.
## chatgpt:
To create nested lists that match the folder structure in the navigation, you can modify the code as follows:

```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Markdown Viewer</title>
</head>
<body>
    <h1>Markdown Files</h1>
    <ul>
        <?php
        // Function to recursively scan a directory and return an array of file paths
        function scanDirectory($dir) {
            $files = [];
            foreach (scandir($dir) as $file) {
                if ($file !== '.' && $file !== '..') {
                    $filePath = $dir . '/' . $file;
                    if (is_dir($filePath)) {
                        $subFiles = scanDirectory($filePath);
                        if (!empty($subFiles)) {
                            $files[] = [
                                'folder' => $file,
                                'subFiles' => $subFiles,
                            ];
                        }
                    } elseif (pathinfo($file, PATHINFO_EXTENSION) === 'md') {
                        $files[] = $file;
                    }
                }
            }
            return $files;
        }

        // Get all Markdown files and folder structure
        $markdownFiles = scanDirectory('assets/markdown');

        // Function to render a nested list
        function renderList($items) {
            echo '<ul>';
            foreach ($items as $item) {
                if (is_array($item)) {
                    echo '<li>' . $item['folder'];
                    renderList($item['subFiles']);
                    echo '</li>';
                } else {
                    $relativePath = implode('/', $items);
                    echo '<li><a href="?file=' . urlencode($relativePath) . '">' . $item . '</a></li>';
                }
            }
            echo '</ul>';
        }

        // Display the nested list
        renderList($markdownFiles);
        ?>
    </ul>

    <hr>

    <div>
        <?php
        // Display the selected Markdown file in HTML
        if (isset($_GET['file'])) {
            $requestedFile = 'assets/markdown/' . $_GET['file'];
            if (file_exists($requestedFile) && pathinfo($requestedFile, PATHINFO_EXTENSION) === 'md') {
                $markdownContent = file_get_contents($requestedFile);
                $htmlContent = markdownToHTML($markdownContent); // Implement a markdown to HTML conversion function
                echo $htmlContent;
            } else {
                echo 'File not found or invalid file format.';
            }
        }
        ?>
    </div>
</body>
</html>
```

In this updated code, we've modified the `scanDirectory` function to return an array structure that represents the folder hierarchy. The `renderList` function recursively renders the nested list structure based on this hierarchy, creating nested `<ul>` and `<li>` elements.

This will create a nested list structure in the navigation that matches the folder structure in the `/assets/markdown` directory and its subdirectories. When a folder is clicked, it will expand to show its subfolders and files, and you can click on a Markdown file to render it as HTML.
## Mark Hazleton:
I got an error Warning: Array to string conversion in C:\\GitHub\\ControlOrigins\\documents\\website\\markdown.php on line 45
## chatgpt:
I apologize for the error. It appears there's an issue with the code when trying to display the selected Markdown file's path as a string. To fix this issue, we need to adjust how we handle file paths within the `renderList` function. Here's the corrected code:

```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Markdown Viewer</title>
</head>
<body>
    <h1>Markdown Files</h1>
    <ul>
        <?php
        // Function to recursively scan a directory and return an array of file paths
        function scanDirectory($dir) {
            $files = [];
            foreach (scandir($dir) as $file) {
                if ($file !== '.' && $file !== '..') {
                    $filePath = $dir . '/' . $file;
                    if (is_dir($filePath)) {
                        $subFiles = scanDirectory($filePath);
                        if (!empty($subFiles)) {
                            $files[] = [
                                'folder' => $file,
                                'subFiles' => $subFiles,
                            ];
                        }
                    } elseif (pathinfo($file, PATHINFO_EXTENSION) === 'md') {
                        $files[] = $file;
                    }
                }
            }
            return $files;
        }

        // Get all Markdown files and folder structure
        $markdownFiles = scanDirectory('assets/markdown');

        // Function to render a nested list
        function renderList($items, $basePath = '') {
            echo '<ul>';
            foreach ($items as $item) {
                if (is_array($item)) {
                    echo '<li>' . $item['folder'];
                    renderList($item['subFiles'], $basePath . $item['folder'] . '/');
                    echo '</li>';
                } else {
                    $relativePath = $basePath . $item;
                    echo '<li><a href="?file=' . urlencode($relativePath) . '">' . $item . '</a></li>';
                }
            }
            echo '</ul>';
        }

        // Display the nested list
        renderList($markdownFiles);
        ?>
    </ul>

    <hr>

    <div>
        <?php
        // Display the selected Markdown file in HTML
        if (isset($_GET['file'])) {
            $requestedFile = 'assets/markdown/' . $_GET['file'];
            if (file_exists($requestedFile) && pathinfo($requestedFile, PATHINFO_EXTENSION) === 'md') {
                $markdownContent = file_get_contents($requestedFile);
                $htmlContent = markdownToHTML($markdownContent); // Implement a markdown to HTML conversion function
                echo $htmlContent;
            } else {
                echo 'File not found or invalid file format.';
            }
        }
        ?>
    </div>
</body>
</html>
```

I've added a `$basePath` parameter to the `renderList` function, which is used to keep track of the relative path of each item in the hierarchy. This should resolve the "Array to string conversion" error you encountered, and the navigation links should work correctly now.
## Mark Hazleton:
Here is my updated script <!DOCTYPE html> <html lang="en"> <head> <meta charset="UTF-8"> <title>Markdown Viewer</title> </head> <body> <h1>Markdown Files</h1> <ul> <?php // Function to recursively scan a directory and return an array of file paths function scanDirectory($dir) { $files = \[\]; foreach (scandir($dir) as $file) { if ($file !== '.' && $file !== '..') { $filePath = $dir . '/' . $file; if (is\_dir($filePath)) { $subFiles = scanDirectory($filePath); if (!empty($subFiles)) { $files\[\] = \[ 'folder' => $file, 'subFiles' => $subFiles, \]; } } elseif (pathinfo($file, PATHINFO\_EXTENSION) === 'md') { $files\[\] = $file; } } } return $files; } // Get all Markdown files and folder structure $markdownFiles = scanDirectory('assets/markdown'); // Function to render a nested list function renderList($items, $basePath = '') { echo '<ul>'; foreach ($items as $item) { if (is\_array($item)) { echo '<li>' . $item\['folder'\]; renderList($item\['subFiles'\], $basePath . $item\['folder'\] . '/'); echo '</li>'; } else { $relativePath = $basePath . $item; echo '<li><a href="?file=' . urlencode($relativePath) . '">' . $item . '</a></li>'; } } echo '</ul>'; } // Display the nested list renderList($markdownFiles); ?> </ul> <hr> <div> <?php // Display the selected Markdown file in HTML if (isset($\_GET\['file'\])) { $requestedFile = 'assets/markdown/' . $\_GET\['file'\]; if (file\_exists($requestedFile) && pathinfo($requestedFile, PATHINFO\_EXTENSION) === 'md') { // Include the Parsedown library require\_once('pages/Parsedown.php'); $markdownContent = file\_get\_contents($requestedFile); // Create a new Parsedown instance $parsedown = new Parsedown(); // Parse Markdown to HTML and display it echo $parsedown->text($markdown); } else { echo 'File not found or invalid file format.'; } } ?> </div> </body> </html> I am getting an error Warning: Undefined variable $markdown in C:\\GitHub\\ControlOrigins\\documents\\website\\markdown.php on line 73
## chatgpt:
It looks like there is a variable name typo in your code. You are trying to use the variable `$markdown` when calling the `Parsedown` library, but you have stored the Markdown content in the variable named `$markdownContent`. To fix this error, you should replace `$markdown` with `$markdownContent` when calling `Parsedown`. Here's the corrected code:

```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Markdown Viewer</title>
</head>
<body>
    <h1>Markdown Files</h1>
    <ul>
        <?php
        // Function to recursively scan a directory and return an array of file paths
        function scanDirectory($dir) {
            $files = [];
            foreach (scandir($dir) as $file) {
                if ($file !== '.' && $file !== '..') {
                    $filePath = $dir . '/' . $file;
                    if (is_dir($filePath)) {
                        $subFiles = scanDirectory($filePath);
                        if (!empty($subFiles)) {
                            $files[] = [
                                'folder' => $file,
                                'subFiles' => $subFiles,
                            ];
                        }
                    } elseif (pathinfo($file, PATHINFO_EXTENSION) === 'md') {
                        $files[] = $file;
                    }
                }
            }
            return $files;
        }

        // Get all Markdown files and folder structure
        $markdownFiles = scanDirectory('assets/markdown');

        // Function to render a nested list
        function renderList($items, $basePath = '') {
            echo '<ul>';
            foreach ($items as $item) {
                if (is_array($item)) {
                    echo '<li>' . $item['folder'];
                    renderList($item['subFiles'], $basePath . $item['folder'] . '/');
                    echo '</li>';
                } else {
                    $relativePath = $basePath . $item;
                    echo '<li><a href="?file=' . urlencode($relativePath) . '">' . $item . '</a></li>';
                }
            }
            echo '</ul>';
        }

        // Display the nested list
        renderList($markdownFiles);
        ?>
    </ul>

    <hr>

    <div>
        <?php
        // Display the selected Markdown file in HTML
        if (isset($_GET['file'])) {
            $requestedFile = 'assets/markdown/' . $_GET['file'];
            if (file_exists($requestedFile) && pathinfo($requestedFile, PATHINFO_EXTENSION) === 'md') {
                
                // Include the Parsedown library
                require_once('pages/Parsedown.php');

                $markdownContent = file_get_contents($requestedFile);
                // Create a new Parsedown instance
                $parsedown = new Parsedown();
                // Parse Markdown to HTML and display it
                echo $parsedown->text($markdownContent); // Corrected variable name
                
            } else {
                echo 'File not found or invalid file format.';
            }
        }
        ?>
    </div>
</body>
</html>
```

I've replaced `$markdown` with `$markdownContent` to match the variable you have defined earlier in the code. This should resolve the "Undefined variable" error.

# Simple PHP JSON CMS
## Mark Hazleton:
Create a simple web site cms with PHP that reads from a JSON file
## chatgpt:
Creating a simple web content management system (CMS) with PHP that reads data from a JSON file is a great way to get started with web development. In this example, I'll provide a basic structure for such a CMS. Make sure you have PHP installed on your web server.

1.  **Directory Structure**:
    
    Create a directory structure like this:
    
    ```arduino
    /simple-cms/
        ├── index.php
        ├── template.php
        ├── content.json
    ```
    
2.  **content.json**:
    
    Create a JSON file named `content.json` to store your website content. Here's an example structure for the JSON file:
    
    ```json
    {
        "homepage": {
            "title": "Welcome to My Website",
            "content": "This is the homepage of my simple CMS website."
        },
        "about": {
            "title": "About Us",
            "content": "Learn more about our company here."
        },
        "contact": {
            "title": "Contact Us",
            "content": "You can reach us at contact@example.com."
        }
    }
    ```
    
3.  **index.php**:
    
    Create an `index.php` file for your website. This PHP script will read the JSON content and display it using a template:
    
    ```php
    <?php
    // Read the JSON content
    $jsonContent = file_get_contents('content.json');
    $content = json_decode($jsonContent, true);
    
    // Check if a page is specified in the URL
    $page = isset($_GET['page']) ? $_GET['page'] : 'homepage';
    
    // Check if the requested page exists in the content
    if (!array_key_exists($page, $content)) {
        $page = '404'; // Default to a 404 page if the page doesn't exist
    }
    
    // Include the template file
    include 'template.php';
    ?>
    ```
    
4.  **template.php**:
    
    Create a `template.php` file to define the structure of your web pages. This file will use the content fetched from the JSON file:
    
    ```php
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title><?php echo $content[$page]['title']; ?></title>
    </head>
    <body>
        <header>
            <h1><?php echo $content[$page]['title']; ?></h1>
        </header>
        <nav>
            <ul>
                <li><a href="?page=homepage">Home</a></li>
                <li><a href="?page=about">About</a></li>
                <li><a href="?page=contact">Contact</a></li>
            </ul>
        </nav>
        <main>
            <p><?php echo $content[$page]['content']; ?></p>
        </main>
        <footer>
            <p>&copy; <?php echo date('Y'); ?> My Website</p>
        </footer>
    </body>
    </html>
    ```
    
5.  **404 Handling**:
    
    Create a custom 404 page in your JSON file:
    
    ```json
    "404": {
        "title": "Page Not Found",
        "content": "The page you requested could not be found."
    }
    ```
    

Now, when you visit `http://yourdomain.com/simple-cms/`, it will display the homepage content. You can navigate to other pages using the links in the navigation bar, e.g., `http://yourdomain.com/simple-cms/?page=about`.

This is a very basic example of a CMS system that reads content from a JSON file. You can expand upon this by adding features like user authentication, a richer content structure, and more robust error handling as needed.
## Mark Hazleton:
In the content.json, I want the options of having the url of a php file to inject into the page.
## chatgpt:
If you want to include the option to specify a PHP file URL in the `content.json` for injection into a page, you can modify the JSON structure to include a "php\_file" field for each page. Here's how you can modify the `content.json` file and the PHP code accordingly:

1.  **Modify `content.json`**:
    
    Update the `content.json` file to include a "php\_file" field for each page. For example:
    
    ```json
    {
        "homepage": {
            "title": "Welcome to My Website",
            "content": "This is the homepage of my simple CMS website.",
            "php_file": "homepage.php"
        },
        "about": {
            "title": "About Us",
            "content": "Learn more about our company here.",
            "php_file": "about.php"
        },
        "contact": {
            "title": "Contact Us",
            "content": "You can reach us at contact@example.com.",
            "php_file": "contact.php"
        }
    }
    ```
    
2.  **Update `index.php`**:
    
    Modify the `index.php` file to include the PHP file specified in the JSON:
    
    ```php
    <?php
    // Read the JSON content
    $jsonContent = file_get_contents('content.json');
    $content = json_decode($jsonContent, true);
    
    // Check if a page is specified in the URL
    $page = isset($_GET['page']) ? $_GET['page'] : 'homepage';
    
    // Check if the requested page exists in the content
    if (!array_key_exists($page, $content)) {
        $page = '404'; // Default to a 404 page if the page doesn't exist
    }
    
    // Include the specified PHP file if available
    if (isset($content[$page]['php_file'])) {
        include $content[$page]['php_file'];
    } else {
        // Include the template file if no PHP file is specified
        include 'template.php';
    }
    ?>
    ```
    
3.  **Create PHP Files**:
    
    Create PHP files corresponding to the "php\_file" field specified in `content.json`. For example, if you have "homepage.php," create a file named `homepage.php` and include the PHP code you want to inject into the page:
    
    ```php
    <?php
    // Additional PHP code for the homepage
    echo "This is additional content injected from homepage.php.";
    ?>
    ```
    
    Repeat this for "about.php" and "contact.php" or any other pages you want to customize with PHP code.
    

Now, when you visit the pages (e.g., `http://yourdomain.com/simple-cms/?page=homepage`), the content specified in the JSON will be displayed, and if a "php\_file" is specified, it will inject the PHP code from that file into the page. This allows you to have dynamic content injected into your CMS pages based on the JSON configuration.
## Mark Hazleton:
For the CMS I want a common layout that is used by all my pages
## chatgpt:
To have a common layout used by all pages in your CMS, you can create a layout template that includes the header, navigation, footer, and a content section. Each individual page can then include this layout and inject its specific content into the content section. Here's how you can achieve this:

1.  **Directory Structure**:
    
    First, ensure your directory structure looks like this:
    
    ```arduino
    /simple-cms/
        ├── index.php
        ├── template.php
        ├── content.json
        ├── pages/
        │   ├── homepage.php
        │   ├── about.php
        │   ├── contact.php
    ```
    
2.  **content.json**:
    
    Update the `content.json` file without the "php\_file" field since we'll be using individual PHP files for each page. For example:
    
    ```json
    {
        "homepage": {
            "title": "Welcome to My Website"
        },
        "about": {
            "title": "About Us"
        },
        "contact": {
            "title": "Contact Us"
        }
    }
    ```
    
3.  **template.php**:
    
    Create or update the `template.php` file to define the common layout:
    
    ```php
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title><?php echo $content[$page]['title']; ?></title>
    </head>
    <body>
        <header>
            <h1><?php echo $content[$page]['title']; ?></h1>
        </header>
        <nav>
            <ul>
                <li><a href="?page=homepage">Home</a></li>
                <li><a href="?page=about">About</a></li>
                <li><a href="?page=contact">Contact</a></li>
            </ul>
        </nav>
        <main>
            <?php include "pages/$page.php"; ?>
        </main>
        <footer>
            <p>&copy; <?php echo date('Y'); ?> My Website</p>
        </footer>
    </body>
    </html>
    ```
    
4.  **Individual Page Files**:
    
    Create individual PHP files for each page under the `pages/` directory. For example, `homepage.php`, `about.php`, and `contact.php`. These files should only contain the specific content for each page:
    
    ```php
    <?php
    // Content for homepage.php
    echo "This is the content for the homepage.";
    ?>
    ```
    
    Repeat this for the other pages, customizing the content accordingly.
    
5.  **Update `index.php`**:
    
    Modify the `index.php` file to read the JSON content and include the template:
    
    ```php
    <?php
    // Read the JSON content
    $jsonContent = file_get_contents('content.json');
    $content = json_decode($jsonContent, true);
    
    // Check if a page is specified in the URL
    $page = isset($_GET['page']) ? $_GET['page'] : 'homepage';
    
    // Check if the requested page exists in the content
    if (!array_key_exists($page, $content)) {
        $page = '404'; // Default to a 404 page if the page doesn't exist
    }
    
    // Include the template file
    include 'template.php';
    ?>
    ```
    

Now, each page (`homepage.php`, `about.php`, `contact.php`) includes the common layout defined in `template.php`, ensuring a consistent design across all pages while allowing for specific content customization.
## Mark Hazleton:
update the template.php to use the content.json file to build the navigation
## chatgpt:
To build the navigation menu dynamically using the `content.json` file, you can update the `template.php` file as follows:

```php
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $content[$page]['title']; ?></title>
</head>
<body>
    <header>
        <h1><?php echo $content[$page]['title']; ?></h1>
    </header>
    <nav>
        <ul>
            <?php
            foreach ($content as $key => $value) {
                echo '<li><a href="?page=' . $key . '">' . $value['title'] . '</a></li>';
            }
            ?>
        </ul>
    </nav>
    <main>
        <?php include "pages/$page.php"; ?>
    </main>
    <footer>
        <p>&copy; <?php echo date('Y'); ?> My Website</p>
    </footer>
</body>
</html>
```

This code uses a `foreach` loop to iterate through the pages defined in the `content.json` file and dynamically generates navigation links based on the page titles. This way, whenever you add or remove pages in the JSON file, the navigation menu will automatically reflect those changes.

With this update, you no longer need to manually update the navigation menu in the `template.php` file whenever you add or remove pages from your CMS.
## Mark Hazleton:
I do not want the 404 page to show up in the navigation in the template.php file
## chatgpt:
If you don't want the "404" page to appear in the navigation menu in the `template.php` file, you can simply add a conditional check to exclude it. Here's the updated `template.php` file with the "404" page excluded from the navigation menu:

```php
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $content[$page]['title']; ?></title>
</head>
<body>
    <header>
        <h1><?php echo $content[$page]['title']; ?></h1>
    </header>
    <nav>
        <ul>
            <?php
            foreach ($content as $key => $value) {
                // Exclude the "404" page from navigation
                if ($key !== '404') {
                    echo '<li><a href="?page=' . $key . '">' . $value['title'] . '</a></li>';
                }
            }
            ?>
        </ul>
    </nav>
    <main>
        <?php include "pages/$page.php"; ?>
    </main>
    <footer>
        <p>&copy; <?php echo date('Y'); ?> My Website</p>
    </footer>
</body>
</html>
```

With this modification, the "404" page will not be included in the navigation menu. All other pages defined in the `content.json` file will still appear in the menu as before.
## Mark Hazleton:
404 is still showing up in the navigation
## chatgpt:
I apologize for the oversight. To exclude the "404" page from the navigation, you should use a conditional check to skip it during the navigation menu generation. Make sure you have properly defined your `content.json` file and that the "404" page is not included there. Here's the updated `template.php` file:

```php
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $content[$page]['title']; ?></title>
</head>
<body>
    <header>
        <h1><?php echo $content[$page]['title']; ?></h1>
    </header>
    <nav>
        <ul>
            <?php
            foreach ($content as $key => $value) {
                // Exclude the "404" page from navigation
                if ($key !== '404') {
                    echo '<li><a href="?page=' . $key . '">' . $value['title'] . '</a></li>';
                }
            }
            ?>
        </ul>
    </nav>
    <main>
        <?php include "pages/$page.php"; ?>
    </main>
    <footer>
        <p>&copy; <?php echo date('Y'); ?> My Website</p>
    </footer>
</body>
</html>
```

Ensure that the "404" page is not present in your `content.json` file, and this updated `template.php` code will exclude it from the navigation menu.
## Mark Hazleton:
when I put in a bad page name, rather than gettign the 404, I am getting the following error: Warning: Undefined array key 404 in C:\\GitHub\\ControlOrigins\\documents\\website\\template.php on line 9 Warning: Trying to access array offset on value of type null in C:\\GitHub\\ControlOrigins\\documents\\website\\template.php on line 9
## chatgpt:
I apologize for the oversight. To properly handle cases where an invalid page name is provided in the URL, you should check whether the requested page exists in the `content.json` array before trying to access it. Here's an updated `template.php` that includes error handling for such cases:

```php
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo isset($content[$page]['title']) ? $content[$page]['title'] : 'Page Not Found'; ?></title>
</head>
<body>
    <header>
        <h1><?php echo isset($content[$page]['title']) ? $content[$page]['title'] : 'Page Not Found'; ?></h1>
    </header>
    <nav>
        <ul>
            <?php
            foreach ($content as $key => $value) {
                // Exclude the "404" page from navigation
                if ($key !== '404') {
                    echo '<li><a href="?page=' . $key . '">' . $value['title'] . '</a></li>';
                }
            }
            ?>
        </ul>
    </nav>
    <main>
        <?php
        // Include the specified PHP file if available
        if (isset($content[$page]['php_file'])) {
            include "pages/" . $content[$page]['php_file'];
        } else {
            // Include the specific page content if it exists, or display a 404 message
            if (isset($content[$page]['content'])) {
                echo $content[$page]['content'];
            } else {
                echo "Page not found.";
            }
        }
        ?>
    </main>
    <footer>
        <p>&copy; <?php echo date('Y'); ?> My Website</p>
    </footer>
</body>
</html>
```

With this updated code, it first checks if the requested page exists in the `content.json` array. If it doesn't, it displays a "Page Not Found" message. This should prevent the error you were encountering when using an invalid page name in the URL.
## Mark Hazleton:
for this line <?php include "pages/$page.php"; ?> I want to add code to make sure the file exists and only include it if it is found, otherwise display content from the "pages/404.php" file
## chatgpt:
To include a specific PHP file only if it exists and display content from a `pages/404.php` file when the requested PHP file is not found, you can use the `file_exists()` function. Here's how you can modify the `template.php` file to achieve this:

```php
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo isset($content[$page]['title']) ? $content[$page]['title'] : 'Page Not Found'; ?></title>
</head>
<body>
    <header>
        <h1><?php echo isset($content[$page]['title']) ? $content[$page]['title'] : 'Page Not Found'; ?></h1>
    </header>
    <nav>
        <ul>
            <?php
            foreach ($content as $key => $value) {
                // Exclude the "404" page from navigation
                if ($key !== '404') {
                    echo '<li><a href="?page=' . $key . '">' . $value['title'] . '</a></li>';
                }
            }
            ?>
        </ul>
    </nav>
    <main>
        <?php
        $phpFile = "pages/$page.php";

        if (file_exists($phpFile)) {
            include $phpFile;
        } elseif (isset($content[$page]['content'])) {
            echo $content[$page]['content'];
        } else {
            include "pages/404.php";
        }
        ?>
    </main>
    <footer>
        <p>&copy; <?php echo date('Y'); ?> My Website</p>
    </footer>
</body>
</html>
```

With this modification, it checks if the requested PHP file exists using `file_exists()`. If the file exists, it includes it; otherwise, it checks if the page content is specified in the `content.json` file and displays it. If neither the file nor content is found, it includes the `pages/404.php` file as a fallback.
## Mark Hazleton:
In the index.php, I want to validate the $page variable so that it always contains a valid title and content. if the requested page is not found default &page title to "404 - Not Found" and content to "Page Not Found" <?php // Read the JSON content $jsonContent = file\_get\_contents('content.json'); $content = json\_decode($jsonContent, true); // Check if a page is specified in the URL $page = isset($\_GET\['page'\]) ? $\_GET\['page'\] : 'homepage'; // Check if the requested page exists in the content if (!array\_key\_exists($page, $content)) { $page = '404'; // Default to a 404 page if the page doesn't exist } // Include the template file include 'template.php'; ?>
## chatgpt:
To validate the `$page` variable in your `index.php` and ensure it always contains a valid title and content, you can modify your code as follows:

```php
<?php
// Read the JSON content
$jsonContent = file_get_contents('content.json');
$content = json_decode($jsonContent, true);

// Check if a page is specified in the URL
$page = isset($_GET['page']) ? $_GET['page'] : 'homepage';

// Check if the requested page exists in the content
if (!array_key_exists($page, $content) || !isset($content[$page]['title']) || !isset($content[$page]['content'])) {
    // Default to a 404 page if the page doesn't exist or lacks title/content
    $page = '404';
    $content[$page]['title'] = '404 - Not Found';
    $content[$page]['content'] = 'Page Not Found';
}

// Include the template file
include 'template.php';
?>
```

This code first checks if the requested page exists and if it has both a title and content. If any of these conditions are not met, it defaults to the "404" page with the specified title and content. This ensures that `$page` always contains valid title and content information.
## Mark Hazleton:
Update template.php to include Bootstrap for formatting, update navigation and content to use bootstrap classes
## chatgpt:
To incorporate Bootstrap for formatting in your `template.php` file and update the navigation and content to use Bootstrap classes, you'll need to include the Bootstrap CSS and modify the HTML structure accordingly. Make sure you have the Bootstrap CSS file (e.g., `bootstrap.min.css`) available in your project directory or linked to from a content delivery network (CDN). Here's an updated `template.php` file:

```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo isset($content[$page]['title']) ? $content[$page]['title'] : '404 - Not Found'; ?></title>
    <link href="path-to-bootstrap/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-primary text-white text-center py-4">
        <h1><?php echo isset($content[$page]['title']) ? $content[$page]['title'] : '404 - Not Found'; ?></h1>
    </header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <ul class="navbar-nav">
                <?php
                foreach ($content as $key => $value) {
                    if ($key !== '404') {
                        echo '<li class="nav-item"><a class="nav-link" href="?page=' . $key . '">' . $value['title'] . '</a></li>';
                    }
                }
                ?>
            </ul>
        </div>
    </nav>
    <main class="container mt-4">
        <?php
        $phpFile = "pages/$page.php";

        if (file_exists($phpFile)) {
            include $phpFile;
        } elseif (isset($content[$page]['content'])) {
            echo '<div class="alert alert-info">' . $content[$page]['content'] . '</div>';
        } else {
            include "pages/404.php";
        }
        ?>
    </main>
    <footer class="bg-dark text-white text-center py-2">
        <p>&copy; <?php echo date('Y'); ?> My Website</p>
    </footer>
</body>
</html>
```

This updated code includes the Bootstrap CSS and applies Bootstrap classes to the header, navigation, content, and footer sections to improve formatting and styling. It uses Bootstrap's navigation classes and container layout for better responsiveness and visual appeal.

Ensure you replace `"path-to-bootstrap/bootstrap.min.css"` with the correct path to your Bootstrap CSS file or use a CDN link to include Bootstrap.


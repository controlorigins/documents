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

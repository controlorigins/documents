<div id="readme-content">
        <?php
        // Include the Parsedown library
        require_once('Parsedown.php');

        // Specify the path to your readme.md file
        $readmeFile = 'README.md';

        // Check if the readme file exists
        if (file_exists($readmeFile)) {
            // Read the Markdown content from the file
            $markdown = file_get_contents($readmeFile);

            // Create a new Parsedown instance
            $parsedown = new Parsedown();

            // Parse Markdown to HTML and display it
            echo $parsedown->text($markdown);
        } else {
            echo '<p>Readme not found.</p>';
        }
        ?>
    </div>

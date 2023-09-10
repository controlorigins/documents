    <ul>
        <?php
        // Read the JSON file
        $jsonFile = 'projects.json';
        $data = file_get_contents($jsonFile);
        $projects = json_decode($data, true);

        // Check if JSON decoding was successful
        if ($projects !== null) {
            foreach ($projects as $project) {
                if (isset($project['p'])) {
                    echo '<li>' . $project['p'] . '</li>';
                }
            }
        } else {
            echo '<li>No projects found.</li>';
        }
        ?>
    </ul>

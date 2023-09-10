    <div class="container mt-4">
        <h1>Projects</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Link</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $jsonFile = 'projects.json';
                $jsonData = file_get_contents($jsonFile);
                $projects = json_decode($jsonData, true);

                if ($projects !== null) {
                    foreach ($projects as $project) {
                        echo '<tr>';
                        echo '<td><img src="' . $project['image'] . '" alt="' . $project['p'] . '" class="thumbnail-img img-fluid"></td>';
                        echo '<td>' . $project['p'] . '</td>';
                        echo '<td>' . $project['d'] . '</td>';
                        echo '<td><a href="' . $project['h'] . '" target="_blank">' . $project['h'] . '</a></td>';
                        echo '</tr>';
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

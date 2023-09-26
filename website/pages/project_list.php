<div class='card'>
    <div class='card-header'>
        <h1>Projects List</h1>  
        <a href='/?file=ChatGPT%252FSessions%252FCreate%2BPHP%2BProject%2BTable.md'>
        How this page was created</a>
    </div>
    <div class="card-body">

    <div class="container-fluid mt-4">
        <table class="table table-bordered display" id="myTable">
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
                $jsonFile = 'data/projects.json';
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
    <div class="card-footer">
        <br/>
        <br/>
    </div>
</div>
<br/><br/><br/><br/>


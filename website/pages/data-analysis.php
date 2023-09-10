    <h1>Select a CSV File</h1>

    <?php
    $folderPath = 'data'; // Specify the folder path

    // Check if the folder exists
    if (is_dir($folderPath)) {
        // Open the directory
        if ($dirHandle = opendir($folderPath)) {
            echo "<form method='POST' action=''>"; // Form to select a CSV file

            echo "List of CSV files in the 'data' folder:<br>";

            // Loop through the files in the directory
            while (false !== ($file = readdir($dirHandle))) {
                // Check if the file has a ".csv" extension
                if (pathinfo($file, PATHINFO_EXTENSION) === 'csv') {
                    echo "<input type='radio' name='csvFile' value='$file'> $file<br>";
                }
            }

            echo "<input type='submit' value='View Selected CSV'>";
            echo "</form>";

            // Close the directory handle
            closedir($dirHandle);
        } else {
            echo "Unable to open the 'data' folder.";
        }
    } else {
        echo "The 'data' folder does not exist.";
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $selectedFile = $_POST["csvFile"];

        if ($selectedFile) {
            // Read and display the contents of the selected CSV file
            $csvData = file_get_contents("data/" . $selectedFile);
            $lines = explode(PHP_EOL, $csvData);

            echo "<h2>Contents of $selectedFile:</h2>";
            echo "<table border='1'>";
            
            // Parse the CSV data
            $firstRow = true;
            foreach ($lines as $line) {
                echo "<tr>";
                $cells = str_getcsv($line);
                foreach ($cells as $cell) {
                    if ($firstRow) {
                        echo "<th>$cell</th>"; // Display as header
                    } else {
                        echo "<td>$cell</td>";
                    }
                }
                echo "</tr>";
                $firstRow = false;
            }

            echo "</table>";
        } else {
            echo "<p>Please select a CSV file to view.</p>";
        }
    }
    ?>

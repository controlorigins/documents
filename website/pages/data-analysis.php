    <div class="container">
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

                echo "<input type='submit' class='btn btn-primary' value='View Selected CSV'>";
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
        
                // Parse the CSV data
                $data = [];
                $header = null;
                foreach ($lines as $line) {
                    $cells = str_getcsv($line);
                    if (!$header) {
                        $header = $cells; // Store the header row
                    } else {
                        $data[] = $cells; // Store data rows
                    }
                }
        
                // Output summary for each field
                echo "<h3>Field Summary:</h3>";
                echo "<table class='table table-bordered'>";
                echo "<tr><th>Field</th><th>Minimum</th><th>Average</th><th>Maximum</th><th>Most Common</th><th>Least Common</th><th>Distinct Count</th></tr>";
        
                foreach ($header as $field) {
                    $fieldData = array_column($data, array_search($field, $header));
                    // Filter out non-integer and non-string values
                    $filteredFieldData = array_filter($fieldData, function($value) {
                        return is_string($value) || is_numeric($value);
                    });
                    $min = min($filteredFieldData);
                    $max = max($filteredFieldData);
                    $average = array_sum($filteredFieldData) / count($filteredFieldData);
                    $distinctCount = count(array_count_values($filteredFieldData));
                    $valueCounts = array_count_values($filteredFieldData);
                    arsort($valueCounts);
                    $mostCommon = key($valueCounts);
                    end($valueCounts);
                    $leastCommon = key($valueCounts);
                            
                    echo "<tr>";
                    echo "<td>$field</td>";
                    echo "<td>$min</td>";
                    echo "<td>$average</td>";
                    echo "<td>$max</td>";
                    echo "<td>$mostCommon</td>";
                    echo "<td>$leastCommon</td>";
                    echo "<td>$distinctCount</td>";
                    echo "</tr>";
                }
        
                echo "</table>";
        
                // Output the CSV data in a table
                echo "<h3>Data Table:</h3>";
                echo "<table class='table table-bordered display' id='myTable'>";
                echo "<thead><tr>";
                foreach ($header as $field) {
                    echo "<th>$field</th>";
                }
                echo "</thead></tr>";
                echo "<tbody>";
                foreach ($data as $rowData) {
                    echo "<tr>";
                    foreach ($rowData as $cell) {
                        echo "<td>$cell</td>";
                    }
                    echo "</tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<p>Please select a CSV file to view.</p>";
            }
        }
        ?>
    </div>

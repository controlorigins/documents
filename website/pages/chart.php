<?php
// Turn on error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Global directory variable
$dir = "data/";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Interactive Charting Demo</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<?php
// Function to read CSV header
function read_csv_header($file_path) {
    $file = fopen($file_path, "r");
    $header = fgetcsv($file);
    fclose($file);
    return $header;
}

// Function to read CSV data
function read_csv_data($file_path, $xField, $yField) {
    $file = fopen($file_path, "r");
    $pivotTable = [];
    $firstRow = true;
    while (($row = fgetcsv($file)) !== FALSE) {
        if ($firstRow) {
            $firstRow = false;
            continue;
        }
        $xValue = $row[$xField];
        $yValue = $row[$yField];
        if (!isset($pivotTable[$xValue][$yValue])) {
            $pivotTable[$xValue][$yValue] = 0;
        }
        $pivotTable[$xValue][$yValue]++;
    }
    fclose($file);
    return $pivotTable;
}
?>

<!-- File Selection Form -->
<form method="post" action="">
    <label for="fileSelector">Select a file to chart:</label>
    <select name="selectedFile" id="fileSelector" onchange="this.form.submit()">
        <option value="">--Select a file--</option>
        <?php
        $csvFiles = glob($dir . "*.csv");
        foreach ($csvFiles as $file) {
            $fileName = basename($file);
            $selected = ($_POST['selectedFile'] ?? '') === $fileName ? 'selected' : '';
            echo "<option value=\"$fileName\" $selected>$fileName</option>";
        }
        ?>
    </select>
</form>

<?php
if (isset($_POST['selectedFile']) && $_POST['selectedFile'] !== '') {
    echo "Debug: Selected file is " . $_POST['selectedFile'] . "<br>";  // Debugging line
    $headerRow = read_csv_header($dir . $_POST['selectedFile']);
    if ($headerRow) {
        echo "Debug: Header row read successfully<br>";  // Debugging line
        ?>
        <!-- Axis Selection Form -->
        <form method="post" action="">
            <input type="hidden" name="selectedFile" value="<?php echo $_POST['selectedFile']; ?>">
            <label for="xFieldSelector">Select X-axis field:</label>
            <select name="xField" id="xFieldSelector">
                <?php
                foreach ($headerRow as $index => $field) {
                    echo "<option value=\"$index\">$field</option>";
                }
                ?>
            </select>
            <label for="yFieldSelector">Select Y-axis field:</label>
            <select name="yField" id="yFieldSelector">
                <?php
                foreach ($headerRow as $index => $field) {
                    echo "<option value=\"$index\">$field</option>";
                }
                ?>
            </select>
            <input type="submit" value="Chart Data">
        </form>
        <?php

 
    } else {
        echo "Debug: Failed to read header row<br>";  // Debugging line
    }
}

if (isset($_POST['selectedFile']) && $_POST['selectedFile'] !== '' && isset($_POST['xField']) && isset($_POST['yField'])) {
    echo "Debug: X-Field is " . $_POST['xField'] . " and Y-Field is " . $_POST['yField'] . "<br>";  // Debugging line
    $pivotTable = read_csv_data($dir . $_POST['selectedFile'], $_POST['xField'], $_POST['yField']);
    if ($pivotTable) {
        echo "Debug: Pivot table created successfully<br>";  // Debugging line
        $labels = array_keys($pivotTable);
        $data = array_map('array_sum', $pivotTable);
    } else {
        echo "Debug: Failed to create pivot table<br>";  // Debugging line
    }
}


?>

<!-- Chart Canvas -->
<canvas id="chartCanvas"></canvas>

<script type="text/javascript">
    console.log("Debug: Labels are ", <?php echo json_encode($labels ?? []); ?>);  // Debugging line
    console.log("Debug: Data is ", <?php echo json_encode($data ?? []); ?>);  // Debugging line
    var labels = <?php echo json_encode($labels ?? []); ?>;
    var data = <?php echo json_encode($data ?? []); ?>;
    
    if (labels.length > 0 && data.length > 0) {
        var ctx = document.getElementById('chartCanvas').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Count',
                    data: data,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    x: { beginAtZero: true },
                    y: { beginAtZero: true }
                }
            }
        });
    }
</script>

</body>
</html>

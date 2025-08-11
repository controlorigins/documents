<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$response = ['summary' => [], 'stats' => [], 'error' => null];

try {
    if (!isset($_GET['file'])) {
        throw new Exception('No file specified');
    }

    $selectedFile = $_GET['file'];
    $folderPath = __DIR__ . '/../../data';
    $filePath = $folderPath . '/' . $selectedFile;

    // Validate file exists and is CSV
    if (!file_exists($filePath) || pathinfo($selectedFile, PATHINFO_EXTENSION) !== 'csv') {
        throw new Exception('Invalid CSV file');
    }

    // Read and parse the CSV file
    $csvData = file_get_contents($filePath);
    if ($csvData === false) {
        throw new Exception('Could not read CSV file');
    }

    $lines = explode(PHP_EOL, $csvData);
    $header = null;
    $data = [];

    foreach ($lines as $lineNumber => $line) {
        if (trim($line) === '') continue; // Skip empty lines
        
        $cells = str_getcsv($line, ',', '"', '\\');
        
        if ($header === null) {
            $header = $cells; // Store the header row
        } else if (count($cells) === count($header)) {
            $data[] = $cells; // Store data rows
        }
    }

    // Calculate field summaries
    $fieldSummaries = [];
    foreach ($header as $index => $field) {
        $fieldData = array_column($data, $index);
        
        // Filter out empty values
        $filteredFieldData = array_filter($fieldData, function ($value) {
            return trim($value) !== '';
        });
        
        if (empty($filteredFieldData)) {
            continue;
        }

        $min = min($filteredFieldData);
        $max = max($filteredFieldData);
        
        // Check if all values are numeric for average calculation
        $allNumeric = true;
        $numericValues = [];
        foreach ($filteredFieldData as $value) {
            if (is_numeric($value)) {
                $numericValues[] = (float)$value;
            } else {
                $allNumeric = false;
            }
        }
        
        $average = $allNumeric && !empty($numericValues) ? 
                   round(array_sum($numericValues) / count($numericValues), 2) : 'N/A';
        
        $distinctCount = count(array_count_values($filteredFieldData));
        $valueCounts = array_count_values($filteredFieldData);
        arsort($valueCounts);
        $mostCommon = key($valueCounts);
        end($valueCounts);
        $leastCommon = key($valueCounts);
        
        $fieldSummaries[] = [
            'field' => htmlspecialchars($field),
            'min' => htmlspecialchars($min),
            'max' => htmlspecialchars($max),
            'average' => $average,
            'distinctCount' => $distinctCount,
            'mostCommon' => htmlspecialchars($mostCommon),
            'leastCommon' => htmlspecialchars($leastCommon)
        ];
    }

    $response['summary'] = $fieldSummaries;
    $response['columns'] = $header; // Add columns array for chart page
    $response['stats'] = [
        'rows' => count($data),
        'columns' => count($header),
        'cells' => count($data) * count($header)
    ];

} catch (Exception $e) {
    $response['error'] = $e->getMessage();
    http_response_code(400);
}

echo json_encode($response);
?>

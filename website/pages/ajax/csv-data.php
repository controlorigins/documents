<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$response = ['data' => [], 'error' => null];

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
            // Create associative array for each row
            $rowData = [];
            for ($i = 0; $i < count($header); $i++) {
                $rowData[] = isset($cells[$i]) ? htmlspecialchars($cells[$i]) : '';
            }
            $data[] = $rowData;
        }
    }

    $response['data'] = $data;
    $response['columns'] = array_map('htmlspecialchars', $header);
    $response['recordsTotal'] = count($data);
    $response['recordsFiltered'] = count($data);

} catch (Exception $e) {
    $response['error'] = $e->getMessage();
    http_response_code(400);
}

echo json_encode($response);
?>

<?php
header('Content-Type: application/json');

try {
    // Get parameters
    $csvFile = $_GET['file'] ?? '';
    $labelColumn = $_GET['labelCol'] ?? '';
    $dataColumn = $_GET['dataCol'] ?? '';
    $limit = intval($_GET['limit'] ?? 0);
    
    if (empty($csvFile) || empty($labelColumn) || empty($dataColumn)) {
        throw new Exception('Missing required parameters');
    }
    
    // Validate file exists and is in data directory
    $filePath = __DIR__ . '/../../data/' . basename($csvFile);
    if (!file_exists($filePath)) {
        throw new Exception('CSV file not found');
    }
    
    // Read and parse CSV
    $csvData = [];
    $headers = [];
    $handle = fopen($filePath, 'r');
    
    if ($handle === false) {
        throw new Exception('Could not open CSV file');
    }
    
    // Read header row
    $headers = fgetcsv($handle, 1000, ',', '"', '\\');
    if ($headers === false) {
        throw new Exception('Could not read CSV headers');
    }
    
    // Find column indices
    $labelIndex = array_search($labelColumn, $headers);
    $dataIndex = array_search($dataColumn, $headers);
    
    if ($labelIndex === false || $dataIndex === false) {
        throw new Exception('Column not found in CSV file');
    }
    
    // Read data rows
    $rowCount = 0;
    while (($row = fgetcsv($handle, 1000, ',', '"', '\\')) !== false && ($limit === 0 || $rowCount < $limit)) {
        if (isset($row[$labelIndex]) && isset($row[$dataIndex])) {
            $label = trim($row[$labelIndex]);
            $value = trim($row[$dataIndex]);
            
            // Try to convert value to number
            if (is_numeric($value)) {
                $value = floatval($value);
            } else {
                // For non-numeric data, we'll count occurrences
                $value = 1;
            }
            
            if (!empty($label)) {
                // Group by label for aggregation
                if (isset($csvData[$label])) {
                    $csvData[$label] += $value;
                } else {
                    $csvData[$label] = $value;
                }
            }
        }
        $rowCount++;
    }
    
    fclose($handle);
    
    if (empty($csvData)) {
        throw new Exception('No valid data found in CSV file');
    }
    
    // Sort by value (descending) and limit if needed
    arsort($csvData);
    if ($limit > 0) {
        $csvData = array_slice($csvData, 0, $limit, true);
    }
    
    // Prepare chart data
    $labels = array_keys($csvData);
    $values = array_values($csvData);
    
    // Response
    echo json_encode([
        'success' => true,
        'labels' => $labels,
        'values' => $values,
        'total_rows' => count($csvData),
        'label_column' => $labelColumn,
        'data_column' => $dataColumn
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>

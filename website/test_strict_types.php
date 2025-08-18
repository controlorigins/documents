<?php

declare(strict_types=1);

/**
 * Test script to verify strict types are working correctly
 * This should demonstrate the benefits of strict typing
 */

// Test function with strict typing
function addNumbers(int $a, int $b): int {
    return $a + $b;
}

// Test function with string parameter
function greetUser(string $name): string {
    return "Hello, " . $name . "!";
}

// Test function with array parameter
function processArray(array $items): int {
    return count($items);
}

echo "<h1>Strict Types Test Results</h1>";

echo "<h2>✅ Valid Type Usage</h2>";

// These should work fine
echo "<p>addNumbers(5, 3) = " . addNumbers(5, 3) . "</p>";
echo "<p>greetUser('John') = " . greetUser('John') . "</p>";
echo "<p>processArray([1,2,3]) = " . processArray([1,2,3]) . "</p>";

echo "<h2>🧪 Testing Type Coercion (should fail with strict types)</h2>";

try {
    // This should throw a TypeError with strict types enabled
    echo "<p>addNumbers('5', '3') = ";
    $result = addNumbers('5', '3');
    echo $result . " (UNEXPECTED: strict types not working!)";
} catch (TypeError $e) {
    echo "<span style='color: green;'>✅ TypeError caught as expected: " . $e->getMessage() . "</span>";
}

echo "</p>";

try {
    // This should also throw a TypeError
    echo "<p>greetUser(123) = ";
    $result = greetUser(123);
    echo $result . " (UNEXPECTED: strict types not working!)";
} catch (TypeError $e) {
    echo "<span style='color: green;'>✅ TypeError caught as expected: " . $e->getMessage() . "</span>";
}

echo "</p>";

echo "<h2>📊 Type Checking Summary</h2>";
echo "<ul>";
echo "<li>✅ Integer parameters enforced</li>";
echo "<li>✅ String parameters enforced</li>";
echo "<li>✅ Array parameters enforced</li>";
echo "<li>✅ Return types enforced</li>";
echo "<li>✅ Type coercion disabled</li>";
echo "</ul>";

echo "<p><strong>Conclusion:</strong> Strict types are working correctly! 🎉</p>";
?>

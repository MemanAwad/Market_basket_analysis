<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to read and clean the CSV file
function readAndCleanCSV($filename) {
    $transactions = [];
    
    if (($handle = fopen($filename, 'r')) !== FALSE) {
        while (($data = fgetcsv($handle)) !== FALSE) {
            // Clean the data
            $cleanedData = array_map('trim', $data); // Remove whitespace
            $cleanedData = array_filter($cleanedData); // Remove empty values
            $cleanedData = array_unique($cleanedData); // Remove duplicates

            // Only add non-empty cleaned data
            if (!empty($cleanedData)) {
                $transactions[] = $cleanedData;
            }
        }
        fclose($handle);
    } else {
        echo "Error opening the file.";
    }

    return $transactions;
}

// Function to calculate support
function calculateSupport($transactions, $minSupport) {
    $itemCount = [];
    $totalTransactions = count($transactions);
    
    // Count occurrences of each item
    foreach ($transactions as $transaction) {
        foreach ($transaction as $item) {
            if (isset($itemCount[$item])) {
                $itemCount[$item]++;
            } else {
                $itemCount[$item] = 1;
            }
        }
    }

    // Calculate support
    $frequentItemsets = [];
    foreach ($itemCount as $item => $count) {
        $support = ($count / $totalTransactions) * 100; // Support as a percentage
        if ($support >= $minSupport) {
            $frequentItemsets[$item] = $support;
        }
    }
    
    return [$frequentItemsets, $itemCount]; // Return both frequent itemsets and itemCount
}

// Function to generate association rules
function generateAssociationRules($frequentItemsets, $itemCount, $transactions, $minConfidence) {
    $rules = [];

    // Loop over all itemsets to generate pairs
    foreach ($frequentItemsets as $item1 => $support1) {
        foreach ($frequentItemsets as $item2 => $support2) {
            if ($item1 !== $item2) {
                // Count how many transactions contain both items
                $pairCount = 0;
                foreach ($transactions as $transaction) {
                    if (in_array($item1, $transaction) && in_array($item2, $transaction)) {
                        $pairCount++;
                    }
                }

                // Calculate confidence
                if (isset($itemCount[$item1]) && $itemCount[$item1] > 0) {
                    $confidence = ($pairCount / $itemCount[$item1]) * 100; // Confidence as a percentage
                    if ($confidence >= $minConfidence) {
                        $rules[] = [
                            'rule' => "$item1 => $item2",
                            'confidence' => $confidence
                        ];
                    }
                }
            }
        }
    }

    return $rules;
}

// Load and clean the data from basket.csv
$filename = 'basket.csv';
$transactions = readAndCleanCSV($filename);

// Calculate frequent itemsets with a minimum support of 2%
$minSupport = 2;
list($frequentItemsets, $itemCount) = calculateSupport($transactions, $minSupport);

// Start HTML output for displaying tables
echo "<html><body>";

// Print supported item sets in an HTML table
echo "<h2>Supported Item Sets:</h2>";
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr><th>Item</th><th>Support (%)</th></tr>";
foreach ($frequentItemsets as $item => $support) {
    echo "<tr><td>$item</td><td>" . number_format($support, 2) . "%</td></tr>";
}
echo "</table>";

// Generate association rules with a minimum confidence of 10%
$minConfidence = 10;
$rules = generateAssociationRules($frequentItemsets, $itemCount, $transactions, $minConfidence);

// Print association rules in an HTML table
echo "<h2>Association Rules:</h2>";
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr><th>Rule</th><th>Confidence (%)</th></tr>";
foreach ($rules as $rule) {
    echo "<tr><td>If a customer buys " . explode(' => ', $rule['rule'])[0] . ", they will also buy " . explode(' => ', $rule['rule'])[1] . "</td>";
    echo "<td>" . number_format($rule['confidence'], 2) . "%</td></tr>";
}
echo "</table>";

// End HTML output
echo "</body></html>";
?>

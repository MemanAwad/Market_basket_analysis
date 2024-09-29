<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to read and clean the CSV file
function readAndCleanCSV($filename) {
    $transactions = [];
    
    if (($handle = fopen($filename, 'r')) !== FALSE) {
        while (($data = fgetcsv($handle)) !== FALSE) {
            // Assuming each row contains a list of items in the basket
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

    foreach ($frequentItemsets as $item => $support) {
        foreach ($frequentItemsets as $item2 => $support2) {
            if ($item !== $item2) {
                // Count occurrences of item pairs
                $pairCount = 0;
                foreach ($transactions as $transaction) {
                    if (in_array($item, $transaction) && in_array($item2, $transaction)) {
                        $pairCount++;
                    }
                }

                // Calculate confidence
                if (isset($itemCount[$item]) && $itemCount[$item] > 0) {
                    $confidence = ($pairCount / $itemCount[$item]) * 100; // Confidence as a percentage
                    if ($confidence >= $minConfidence) {
                        $rules[] = [
                            'rule' => "$item => $item2",
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

// Print supported item sets
echo "\nSupported Item Sets:\n";
echo "+-------------------+-------------------+\n";
echo "| Item              | Support (%)       |\n";
echo "+-------------------+-------------------+\n";
foreach ($frequentItemsets as $item => $support) {
    printf("| %-17s | %-17.2f |\n", $item, $support);
}
echo "+-------------------+-------------------+\n";

// Generate association rules with a minimum confidence of 50%
$minConfidence = 20;
$rules = generateAssociationRules($frequentItemsets, $itemCount, $transactions, $minConfidence);

// Print association rules
echo "\nAssociation Rules:\n";
echo "+-------------------+-------------------+\n";
echo "| Rule              | Confidence (%)    |\n";
echo "+-------------------+-------------------+\n";
foreach ($rules as $rule) {
    printf("| %-17s | %-17.2f |\n", $rule['rule'], $rule['confidence']);
}
echo "+-------------------+-------------------+\n";
?>

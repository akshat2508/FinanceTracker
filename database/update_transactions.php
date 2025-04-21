<?php
require_once __DIR__ . '/../config/database.php';

// Add subcategory column to transactions table
$sql = "SHOW COLUMNS FROM transactions LIKE 'subcategory'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    $sql = "ALTER TABLE transactions ADD COLUMN subcategory VARCHAR(50) DEFAULT NULL";
    if ($conn->query($sql)) {
        echo "Added subcategory column to transactions table successfully\n";
    } else {
        echo "Error adding subcategory column: " . $conn->error . "\n";
    }
} else {
    echo "Column subcategory already exists in transactions table\n";
}

echo "Database update completed\n";
?>

<?php
require_once __DIR__ . '/../config/database.php';

// Create subcategories table
$sql = "CREATE TABLE IF NOT EXISTS subcategories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    category VARCHAR(50) NOT NULL,
    subcategory VARCHAR(50) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    UNIQUE KEY unique_subcategory (user_id, category, subcategory)
)";

if ($conn->query($sql)) {
    echo "Subcategories table created successfully\n";
} else {
    echo "Error creating subcategories table: " . $conn->error . "\n";
}

// Add new columns to budget_goals table
$columns = [
    "subcategory VARCHAR(50) DEFAULT NULL",
    "year INT NOT NULL DEFAULT YEAR(CURRENT_DATE)",
    "description TEXT",
    "alert_threshold INT DEFAULT 80",
    "is_recurring BOOLEAN DEFAULT FALSE",
    "recurring_type ENUM('monthly', 'quarterly', 'yearly') DEFAULT 'monthly'",
    "last_notification_date DATE DEFAULT NULL"
];

foreach ($columns as $column) {
    $columnName = explode(" ", $column)[0];
    $sql = "SHOW COLUMNS FROM budget_goals LIKE '$columnName'";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 0) {
        $sql = "ALTER TABLE budget_goals ADD COLUMN $column";
        if ($conn->query($sql)) {
            echo "Added column $columnName successfully\n";
        } else {
            echo "Error adding column $columnName: " . $conn->error . "\n";
        }
    } else {
        echo "Column $columnName already exists\n";
    }
}

echo "Database update completed\n";
?>

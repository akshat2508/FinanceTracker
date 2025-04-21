<?php
require_once __DIR__ . '/../config/database.php';

// Update budget_goals table structure
$sql = "ALTER TABLE budget_goals 
        DROP COLUMN IF EXISTS alert_threshold,
        DROP COLUMN IF EXISTS description,
        DROP COLUMN IF EXISTS is_recurring,
        DROP COLUMN IF EXISTS recurring_type,
        DROP COLUMN IF EXISTS last_notification_date,
        ADD COLUMN IF NOT EXISTS period_type ENUM('weekly', 'monthly', 'yearly') NOT NULL DEFAULT 'monthly',
        ADD COLUMN IF NOT EXISTS start_date DATE NOT NULL,
        ADD COLUMN IF NOT EXISTS end_date DATE NOT NULL";

if ($conn->query($sql)) {
    echo "Updated budget_goals table structure successfully\n";
} else {
    echo "Error updating budget_goals table: " . $conn->error . "\n";
}

echo "Database update completed\n";
?>

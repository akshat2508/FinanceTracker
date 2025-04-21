<?php
require_once 'config/database.php';

$query = "SELECT COUNT(*) as user_count FROM users";
$result = $conn->query($query);

if ($result) {
    $row = $result->fetch_assoc();
    echo "Database connection successful!\n";
    echo "Number of users in database: " . $row['user_count'];
} else {
    echo "Connection failed: " . $conn->error;
}
?>

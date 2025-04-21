<?php
require_once __DIR__ . '/../config/database.php';

// First, clear all existing data
$conn->query("DELETE FROM budget_goals");
$conn->query("DELETE FROM transactions");
$conn->query("DELETE FROM users");

// Reset auto-increment
$conn->query("ALTER TABLE users AUTO_INCREMENT = 1");
$conn->query("ALTER TABLE transactions AUTO_INCREMENT = 1");
$conn->query("ALTER TABLE budget_goals AUTO_INCREMENT = 1");

// Admin user data
$adminUsername = "admin";
$adminEmail = "admin@example.com";
$adminPassword = password_hash("admin123", PASSWORD_DEFAULT);
$adminRole = "admin";

// Insert admin user with balance_set as TRUE
$sql = "INSERT INTO users (username, email, password, role, balance_set) VALUES (?, ?, ?, ?, TRUE)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $adminUsername, $adminEmail, $adminPassword, $adminRole);

if ($stmt->execute()) {
    echo "Admin user created successfully!\n";
} else {
    echo "Error creating admin user: " . $conn->error . "\n";
}

$conn->close();
?>

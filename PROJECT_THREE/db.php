<?php
// php/db.php
$conn = new mysqli("localhost", "root", "", "hotel");

if ($conn->connect_error) {
    // FIX: log the error instead of exposing it to users
    error_log("DB connection failed: " . $conn->connect_error);
    die("Service temporarily unavailable. Please try again later.");
}

// Set charset to prevent encoding issues
$conn->set_charset("utf8mb4");
?>

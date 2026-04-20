<?php
// php/contact.php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: contact.html");
    exit();
}

// FIX: field name was "emails" in original HTML — now correctly "email"
$name     = trim($_POST['name']);
$email    = trim($_POST['email']);   // FIX: was $_POST['email'] but form had name="emails"
$phone    = trim($_POST['phone']);
$location = trim($_POST['location']);
$message  = trim($_POST['message']);

// FIX: use prepared statement — no more SQL injection
$stmt = $conn->prepare(
    "INSERT INTO contacts (name, email, phone, location, message) VALUES (?, ?, ?, ?, ?)"
);
$stmt->bind_param("sssss", $name, $email, $phone, $location, $message);

if ($stmt->execute()) {
    header("Location: contact.html?success=1");
    exit();
} else {
    error_log("Contact insert failed: " . $stmt->error);
    header("Location: contact.html?error=1");
    exit();
}
?>

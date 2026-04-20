<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /hotel_website/register.html");
    exit();
}

$username       = trim($_POST['username']);
$password       = $_POST['password'];
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    header("Location: /hotel_website/register.html?error=exists");
    exit();
}

$stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'customer')");
$stmt->bind_param("ss", $username, $hashedPassword);
if ($stmt->execute()) {
    header("Location: /hotel_website/login.html?registered=1");
    exit();
} else {
    header("Location: /hotel_website/register.html?error=fail");
    exit();
}
?>
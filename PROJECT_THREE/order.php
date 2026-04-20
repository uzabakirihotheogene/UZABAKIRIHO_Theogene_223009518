<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /THEOGENE_RELAX_HOTEL/order.html");
    exit();
}

$name    = trim($_POST['name']);
$email   = trim($_POST['email']);
$phone   = trim($_POST['phone']);
$menu    = trim($_POST['menu']);
$address = trim($_POST['address']);
$date    = trim($_POST['date']);
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;

$stmt = $conn->prepare(
    "INSERT INTO orders (name, email, phone, menu, address, date, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param("ssssssi", $name, $email, $phone, $menu, $address, $date, $user_id);

if ($stmt->execute()) {
    if (isset($_SESSION['user']) && $_SESSION['role'] === 'customer') {
        header("Location: /THEOGENE_RELAX_HOTEL/my_account.php?tab=orders&success=1");
    } else {
        header("Location: /THEOGENE_RELAX_HOTEL/order.html?success=1");
    }
    exit();
} else {
    header("Location: /THEOGENE_RELAX_HOTEL/order.html?error=1");
    exit();
}
?>
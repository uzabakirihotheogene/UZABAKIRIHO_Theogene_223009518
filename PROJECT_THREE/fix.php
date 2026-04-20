<?php
include 'C:/xampp1/htdocs/THEOGENE_RELAX_HOTEL/php/db.php';
$hash = password_hash("1234", PASSWORD_DEFAULT);
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = 'mucyo'");
$stmt->bind_param("s", $hash);
$stmt->execute();
echo "Done! Now login with password: 1234";
?>
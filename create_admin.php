<?php

require_once "database/config.php";

$fullname = "Administrator";
$username = "admin";
$password = "admin123";
$role = "Admin";

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$check = $conn->prepare("SELECT id FROM users WHERE username = ?");
$check->execute([$username]);

if ($check->rowCount() > 0) {
    echo "Admin user already exists.";
    exit();
}

$sql = $conn->prepare("
    INSERT INTO users(fullname, username, password, role)
    VALUES(?, ?, ?, ?)
");

if ($sql->execute([$fullname, $username, $hashedPassword, $role])) {
    echo "Admin user created successfully.<br><br>";
    echo "Username : admin<br>";
    echo "Password : admin123";
} else {
    echo "Failed to create admin user.";
}

?>
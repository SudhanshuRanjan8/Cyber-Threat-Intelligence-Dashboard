<?php
// ==========================================
// Database Configuration
// Project: Cyber Threat Intelligence Dashboard
// ==========================================

$host = "localhost";
$dbname = "cti_dashboard";
$username = "root";
$password = "";

try {
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $username,
        $password
    );

    // Enable Exceptions
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch associative array by default
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch(PDOException $e) {

    die("Database Connection Failed : " . $e->getMessage());

}
?>
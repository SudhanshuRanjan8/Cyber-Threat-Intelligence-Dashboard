<?php

session_start();

require_once "../database/config.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: index.php");
    exit();
}

$username = trim($_POST['username']);
$password = trim($_POST['password']);

if (empty($username) || empty($password)) {

    $_SESSION['error'] = "Please enter Username and Password.";

    header("Location: index.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

$ip = $_SERVER['REMOTE_ADDR'];

if ($user && password_verify($password, $user['password'])) {

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['fullname'] = $user['fullname'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    $log = $conn->prepare("
        INSERT INTO login_logs(username, ip_address, status)
        VALUES(?, ?, ?)
    ");

    $log->execute([
        $username,
        $ip,
        "Success"
    ]);

    $activity = $conn->prepare("
        INSERT INTO activity_logs(username, activity, ip_address)
        VALUES(?, ?, ?)
    ");

    $activity->execute([
        $username,
        "Logged into the system",
        $ip
    ]);

    header("Location: ../dashboard/dashboard.php");
    exit();

} else {

    $log = $conn->prepare("
        INSERT INTO login_logs(username, ip_address, status)
        VALUES(?, ?, ?)
    ");

    $log->execute([
        $username,
        $ip,
        "Failed"
    ]);

    $_SESSION['error'] = "Invalid Username or Password.";

    header("Location: index.php");
    exit();
}

?>
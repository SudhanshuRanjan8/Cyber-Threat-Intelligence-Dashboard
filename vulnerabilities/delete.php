<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/index.php");
    exit();
}

require_once "../database/config.php";

if (!isset($_GET['id'])) {
    header("Location: view.php");
    exit();
}

$id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT id FROM vulnerabilities WHERE id=?");
$stmt->execute([$id]);

if ($stmt->rowCount() == 0) {
    header("Location: view.php");
    exit();
}

$delete = $conn->prepare("DELETE FROM vulnerabilities WHERE id=?");
$delete->execute([$id]);

header("Location:view.php?deleted=1");
exit();

?>
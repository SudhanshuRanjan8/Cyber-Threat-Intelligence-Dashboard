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

$check = $conn->prepare("SELECT id FROM threats WHERE id = ?");
$check->execute([$id]);

if ($check->rowCount() == 0) {
    header("Location: view.php");
    exit();
}

$delete = $conn->prepare("DELETE FROM threats WHERE id = ?");
$delete->execute([$id]);

header("Location: view.php?deleted=1");
exit();

?>
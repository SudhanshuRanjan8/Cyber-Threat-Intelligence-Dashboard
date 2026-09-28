<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/index.php");
    exit();
}

require_once "../database/config.php";

if(!isset($_GET['id']))
{
    header("Location:view.php");
    exit();
}

$id=(int)$_GET['id'];

$stmt=$conn->prepare("
UPDATE alerts
SET status='Resolved'
WHERE id=?
");

$stmt->execute([$id]);

header("Location:view.php?resolved=1");
exit();

?>
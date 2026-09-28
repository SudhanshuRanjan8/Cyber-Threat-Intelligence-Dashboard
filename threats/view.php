<?php

session_start();

if(!isset($_SESSION['user_id']))
{
    header("Location: ../login/index.php");
    exit();
}

require_once "../database/config.php";

$threats = $conn->query("
SELECT *
FROM threats
ORDER BY detected_on DESC
")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Threat Management</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<link rel="stylesheet" href="../assets/css/dashboard.css">

</head>

<body>

<?php include("../includes/sidebar.php"); ?>

<div class="main">

<?php include("../includes/navbar.php"); ?>

<div class="content">

<div class="d-flex justify-content-between mb-3">

<h3>

Threat Management

</h3>

<a href="add.php" class="btn btn-primary">

<i class="fa-solid fa-plus"></i>

Add Threat

</a>

</div>

<?php

if(isset($_GET['success']))
{
?>

<div class="alert alert-success">
    Threat added successfully.
</div>

<?php
}

if(isset($_GET['updated']))
{
?>

<div class="alert alert-warning">
    Threat updated successfully.
</div>

<?php
}

if(isset($_GET['deleted']))
{
?>

<div class="alert alert-danger">
    Threat deleted successfully.
</div>

<?php
}
?>

<div class="card shadow">

<div class="card-body">

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>ID</th>

<th>Name</th>

<th>Type</th>

<th>Severity</th>

<th>Source IP</th>

<th>Destination IP</th>

<th>Status</th>

<th>Date</th>

<th width="170">

Action

</th>

</tr>

</thead>

<tbody>

<?php

foreach($threats as $row)
{

?>

<tr>

<td>

<?php echo $row['id']; ?>

</td>

<td>

<?php echo htmlspecialchars($row['threat_name']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['threat_type']); ?>

</td>

<td>

<?php echo $row['severity']; ?>

</td>

<td>

<?php echo htmlspecialchars($row['source_ip']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['destination_ip']); ?>

</td>

<td>

<?php echo $row['status']; ?>

</td>

<td>

<?php echo $row['detected_on']; ?>

</td>

<td>

<a
href="edit.php?id=<?php echo $row['id']; ?>"
class="btn btn-warning btn-sm">

<i class="fa-solid fa-pen"></i>

</a>

<a
href="delete.php?id=<?php echo $row['id']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this threat?');">

<i class="fa-solid fa-trash"></i>

</a>

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

</div>

</div>

</div>

</body>

</html>
<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/index.php");
    exit();
}

require_once "../database/config.php";

$stmt = $conn->query("
SELECT *
FROM alerts
ORDER BY created_at DESC
");

$alerts = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Alert Management</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<link rel="stylesheet" href="../assets/css/dashboard.css">

</head>

<body>

<?php include("../includes/sidebar.php"); ?>

<div class="main">

<?php include("../includes/navbar.php"); ?>

<div class="content">

<h3 class="mb-4">

<i class="fa-solid fa-bell"></i>

Alert Management

</h3>

<?php

if(isset($_GET['resolved']))
{

echo '<div class="alert alert-success">
Alert marked as Resolved.
</div>';

}

?>

<div class="card shadow">

<div class="card-body">

<table class="table table-bordered table-hover align-middle">

<thead class="table-dark">

<tr>

<th>ID</th>

<th>Title</th>

<th>Message</th>

<th>Severity</th>

<th>Source</th>

<th>Status</th>

<th>Date</th>

<th>Action</th>

</tr>

</thead>

<tbody>

<?php

if(count($alerts)>0)
{

foreach($alerts as $row)
{

?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo htmlspecialchars($row['alert_title']); ?></td>

<td><?php echo htmlspecialchars($row['alert_message']); ?></td>

<td>

<?php

switch($row['severity'])
{

case "Critical":

echo '<span class="badge bg-danger">Critical</span>';

break;

case "High":

echo '<span class="badge bg-warning text-dark">High</span>';

break;

case "Medium":

echo '<span class="badge bg-primary">Medium</span>';

break;

default:

echo '<span class="badge bg-success">Low</span>';

}

?>

</td>

<td>

<?php echo $row['source_module']; ?>

</td>

<td>

<?php

if($row['status']=="Resolved")
{

echo '<span class="badge bg-success">Resolved</span>';

}

else

{

echo '<span class="badge bg-danger">Active</span>';

}

?>

</td>

<td>

<?php echo $row['created_at']; ?>

</td>

<td>

<?php

if($row['status']=="Active")
{

?>

<a
href="resolve.php?id=<?php echo $row['id']; ?>"
class="btn btn-success btn-sm">

<i class="fa-solid fa-check"></i>

Resolve

</a>

<?php

}

else

{

echo "-";

}

?>

</td>

</tr>

<?php

}

}

else

{

?>

<tr>

<td colspan="8" class="text-center">

No Alerts Available

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
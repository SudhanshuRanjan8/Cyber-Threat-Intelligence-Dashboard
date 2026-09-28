<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/index.php");
    exit();
}

require_once "../database/config.php";

$stmt = $conn->query("SELECT * FROM iocs ORDER BY created_at DESC");
$iocs = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>IOC Management</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<link rel="stylesheet" href="../assets/css/dashboard.css">

</head>

<body>

<?php include("../includes/sidebar.php"); ?>

<div class="main">

<?php include("../includes/navbar.php"); ?>

<div class="content">

<div class="d-flex justify-content-between align-items-center mb-3">

<h3>
<i class="fa-solid fa-network-wired"></i>
IOC Management
</h3>

<a href="add.php" class="btn btn-primary">
<i class="fa-solid fa-plus"></i>
Add IOC
</a>

</div>

<?php

if(isset($_GET['success']))
{
?>

<div class="alert alert-success alert-dismissible fade show" role="alert">
IOC added successfully.
<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>

<?php
}

if(isset($_GET['updated']))
{
?>

<div class="alert alert-warning alert-dismissible fade show" role="alert">
IOC updated successfully.
<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>

<?php
}

if(isset($_GET['deleted']))
{
?>

<div class="alert alert-danger alert-dismissible fade show" role="alert">
IOC deleted successfully.
<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>

<?php
}

?>

<div class="card shadow">

<div class="card-body">

<table class="table table-bordered table-hover align-middle">

<thead class="table-dark">

<tr>

<th>ID</th>
<th>Type</th>
<th>Indicator</th>
<th>Threat Level</th>
<th>Source</th>
<th>Status</th>
<th>Created</th>
<th width="120">Action</th>

</tr>

</thead>

<tbody>

<?php if(count($iocs) > 0){ ?>

<?php foreach($iocs as $ioc){ ?>

<tr>

<td><?php echo $ioc['id']; ?></td>

<td><?php echo htmlspecialchars($ioc['indicator_type']); ?></td>

<td><?php echo htmlspecialchars($ioc['indicator_value']); ?></td>

<td><?php echo htmlspecialchars($ioc['threat_level']); ?></td>

<td><?php echo htmlspecialchars($ioc['source']); ?></td>

<td><?php echo htmlspecialchars($ioc['status']); ?></td>

<td><?php echo $ioc['created_at']; ?></td>

<td>

<a href="edit.php?id=<?php echo $ioc['id']; ?>" class="btn btn-warning btn-sm">
<i class="fa-solid fa-pen"></i>
</a>

<a href="delete.php?id=<?php echo $ioc['id']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this IOC?')">
<i class="fa-solid fa-trash"></i>
</a>

</td>

</tr>

<?php } ?>

<?php } else { ?>

<tr>

<td colspan="8" class="text-center text-muted">

No IOC records found.

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
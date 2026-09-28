<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/index.php");
    exit();
}

require_once "../database/config.php";

$stmt = $conn->query("SELECT * FROM vulnerabilities ORDER BY detected_on DESC");
$vulnerabilities = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Vulnerability Management</title>

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

<i class="fa-solid fa-bug"></i>

Vulnerability Management

</h3>

<a href="add.php" class="btn btn-danger">

<i class="fa-solid fa-plus"></i>

Add Vulnerability

</a>

</div>

<?php

if(isset($_GET['success']))
{
    echo '<div class="alert alert-success alert-dismissible fade show">
            Vulnerability added successfully.
            <button class="btn-close" data-bs-dismiss="alert"></button>
          </div>';
}

if(isset($_GET['updated']))
{
    echo '<div class="alert alert-warning alert-dismissible fade show">
            Vulnerability updated successfully.
            <button class="btn-close" data-bs-dismiss="alert"></button>
          </div>';
}

if(isset($_GET['deleted']))
{
    echo '<div class="alert alert-danger alert-dismissible fade show">
            Vulnerability deleted successfully.
            <button class="btn-close" data-bs-dismiss="alert"></button>
          </div>';
}

?>

<div class="card shadow">

<div class="card-body">

<table class="table table-bordered table-hover align-middle">

<thead class="table-dark">

<tr>

<th>ID</th>

<th>CVE ID</th>

<th>Name</th>

<th>CVSS</th>

<th>Severity</th>

<th>Software</th>

<th>Detected On</th>

<th width="120">Action</th>

</tr>

</thead>

<tbody>

<?php if(count($vulnerabilities)>0){ ?>

<?php foreach($vulnerabilities as $row){ ?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo htmlspecialchars($row['cve_id']); ?></td>

<td><?php echo htmlspecialchars($row['vulnerability_name']); ?></td>

<td><?php echo $row['cvss_score']; ?></td>

<td><?php echo $row['severity']; ?></td>

<td><?php echo htmlspecialchars($row['affected_software']); ?></td>

<td><?php echo $row['detected_on']; ?></td>

<td>

<a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">

<i class="fa-solid fa-pen"></i>

</a>

<a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm"
onclick="return confirm('Delete this vulnerability?')">

<i class="fa-solid fa-trash"></i>

</a>

</td>

</tr>

<?php } ?>

<?php } else { ?>

<tr>

<td colspan="8" class="text-center">

No vulnerabilities found.

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
<?php

session_start();

// Uncomment when session protection is required
// if (!isset($_SESSION['user_id'])) {
//     header("Location: ../login/index.php");
//     exit();
// }

require_once "../database/config.php";

if(isset($_POST['save']))
{

    $threat_name = trim($_POST['threat_name']);
    $threat_type = trim($_POST['threat_type']);
    $severity = $_POST['severity'];
    $source_ip = trim($_POST['source_ip']);
    $destination_ip = trim($_POST['destination_ip']);
    $status = $_POST['status'];
    $description = trim($_POST['description']);

    $sql = $conn->prepare("
        INSERT INTO threats
        (
            threat_name,
            threat_type,
            severity,
            source_ip,
            destination_ip,
            status,
            description
        )
        VALUES
        (
            ?,?,?,?,?,?,?
        )
    ");

    if($sql->execute([
        $threat_name,
        $threat_type,
        $severity,
        $source_ip,
        $destination_ip,
        $status,
        $description
    ]))
    {

        // ==========================
        // AUTO ALERT GENERATION
        // ==========================

        if($severity == "Critical")
        {

            $alert = $conn->prepare("
                INSERT INTO alerts
                (
                    alert_title,
                    alert_message,
                    severity,
                    source_module
                )
                VALUES
                (
                    ?,?,?,?
                )
            ");

            $alert_title = "Critical Threat Detected";

            $alert_message = "Threat: ".$threat_name.
                             " | Type: ".$threat_type.
                             " | Source IP: ".$source_ip;

            $alert->execute([
                $alert_title,
                $alert_message,
                "Critical",
                "Threat"
            ]);

        }

        header("Location: view.php?success=1");
        exit();

    }

}

?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Add Threat</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h3>

<i class="fa-solid fa-shield-virus"></i>

Add New Threat

</h3>

</div>

<div class="card-body">

<form method="POST">

<div class="row">

<div class="col-md-6 mb-3">

<label>Threat Name</label>

<input
type="text"
name="threat_name"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label>Threat Type</label>

<input
type="text"
name="threat_type"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label>Severity</label>

<select
name="severity"
class="form-select">

<option>Low</option>
<option>Medium</option>
<option>High</option>
<option>Critical</option>

</select>

</div>

<div class="col-md-6 mb-3">

<label>Status</label>

<select
name="status"
class="form-select">

<option>Active</option>
<option>Resolved</option>

</select>

</div>

<div class="col-md-6 mb-3">

<label>Source IP</label>

<input
type="text"
name="source_ip"
class="form-control">

</div>

<div class="col-md-6 mb-3">

<label>Destination IP</label>

<input
type="text"
name="destination_ip"
class="form-control">

</div>

<div class="col-12 mb-3">

<label>Description</label>

<textarea
name="description"
class="form-control"
rows="4"></textarea>

</div>

<div class="col-12">

<button
type="submit"
name="save"
class="btn btn-primary">

<i class="fa-solid fa-floppy-disk"></i>

Save Threat

</button>

<a
href="view.php"
class="btn btn-secondary">

View Threats

</a>

</div>

</div>

</form>

</div>

</div>

</div>

</body>

</html>
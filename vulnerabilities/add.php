<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/index.php");
    exit();
}

require_once "../database/config.php";

if(isset($_POST['save']))
{

    $cve = trim($_POST['cve_id']);
    $name = trim($_POST['vulnerability_name']);
    $cvss = $_POST['cvss_score'];
    $severity = $_POST['severity'];
    $software = trim($_POST['affected_software']);
    $solution = trim($_POST['solution']);
    $description = trim($_POST['description']);

    $stmt = $conn->prepare("
        INSERT INTO vulnerabilities
        (
            cve_id,
            vulnerability_name,
            cvss_score,
            severity,
            affected_software,
            solution,
            description
        )
        VALUES
        (
            ?,?,?,?,?,?,?
        )
    ");

    if($stmt->execute([
        $cve,
        $name,
        $cvss,
        $severity,
        $software,
        $solution,
        $description
    ]))
    {

        // ==========================
        // AUTO ALERT GENERATION
        // ==========================

        if($cvss >= 9.0)
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

            $alert_title = "Critical Vulnerability Detected";

            $alert_message = $cve .
                             " | " .
                             $name .
                             " | CVSS Score: " .
                             $cvss;

            $alert->execute([
                $alert_title,
                $alert_message,
                "Critical",
                "Vulnerability"
            ]);

        }

        header("Location:view.php?success=1");
        exit();

    }

}

?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Add Vulnerability</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<link rel="stylesheet" href="../assets/css/dashboard.css">

</head>

<body>

<?php include("../includes/sidebar.php"); ?>

<div class="main">

<?php include("../includes/navbar.php"); ?>

<div class="content">

<div class="card shadow">

<div class="card-header bg-danger text-white">

<h3>

<i class="fa-solid fa-bug"></i>

Add Vulnerability

</h3>

</div>

<div class="card-body">

<form method="POST">

<div class="row">

<div class="col-md-6 mb-3">

<label>CVE ID</label>

<input
type="text"
name="cve_id"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label>Vulnerability Name</label>

<input
type="text"
name="vulnerability_name"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label>CVSS Score</label>

<input
type="number"
step="0.1"
min="0"
max="10"
name="cvss_score"
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

<label>Affected Software</label>

<input
type="text"
name="affected_software"
class="form-control">

</div>

<div class="col-12 mb-3">

<label>Description</label>

<textarea
name="description"
rows="4"
class="form-control"></textarea>

</div>

<div class="col-12 mb-3">

<label>Solution</label>

<textarea
name="solution"
rows="4"
class="form-control"></textarea>

</div>

<div class="col-12">

<button
type="submit"
name="save"
class="btn btn-danger">

<i class="fa-solid fa-floppy-disk"></i>

Save Vulnerability

</button>

<a
href="view.php"
class="btn btn-secondary">

View Vulnerabilities

</a>

</div>

</div>

</form>

</div>

</div>

</div>

</div>

</body>

</html>
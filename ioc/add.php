<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/index.php");
    exit();
}

require_once "../database/config.php";

if(isset($_POST['save']))
{

    $indicator_type = $_POST['indicator_type'];
    $indicator_value = trim($_POST['indicator_value']);
    $threat_level = $_POST['threat_level'];
    $source = trim($_POST['source']);
    $status = $_POST['status'];
    $description = trim($_POST['description']);

    $sql = $conn->prepare("
        INSERT INTO iocs
        (
            indicator_type,
            indicator_value,
            threat_level,
            source,
            status,
            description
        )
        VALUES
        (
            ?,?,?,?,?,?
        )
    ");

    if($sql->execute([
        $indicator_type,
        $indicator_value,
        $threat_level,
        $source,
        $status,
        $description
    ]))
    {

        // ==========================
        // AUTO ALERT GENERATION
        // ==========================

        if($threat_level == "High" || $threat_level == "Critical")
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

            $alert_title = "High Risk IOC Detected";

            $alert_message = $indicator_type . " : " . $indicator_value;

            $alert->execute([
                $alert_title,
                $alert_message,
                $threat_level,
                "IOC"
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

<title>Add IOC</title>

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

<div class="card-header bg-primary text-white">

<h3>

<i class="fa-solid fa-network-wired"></i>

Add IOC

</h3>

</div>

<div class="card-body">

<form method="POST">

<div class="row">

<div class="col-md-6 mb-3">

<label>Indicator Type</label>

<select name="indicator_type" class="form-select">
<option>IP Address</option>
<option>Domain</option>
<option>URL</option>
<option>Hash</option>
</select>

</div>

<div class="col-md-6 mb-3">

<label>Indicator Value</label>

<input type="text" name="indicator_value" class="form-control" required>

</div>

<div class="col-md-6 mb-3">

<label>Threat Level</label>

<select name="threat_level" class="form-select">
<option>Low</option>
<option>Medium</option>
<option>High</option>
<option>Critical</option>
</select>

</div>

<div class="col-md-6 mb-3">

<label>Source</label>

<input type="text" name="source" class="form-control">

</div>

<div class="col-md-6 mb-3">

<label>Status</label>

<select name="status" class="form-select">
<option>Active</option>
<option>Inactive</option>
</select>

</div>

<div class="col-12 mb-3">

<label>Description</label>

<textarea name="description" class="form-control" rows="4"></textarea>

</div>

<div class="col-12">

<button type="submit" name="save" class="btn btn-primary">

<i class="fa-solid fa-floppy-disk"></i>

Save IOC

</button>

<a href="view.php" class="btn btn-secondary">

View IOC

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
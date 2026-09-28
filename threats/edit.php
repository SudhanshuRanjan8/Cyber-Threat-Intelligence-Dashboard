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

$id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM threats WHERE id = ?");
$stmt->execute([$id]);

$threat = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$threat) {
    header("Location: view.php");
    exit();
}

if (isset($_POST['update'])) {

    $threat_name = trim($_POST['threat_name']);
    $threat_type = trim($_POST['threat_type']);
    $severity = $_POST['severity'];
    $source_ip = trim($_POST['source_ip']);
    $destination_ip = trim($_POST['destination_ip']);
    $status = $_POST['status'];
    $description = trim($_POST['description']);

    $update = $conn->prepare("
        UPDATE threats
        SET
            threat_name=?,
            threat_type=?,
            severity=?,
            source_ip=?,
            destination_ip=?,
            status=?,
            description=?
        WHERE id=?
    ");

    $update->execute([
        $threat_name,
        $threat_type,
        $severity,
        $source_ip,
        $destination_ip,
        $status,
        $description,
        $id
    ]);

    header("Location: view.php?updated=1");
    exit();
}

?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Edit Threat</title>

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

<div class="card-header bg-warning">

<h3>Edit Threat</h3>

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
value="<?php echo htmlspecialchars($threat['threat_name']); ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label>Threat Type</label>

<input
type="text"
name="threat_type"
class="form-control"
value="<?php echo htmlspecialchars($threat['threat_type']); ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label>Severity</label>

<select
name="severity"
class="form-select">

<?php

$levels = ["Low","Medium","High","Critical"];

foreach($levels as $level){

$selected = ($threat['severity'] == $level) ? "selected" : "";

echo "<option value='$level' $selected>$level</option>";

}

?>

</select>

</div>

<div class="col-md-6 mb-3">

<label>Status</label>

<select
name="status"
class="form-select">

<option value="Active" <?php if($threat['status']=="Active") echo "selected"; ?>>

Active

</option>

<option value="Resolved" <?php if($threat['status']=="Resolved") echo "selected"; ?>>

Resolved

</option>

</select>

</div>

<div class="col-md-6 mb-3">

<label>Source IP</label>

<input
type="text"
name="source_ip"
class="form-control"
value="<?php echo htmlspecialchars($threat['source_ip']); ?>">

</div>

<div class="col-md-6 mb-3">

<label>Destination IP</label>

<input
type="text"
name="destination_ip"
class="form-control"
value="<?php echo htmlspecialchars($threat['destination_ip']); ?>">

</div>

<div class="col-12 mb-3">

<label>Description</label>

<textarea
name="description"
rows="4"
class="form-control"><?php echo htmlspecialchars($threat['description']); ?></textarea>

</div>

<div class="col-12">

<button
type="submit"
name="update"
class="btn btn-success">

<i class="fa-solid fa-floppy-disk"></i>

Update Threat

</button>

<a
href="view.php"
class="btn btn-secondary">

Back

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
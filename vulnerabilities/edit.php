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

$stmt = $conn->prepare("SELECT * FROM vulnerabilities WHERE id=?");
$stmt->execute([$id]);

$vulnerability = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vulnerability) {
    header("Location: view.php");
    exit();
}

if (isset($_POST['update'])) {

    $cve_id = $_POST['cve_id'];
    $name = $_POST['vulnerability_name'];
    $cvss = $_POST['cvss_score'];
    $severity = $_POST['severity'];
    $software = $_POST['affected_software'];
    $solution = $_POST['solution'];
    $description = $_POST['description'];

    $update = $conn->prepare("
        UPDATE vulnerabilities
        SET
            cve_id=?,
            vulnerability_name=?,
            cvss_score=?,
            severity=?,
            affected_software=?,
            solution=?,
            description=?
        WHERE id=?
    ");

    $update->execute([
        $cve_id,
        $name,
        $cvss,
        $severity,
        $software,
        $solution,
        $description,
        $id
    ]);

    header("Location:view.php?updated=1");
    exit();
}

?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Edit Vulnerability</title>

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

<h3>Edit Vulnerability</h3>

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
value="<?php echo htmlspecialchars($vulnerability['cve_id']); ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label>Vulnerability Name</label>

<input
type="text"
name="vulnerability_name"
class="form-control"
value="<?php echo htmlspecialchars($vulnerability['vulnerability_name']); ?>"
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
value="<?php echo $vulnerability['cvss_score']; ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label>Severity</label>

<select name="severity" class="form-select">

<?php

$levels = ["Low","Medium","High","Critical"];

foreach($levels as $level){

$selected = ($vulnerability['severity']==$level) ? "selected" : "";

echo "<option value='$level' $selected>$level</option>";

}

?>

</select>

</div>

<div class="col-md-6 mb-3">

<label>Affected Software</label>

<input
type="text"
name="affected_software"
class="form-control"
value="<?php echo htmlspecialchars($vulnerability['affected_software']); ?>">

</div>

<div class="col-12 mb-3">

<label>Description</label>

<textarea
name="description"
rows="4"
class="form-control"><?php echo htmlspecialchars($vulnerability['description']); ?></textarea>

</div>

<div class="col-12 mb-3">

<label>Solution</label>

<textarea
name="solution"
rows="4"
class="form-control"><?php echo htmlspecialchars($vulnerability['solution']); ?></textarea>

</div>

<div class="col-12">

<button
type="submit"
name="update"
class="btn btn-success">

<i class="fa-solid fa-floppy-disk"></i>

Update Vulnerability

</button>

<a href="view.php" class="btn btn-secondary">

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
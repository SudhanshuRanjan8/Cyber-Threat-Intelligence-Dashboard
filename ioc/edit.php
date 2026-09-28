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

$stmt = $conn->prepare("SELECT * FROM iocs WHERE id = ?");
$stmt->execute([$id]);

$ioc = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ioc) {
    header("Location: view.php");
    exit();
}

if (isset($_POST['update'])) {

    $indicator_type = $_POST['indicator_type'];
    $indicator_value = trim($_POST['indicator_value']);
    $threat_level = $_POST['threat_level'];
    $source = trim($_POST['source']);
    $status = $_POST['status'];
    $description = trim($_POST['description']);

    $update = $conn->prepare("
        UPDATE iocs
        SET
            indicator_type=?,
            indicator_value=?,
            threat_level=?,
            source=?,
            status=?,
            description=?
        WHERE id=?
    ");

    $update->execute([
        $indicator_type,
        $indicator_value,
        $threat_level,
        $source,
        $status,
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

<title>Edit IOC</title>

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

<h3>Edit IOC</h3>

</div>

<div class="card-body">

<form method="POST">

<div class="row">

<div class="col-md-6 mb-3">

<label>Indicator Type</label>

<select name="indicator_type" class="form-select">

<?php

$types = ["IP Address","Domain","URL","Hash"];

foreach($types as $type){

$selected = ($ioc['indicator_type']==$type) ? "selected" : "";

echo "<option value='$type' $selected>$type</option>";

}

?>

</select>

</div>

<div class="col-md-6 mb-3">

<label>Indicator Value</label>

<input
type="text"
name="indicator_value"
class="form-control"
value="<?php echo htmlspecialchars($ioc['indicator_value']); ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label>Threat Level</label>

<select name="threat_level" class="form-select">

<?php

$levels = ["Low","Medium","High","Critical"];

foreach($levels as $level){

$selected = ($ioc['threat_level']==$level) ? "selected" : "";

echo "<option value='$level' $selected>$level</option>";

}

?>

</select>

</div>

<div class="col-md-6 mb-3">

<label>Source</label>

<input
type="text"
name="source"
class="form-control"
value="<?php echo htmlspecialchars($ioc['source']); ?>">

</div>

<div class="col-md-6 mb-3">

<label>Status</label>

<select name="status" class="form-select">

<option value="Active" <?php if($ioc['status']=="Active") echo "selected"; ?>>Active</option>

<option value="Inactive" <?php if($ioc['status']=="Inactive") echo "selected"; ?>>Inactive</option>

</select>

</div>

<div class="col-12 mb-3">

<label>Description</label>

<textarea
name="description"
rows="4"
class="form-control"><?php echo htmlspecialchars($ioc['description']); ?></textarea>

</div>

<div class="col-12">

<button
type="submit"
name="update"
class="btn btn-success">

Update IOC

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
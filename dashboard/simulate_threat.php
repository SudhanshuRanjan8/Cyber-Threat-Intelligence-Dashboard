<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/index.php");
    exit();
}

require_once("../database/config.php");


/* ==========================================
   RANDOM THREAT LIST
========================================== */

$threats = [

    [
        "name"=>"WannaCry",
        "type"=>"Ransomware",
        "severity"=>"Critical",
        "description"=>"Mass encryption attack detected."
    ],

    [
        "name"=>"LockBit",
        "type"=>"Ransomware",
        "severity"=>"High",
        "description"=>"Suspicious ransomware behaviour detected."
    ],

    [
        "name"=>"Emotet",
        "type"=>"Malware",
        "severity"=>"Critical",
        "description"=>"Malware communication detected."
    ],

    [
        "name"=>"Trojan.Generic",
        "type"=>"Malware",
        "severity"=>"High",
        "description"=>"Generic Trojan infection detected."
    ],

    [
        "name"=>"Phishing Campaign",
        "type"=>"Phishing",
        "severity"=>"Medium",
        "description"=>"Suspicious phishing email campaign."
    ],

    [
        "name"=>"SQL Injection",

        "type"=>"Web Attack",

        "severity"=>"High",

        "description"=>"Possible SQL Injection attempt."
    ],

    [
        "name"=>"Cross Site Scripting",

        "type"=>"Web Attack",

        "severity"=>"Medium",

        "description"=>"XSS payload detected."
    ],

    [
        "name"=>"DDoS Attack",

        "type"=>"Network",

        "severity"=>"Critical",

        "description"=>"Large traffic spike detected."
    ],

    [
        "name"=>"Brute Force Login",

        "type"=>"Authentication",

        "severity"=>"Low",

        "description"=>"Multiple failed login attempts."
    ],

    [
        "name"=>"Spyware.Agent",

        "type"=>"Spyware",

        "severity"=>"Medium",

        "description"=>"Spyware process detected."
    ]

];


/* ==========================================
   RANDOM RECORD
========================================== */

$randomThreat = $threats[array_rand($threats)];

$threatName = $randomThreat["name"];
$threatType = $randomThreat["type"];
$severity   = $randomThreat["severity"];
$description= $randomThreat["description"];


/* ==========================================
   RANDOM IPS
========================================== */

$sourceIP =
rand(10,250).".".
rand(0,255).".".
rand(0,255).".".
rand(1,254);

$destinationIP =
rand(10,250).".".
rand(0,255).".".
rand(0,255).".".
rand(1,254);


/* ==========================================
   INSERT THREAT
========================================== */

$sql="INSERT INTO threats
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
?,
?,
?,
?,
?,
?,
?
)";

$stmt=$conn->prepare($sql);

$stmt->execute([

$threatName,
$threatType,
$severity,
$sourceIP,
$destinationIP,
"Active",
$description

]);


/* ==========================================
   CREATE ALERT
========================================== */

if($severity=="High" || $severity=="Critical")
{

$alertTitle=$severity." Threat Detected";

$alertMessage=$description;

$sql="INSERT INTO alerts
(
alert_title,
alert_message,
severity,
source_module,
status
)

VALUES
(
?,
?,
?,
?,
?
)";

$stmt=$conn->prepare($sql);

$stmt->execute([

$alertTitle,
$alertMessage,
$severity,
"Threat",
"Active"

]);

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Threat Detection Simulation</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#0d1117;

color:white;

display:flex;

justify-content:center;

align-items:center;

height:100vh;

font-family:Segoe UI;

}

.simulator{

width:650px;

background:#161b22;

padding:40px;

border-radius:15px;

box-shadow:0 0 25px rgba(0,255,255,.25);

text-align:center;

}

.progress{

height:25px;

margin-top:20px;

margin-bottom:25px;

}

.card-info{

background:#21262d;

padding:20px;

border-radius:10px;

margin-top:20px;

text-align:left;

}

.badge{

font-size:15px;

padding:8px 15px;

}

.bg-orange{
    background:#fd7e14 !important;
    color:#fff;
}   

</style>

</head>

<body>

<div class="simulator">

<h2 class="mb-3">

<i class="fa-solid fa-shield-halved text-info"></i>

Cyber Threat Intelligence

</h2>

<h4 class="mb-4">

Threat Detection Simulation

</h4>

<p id="scanText">

Initializing Threat Detection Engine...

</p>

<div class="progress">

<div id="progressBar"

class="progress-bar progress-bar-striped progress-bar-animated bg-success"

style="width:0%">

0%

</div>

</div>

<div id="result" style="display:none;">

<div class="alert alert-danger text-center">

    <h3 class="mb-2">

        <i class="fa-solid fa-shield-virus"></i>

        Threat Successfully Detected

    </h3>

    <p class="mb-0">

        Security event has been logged and analyzed.

    </p>

</div>

<div class="card-info">

<p>

<strong>Threat Name :</strong>

<?php echo htmlspecialchars($threatName); ?>

</p>

<p>

<strong>Threat Type :</strong>

<?php echo htmlspecialchars($threatType); ?>

</p>

<p>

<strong>Severity :</strong>

<?php

$badgeClass = "bg-success";

if($severity=="Medium")
    $badgeClass="bg-warning text-dark";

elseif($severity=="High")
    $badgeClass="bg-orange";

elseif($severity=="Critical")
    $badgeClass="bg-danger";

?>

<span class="badge <?php echo $badgeClass; ?>">

<?php echo htmlspecialchars($severity); ?>

</span>

</p>

<p>

<strong>Source IP :</strong>

<?php echo htmlspecialchars($sourceIP); ?>

</p>

<p>

<strong>Destination IP :</strong>

<?php echo htmlspecialchars($destinationIP); ?>

</p>

<p>

<strong>Detection Time :</strong>

<?php echo date("d M Y H:i:s"); ?>

</p>

<p>

<strong>Description :</strong>

<?php echo htmlspecialchars($description); ?>

</p>

</div>

<div class="alert alert-success mt-4">

✔ Threat inserted successfully.

<?php if($severity=="High" || $severity=="Critical"){ ?>

<br><br>

✔ Security Alert Generated Successfully.

<?php } ?>

</div>

<p class="mt-3">

Redirecting to Dashboard...

</p>

</div>

</div>

<script>
let width = 0;

let bar = document.getElementById("progressBar");

let result = document.getElementById("result");

let scanText = document.getElementById("scanText");

const messages = [

"Initializing Threat Detection Engine...",

"Scanning Network Traffic...",

"Checking Firewall Logs...",

"Analyzing Indicators of Compromise...",

"Correlating Threat Intelligence...",

"Detecting Suspicious Activity...",

"Generating Security Alert..."

];

let msg = 0;

let messageTimer = setInterval(function(){

    if(msg < messages.length){

        scanText.innerHTML = messages[msg];

        msg++;

    }

},700);

let timer = setInterval(function(){

    width += 2;

    bar.style.width = width + "%";

    bar.innerHTML = width + "%";

    // Progress bar color changes

    if(width == 30){

        bar.classList.remove("bg-success");

        bar.classList.add("bg-info");

    }

    if(width == 60){

        bar.classList.remove("bg-info");

        bar.classList.add("bg-warning");

    }

    if(width == 90){

        bar.classList.remove("bg-warning");

        bar.classList.add("bg-danger");

    }

    if(width >= 100){

        clearInterval(timer);

        clearInterval(messageTimer);

        scanText.innerHTML = "Threat Analysis Completed";

        result.style.display = "block";

        document.getElementById("alarm").play();

        setTimeout(function(){

            window.location = "dashboard.php";

        },3000);

    }

},40);

</script>

<audio id="alarm">

<source src="../assets/audio/alert.mp3" type="audio/mpeg">

</audio>

</body>

</html>
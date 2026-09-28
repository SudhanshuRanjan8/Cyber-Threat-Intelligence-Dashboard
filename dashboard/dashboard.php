<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/index.php");
    exit();
}

require_once "../database/config.php";

/* =====================================================
   DASHBOARD COUNTS
===================================================== */

$threatCount = $conn->query("
SELECT COUNT(*)
FROM threats
")->fetchColumn();

$iocCount = $conn->query("
SELECT COUNT(*)
FROM iocs
")->fetchColumn();

$alertCount = $conn->query("
SELECT COUNT(*)
FROM alerts
")->fetchColumn();

$cveCount = $conn->query("
SELECT COUNT(*)
FROM vulnerabilities
WHERE cvss_score>=9.0
")->fetchColumn();


/* =====================================================
   CHART DATA
===================================================== */

$severityData = $conn->query("
SELECT severity,
COUNT(*) total
FROM threats
GROUP BY severity
")->fetchAll(PDO::FETCH_ASSOC);

$typeData = $conn->query("
SELECT threat_type,
COUNT(*) total
FROM threats
GROUP BY threat_type
")->fetchAll(PDO::FETCH_ASSOC);

$alertStatus = $conn->query("
SELECT status,
COUNT(*) total
FROM alerts
GROUP BY status
")->fetchAll(PDO::FETCH_ASSOC);


/* =====================================================
   RECENT ACTIVITIES
===================================================== */

$recentThreats = $conn->query("
SELECT threat_name,severity,status
FROM threats
ORDER BY id DESC
LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

$recentIOCs = $conn->query("
SELECT indicator_value,threat_level
FROM iocs
ORDER BY id DESC
LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

$recentVulnerabilities = $conn->query("
SELECT cve_id,severity
FROM vulnerabilities
ORDER BY id DESC
LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

$recentAlerts = $conn->query("
SELECT alert_title,severity,status
FROM alerts
ORDER BY id DESC
LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);


/* =====================================================
   ARRAYS FOR CHART.JS
===================================================== */

$severityLabels=[];
$severityCounts=[];

foreach($severityData as $row){

    $severityLabels[]=$row['severity'];
    $severityCounts[]=$row['total'];

}

$typeLabels=[];
$typeCounts=[];

foreach($typeData as $row){

    $typeLabels[]=$row['threat_type'];
    $typeCounts[]=$row['total'];

}

$alertLabels=[];
$alertCounts=[];

foreach($alertStatus as $row){

    $alertLabels[]=$row['status'];
    $alertCounts[]=$row['total'];

}

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Cyber Threat Intelligence Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<link rel="stylesheet" href="../assets/css/dashboard.css">

</head>

<body>

<?php include("../includes/sidebar.php"); ?>


<div class="main">

<?php include("../includes/navbar.php"); ?>

<div class="content">

<div class="d-flex justify-content-end mb-3">

    <a href="simulate_threat.php"
       class="btn btn-danger">

        <i class="fa-solid fa-radiation"></i>

        Simulate Threat Detection

    </a>

</div>

<!-- ===========================
     Dashboard Cards
============================ -->

<div class="row g-4">

<div class="col-lg-3 col-md-6">

<div class="card dashboard-card card-blue">

<div class="card-body text-center">

<i class="fa-solid fa-triangle-exclamation fa-2x mb-3"></i>

<h5>Total Threats</h5>

<h2>

<?php echo $threatCount; ?>

</h2>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6">

<div class="card dashboard-card card-red">

<div class="card-body text-center">

<i class="fa-solid fa-network-wired fa-2x mb-3"></i>

<h5>IOC Records</h5>

<h2>

<?php echo $iocCount; ?>

</h2>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6">

<div class="card dashboard-card card-green">

<div class="card-body text-center">

<i class="fa-solid fa-bug fa-2x mb-3"></i>

<h5>Critical CVEs</h5>

<h2>

<?php echo $cveCount; ?>

</h2>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6">

<div class="card dashboard-card card-orange">

<div class="card-body text-center">

<i class="fa-solid fa-bell fa-2x mb-3"></i>

<h5>Security Alerts</h5>

<h2>

<?php echo $alertCount; ?>

</h2>

</div>

</div>

</div>

</div>


<!-- ===========================
     System Status
============================ -->

<div class="alert alert-primary mt-4 shadow-sm">

<div class="d-flex justify-content-between align-items-center">

<div>

<h5 class="mb-1">

<i class="fa-solid fa-shield-halved"></i>

System Status

</h5>

<span>

All monitoring services are running normally.

Threat Intelligence Engine is Active.

</span>

</div>

<span class="badge bg-success fs-6">

ONLINE

</span>

</div>

</div>

<!-- ===========================
     System Information
============================ -->

<div class="card shadow mt-4">

    <div class="card-header bg-dark text-white">

        <h5 class="mb-0">
            <i class="fa-solid fa-circle-info"></i>
            System Information
        </h5>

    </div>

    <div class="card-body">

        <div class="row text-center">

            <div class="col-md-3">

                <h6>Logged In User</h6>

                <p class="fw-bold">
                    <?php echo $_SESSION['fullname']; ?>
                </p>

            </div>

            <div class="col-md-3">

                <h6>Role</h6>

                <p class="fw-bold">
                    <?php echo $_SESSION['role']; ?>
                </p>

            </div>

            <div class="col-md-3">

                <h6>Server Time</h6>

                <p class="fw-bold" id="serverTime"></p>

            </div>

            <div class="col-md-3">

                <h6>Dashboard Status</h6>

                <span class="badge bg-success fs-6">

                    Active

                </span>

            </div>

        </div>

    </div>

</div>


<!-- ===========================
     Charts
============================ -->

<div class="row mt-4">

    <div class="col-12">

        <div class="card shadow">

            <div class="card-header bg-primary text-white">

                <h5 class="mb-0">

                    <i class="fa-solid fa-chart-pie"></i>

                    Threat Severity Distribution

                </h5>

            </div>

            <div class="card-body">

                <canvas id="severityChart" height="110"></canvas>

            </div>

        </div>

    </div>

</div>


<div class="row mt-4">

    <div class="col-lg-6">

        <div class="card shadow h-100">

            <div class="card-header bg-success text-white">

                <h5 class="mb-0">

                    <i class="fa-solid fa-chart-column"></i>

                    Threat Types

                </h5>

            </div>

            <div class="card-body">

                <canvas id="typeChart" height="230"></canvas>

            </div>

        </div>

    </div>


    <div class="col-lg-6">

        <div class="card shadow h-100">

            <div class="card-header bg-warning text-dark">

                <h5 class="mb-0">

                    <i class="fa-solid fa-chart-donut"></i>

                    Alert Status

                </h5>

            </div>

            <div class="card-body">

                <canvas id="alertChart" height="230"></canvas>

            </div>

        </div>

    </div>

</div>

<!-- ===========================
     Recent Activities
============================ -->

<div class="row mt-4">

    <!-- Recent Threats -->

    <div class="col-lg-6 mb-4">

        <div class="card shadow h-100">

            <div class="card-header bg-danger text-white">

                <h5 class="mb-0">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    Recent Threats
                </h5>

            </div>

            <div class="card-body table-responsive">

                <table class="table table-hover align-middle">

                    <thead>

                    <tr>

                        <th>Threat</th>
                        <th>Severity</th>
                        <th>Status</th>

                    </tr>

                    </thead>

                    <tbody>

                    <?php if(count($recentThreats)>0): ?>

                        <?php foreach($recentThreats as $row): ?>

                        <?php

                        switch($row['severity']){

                            case 'Critical':
                                $severityClass='bg-danger';
                                break;

                            case 'High':
                                $severityClass='bg-orange';
                                break;

                            case 'Medium':
                                $severityClass='bg-warning text-dark';
                                break;

                            default:
                                $severityClass='bg-success';

                        }

                        $statusClass = ($row['status']=="Resolved")
                            ? "bg-success"
                            : "bg-danger";

                        ?>

                        <tr>

                            <td><?php echo htmlspecialchars($row['threat_name']); ?></td>

                            <td>

                                <span class="badge <?php echo $severityClass; ?>">

                                    <?php echo $row['severity']; ?>

                                </span>

                            </td>

                            <td>

                                <span class="badge <?php echo $statusClass; ?>">

                                    <?php echo $row['status']; ?>

                                </span>

                            </td>

                        </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="3" class="text-center text-muted">

                                No recent threats found.

                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    <!-- Recent Alerts -->

    <div class="col-lg-6 mb-4">

        <div class="card shadow h-100">

            <div class="card-header bg-warning">

                <h5 class="mb-0">

                    <i class="fa-solid fa-bell"></i>

                    Recent Alerts

                </h5>

            </div>

            <div class="card-body table-responsive">

                <table class="table table-hover align-middle">

                    <thead>

                    <tr>

                        <th>Alert</th>
                        <th>Severity</th>
                        <th>Status</th>

                    </tr>

                    </thead>

                    <tbody>

                    <?php if(count($recentAlerts)>0): ?>

                        <?php foreach($recentAlerts as $row): ?>

                        <?php

                        switch($row['severity']){

                            case 'Critical':
                                $severityClass='bg-danger';
                                break;

                            case 'High':
                                $severityClass='bg-orange';
                                break;

                            case 'Medium':
                                $severityClass='bg-warning text-dark';
                                break;

                            default:
                                $severityClass='bg-success';

                        }

                        $statusClass = ($row['status']=="Resolved")
                            ? "bg-success"
                            : "bg-danger";

                        ?>

                        <tr>

                            <td><?php echo htmlspecialchars($row['alert_title']); ?></td>

                            <td>

                                <span class="badge <?php echo $severityClass; ?>">

                                    <?php echo $row['severity']; ?>

                                </span>

                            </td>

                            <td>

                                <span class="badge <?php echo $statusClass; ?>">

                                    <?php echo $row['status']; ?>

                                </span>

                            </td>

                        </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="3" class="text-center text-muted">

                                No recent alerts found.

                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>


<div class="row">

    <!-- Recent IOCs -->

    <div class="col-lg-6 mb-4">

        <div class="card shadow h-100">

            <div class="card-header bg-info text-white">

                <h5 class="mb-0">

                    <i class="fa-solid fa-network-wired"></i>

                    Recent IOCs

                </h5>

            </div>

            <div class="card-body table-responsive">

                <table class="table table-hover align-middle">

                    <thead>

                    <tr>

                        <th>Indicator</th>
                        <th>Threat Level</th>

                    </tr>

                    </thead>

                    <tbody>

                    <?php if(count($recentIOCs)>0): ?>

                        <?php foreach($recentIOCs as $row): ?>

                        <?php

                        switch($row['threat_level']){

                            case 'Critical':
                                $badge='bg-danger';
                                break;

                            case 'High':
                                $badge='bg-orange';
                                break;

                            case 'Medium':
                                $badge='bg-warning text-dark';
                                break;

                            default:
                                $badge='bg-success';

                        }

                        ?>

                        <tr>

                            <td><?php echo htmlspecialchars($row['indicator_value']); ?></td>

                            <td>

                                <span class="badge <?php echo $badge; ?>">

                                    <?php echo $row['threat_level']; ?>

                                </span>

                            </td>

                        </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="2" class="text-center text-muted">

                                No IOC records found.

                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

        <!-- Recent Vulnerabilities -->

    <div class="col-lg-6 mb-4">

        <div class="card shadow h-100">

            <div class="card-header bg-secondary text-white">

                <h5 class="mb-0">

                    <i class="fa-solid fa-bug"></i>

                    Recent Vulnerabilities

                </h5>

            </div>

            <div class="card-body table-responsive">

                <table class="table table-hover align-middle">

                    <thead>

                    <tr>

                        <th>CVE ID</th>

                        <th>Severity</th>

                    </tr>

                    </thead>

                    <tbody>

                    <?php if(count($recentVulnerabilities)>0): ?>

                        <?php foreach($recentVulnerabilities as $row): ?>

                        <?php

                        switch($row['severity']){

                            case 'Critical':
                                $badge='bg-danger';
                                break;

                            case 'High':
                                $badge='bg-orange';
                                break;

                            case 'Medium':
                                $badge='bg-warning text-dark';
                                break;

                            default:
                                $badge='bg-success';

                        }

                        ?>

                        <tr>

                            <td>

                                <?php echo htmlspecialchars($row['cve_id']); ?>

                            </td>

                            <td>

                                <span class="badge <?php echo $badge; ?>">

                                    <?php echo $row['severity']; ?>

                                </span>

                            </td>

                        </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="2" class="text-center text-muted">

                                No vulnerability records found.

                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

document.getElementById("serverTime").innerHTML =
new Date().toLocaleString();


new Chart(document.getElementById('severityChart'), {

    type: 'line',

    data: {

        labels: <?php echo json_encode($severityLabels); ?>,

        datasets: [{

            label: 'Threat Count',

            data: <?php echo json_encode($severityCounts); ?>,

            borderColor: '#0d6efd',

            backgroundColor: 'rgba(13,110,253,0.15)',

            borderWidth: 3,

            fill: true,

            tension: 0.4,

            pointRadius: 6,

            pointHoverRadius: 8,

            pointBackgroundColor: '#0d6efd',

            pointBorderColor: '#ffffff',

            pointBorderWidth: 2

        }]

    },

    options: {

        responsive: true,

        maintainAspectRatio: false,

        plugins: {

            legend: {

                display: true,

                position: 'top'

            }

        },

        scales: {

            y: {

                beginAtZero: true,

                ticks: {

                    precision: 0

                }

            }

        }

    }

});


new Chart(document.getElementById('typeChart'),{

type:'bar',

data:{

labels:<?php echo json_encode($typeLabels); ?>,

datasets:[{

label:'Threat Types',

data:<?php echo json_encode($typeCounts); ?>

}]

},

options:{

responsive:true,

maintainAspectRatio:false

}

});


new Chart(document.getElementById('alertChart'),{

type:'doughnut',

data:{

labels:<?php echo json_encode($alertLabels); ?>,

datasets:[{

data:<?php echo json_encode($alertCounts); ?>

}]

},

options:{

responsive:true,

maintainAspectRatio:false

}

});

</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</div>

</div>

</body>

</html>

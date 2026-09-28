<?php

if(session_status() == PHP_SESSION_NONE)
{
    session_start();
}

date_default_timezone_set("Asia/Kolkata");

?>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">

    <div class="container-fluid">

        <h4 class="mb-0">

            Cyber Threat Intelligence Dashboard

        </h4>

        <p class="text-muted mb-0">

            Real-Time Threat Monitoring & Analysis

        </p>

        <div class="d-flex align-items-center">

            <span class="me-4">

                <i class="fa-solid fa-calendar-days"></i>

                <?php echo date("d M Y"); ?>

            </span>

            <span class="me-4">

                <i class="fa-solid fa-clock"></i>

                <?php echo date("h:i:s A"); ?>

            </span>

            <span class="me-4">

                <i class="fa-solid fa-user-shield"></i>

                <?php echo $_SESSION['fullname']; ?>

            </span>

            <span class="badge bg-primary me-3">

                <?php echo $_SESSION['role']; ?>

            </span>

            <a href="../login/logout.php"
               class="btn btn-danger">

                <i class="fa-solid fa-right-from-bracket"></i>

                Logout

            </a>

        </div>

    </div>

</nav>
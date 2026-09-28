<?php

// ===============================
// Authentication File
// ===============================

session_start();

// If user is not logged in
if(!isset($_SESSION['user_id']))
{
    header("Location: ../login/index.php");
    exit();
}

?>
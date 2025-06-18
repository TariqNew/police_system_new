<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
ob_start(); // Ensures no output before headers

if (
    isset($_SESSION['admin_id']) &&
    isset($_SESSION['role']) &&
    $_SESSION['role'] === 'Admin' &&
    isset($_GET['officer_id'])
) {
    require_once "../DB_connection.php";
    require_once "./data/police_officer.php"; // Make sure this path is correct

    $officer_id = intval($_GET['officer_id']);

    if (removeOfficer($officer_id, $conn)) {
        header("Location: police_officer.php?success=" . urlencode("Officer deleted successfully"));
        exit;
    } else {
        header("Location: police_officer.php?error=" . urlencode("Officer not found or already deleted"));
        exit;
    }

} else {
    header("Location: ../police_officer.php?error=" . urlencode("Unauthorized access"));
    exit;
}

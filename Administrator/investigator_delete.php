<?php

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session and output buffering
session_start();
ob_start();

// Check if admin is logged in and investigator_id is provided
if (
    isset($_SESSION['admin_id']) &&
    isset($_SESSION['role']) &&
    $_SESSION['role'] === 'Admin' &&
    isset($_GET['investigator_id'])
) {
    require_once "../DB_connection.php";
    require_once "./data/investigator.php"; // This should contain removeInvestigator()

    $investigator_id = intval($_GET['investigator_id']);

    try {
        if (removeInvestigator($investigator_id, $conn)) {
            header("Location: investigator.php?success=" . urlencode("Investigator deleted successfully"));
            exit;
        } else {
            header("Location: investigator.php?error=" . urlencode("Investigator not found or already deleted"));
            exit;
        }
    } catch (Exception $e) {
        header("Location: investigator.php?error=" . urlencode("An unexpected error occurred: " . $e->getMessage()));
        exit;
    }

} else {
    header("Location: investigator.php?error=" . urlencode("Unauthorized access or missing parameters"));
    exit;
}

<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (isset($_SESSION['admin_id']) && $_SESSION['role'] === 'Admin') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        include "../DB_connection.php";

        $system_name   = trim($_POST['system_name']);
        $tagline       = trim($_POST['tagline']);
        $description   = trim($_POST['description']);
        $system_year   = trim($_POST['system_year']);
        $system_phase  = trim($_POST['system_phase']);

        if (empty($system_name) || empty($tagline) || empty($description) || empty($system_year) || empty($system_phase)) {
            header("Location: ../setting.php?error=All fields are required!");
            exit;
        }

        $sql = "UPDATE setting SET 
                  system_name = ?, 
                  tagline = ?, 
                  description = ?, 
                  system_year = ?, 
                  system_phase = ?
                WHERE id = 1";

        $stmt = $conn->prepare($sql);
        $success = $stmt->execute([$system_name, $tagline, $description, $system_year, $system_phase]);

        if ($success) {
            header("Location: ../Administrator/settings.php?success=Settings updated successfully!");
        } else {
            header("Location: ../Administrator/settings.php?error=Failed to update settings!");
        }
        exit;
    } else {
        header("Location: ../setting.php?error=Invalid request method!");
        exit;
    }
} else {
    header("Location: ../login.php");
    exit;
}

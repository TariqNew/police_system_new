<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (isset($_SESSION['officer_id']) && $_SESSION['role'] == 'Officer') {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        include "../../DB_connection.php";

        $officer_id = $_SESSION['officer_id'];
        $old_pass = $_POST['old_pass'];
        $new_pass = $_POST['new_pass'];
        $c_new_pass = $_POST['c_new_pass'];

        // Passwords must match
        if ($new_pass !== $c_new_pass) {
            header("Location: ../officer_change.php?perror=New passwords do not match");
            exit;
        }

        // Fetch existing password
        $stmt = $conn->prepare("SELECT password FROM officers WHERE officer_id = ?");
        $stmt->execute([$officer_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $user['password'] === $old_pass) {
            // Update password (plaintext as requested)
            $update = $conn->prepare("UPDATE officers SET password = ? WHERE officer_id = ?");
            if ($update->execute([$new_pass, $officer_id])) {
                header("Location: ../pass.php?psuccess=Password updated successfully");
                exit;
            } else {
                header("Location: ../pass.php?perror=Failed to update password");
                exit;
            }
        } else {
            header("Location: ../pass.php?perror=Incorrect old password");
            exit;
        }
    }
} else {
    header("Location: ../login.php");
    exit;
}

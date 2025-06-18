<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['officer_id']) || $_SESSION['role'] !== 'Officer') {
    header("Location: ../login.php");
    exit;
}

include "../../DB_connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect suspect data
    $fname = trim($_POST['fname'] ?? '');
    $lname = trim($_POST['lname'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $email_address = trim($_POST['email_address'] ?? '');
    $date_of_birth = $_POST['date_of_birth'] ?? '';
    $gender = $_POST['gender'] ?? 'Male';
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['pass'] ?? '');
    $parent_fname = trim($_POST['parent_fname'] ?? '');
    $parent_lname = trim($_POST['parent_lname'] ?? '');
    $parent_phone_number = trim($_POST['parent_phone_number'] ?? '');

    // Case data (optional)
    $case_title = trim($_POST['case_title'] ?? '');
    $case_description = trim($_POST['case_description'] ?? '');
    $case_date = $_POST['case_date'] ?? null;

    // Basic validations
    if (!$fname || !$lname || !$username || !$password || !$date_of_birth) {
        header("Location: ../suspect_add.php?error=Please fill all required fields&fname=".urlencode($fname)."&lname=".urlencode($lname)."&uname=".urlencode($username));
        exit;
    }

    // Check username uniqueness
    $check = $conn->prepare("SELECT suspect_id FROM suspects WHERE username = ?");
    $check->execute([$username]);
    if ($check->rowCount() > 0) {
        header("Location: ../suspect_add.php?error=Username already exists&fname=".urlencode($fname)."&lname=".urlencode($lname)."&uname=".urlencode($username));
        exit;
    }

    // Hash password
    $pass_hashed = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Begin transaction to insert suspect and case atomically
        $conn->beginTransaction();

        // Insert suspect
        $stmt = $conn->prepare("INSERT INTO suspects (username, password, fname, lname, grade, section, address, gender, email_address, date_of_birth, parent_fname, parent_lname, parent_phone_number) VALUES (?, ?, ?, ?, 0, 0, ?, ?, ?, ?, ?, ?, ?)");
        // grade and section hardcoded 0 as per your table definition, you can adjust if needed
        $stmt->execute([$username, $pass_hashed, $fname, $lname, $address, $gender, $email_address, $date_of_birth, $parent_fname, $parent_lname, $parent_phone_number]);

        $suspect_id = $conn->lastInsertId();

        // Insert case only if case_title provided
        if ($case_title) {
            $stmtCase = $conn->prepare("INSERT INTO criminal_cases (suspect_id, case_title, case_description, date_reported) VALUES (?, ?, ?, ?)");
            $date_reported = $case_date ?: date('Y-m-d');
            $stmtCase->execute([$suspect_id, $case_title, $case_description, $date_reported]);
        }

        $conn->commit();

        header("Location: ../suspect.php?success=Suspect and case added successfully");
        exit;
    } catch (Exception $e) {
        $conn->rollBack();
        header("Location: ../suspect_add.php?error=Failed to add suspect or case: " . urlencode($e->getMessage()) . "&fname=".urlencode($fname)."&lname=".urlencode($lname)."&uname=".urlencode($username));
        exit;
    }
} else {
    header("Location: ../suspect_add.php");
    exit;
}

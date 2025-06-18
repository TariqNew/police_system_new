<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (isset($_POST['uname']) && isset($_POST['pass']) && isset($_POST['role'])) {
    include "../DB_connection.php";

    $uname = trim($_POST['uname']);
    $pass = trim($_POST['pass']);
    $role = $_POST['role'];

    // Basic validation
    if (empty($uname)) {
        $em = "Username is required";
        header("Location: ../login.php?error=" . urlencode($em));
        exit;
    } elseif (empty($pass)) {
        $em = "Password is required";
        header("Location: ../login.php?error=" . urlencode($em));
        exit;
    } elseif (empty($role)) {
        $em = "An error occurred";
        header("Location: ../login.php?error=" . urlencode($em));
        exit;
    }

    try {
        // Choose SQL query and role name based on selected role
        switch ($role) {
            case '1':
                $sql = "SELECT * FROM admin WHERE username = :uname";
                $roleName = "Admin";
                break;
            case '2':
                $sql = "SELECT * FROM officers WHERE username = :uname";
                $roleName = "Officer";
                break;
            case '3':
                $sql = "SELECT * FROM investigators WHERE username = :uname";
                $roleName = "Investigator";
                break;
            default:
                $em = "Invalid role selection.";
                header("Location: ../login.php?error=" . urlencode($em));
                exit;
        }

        // Prepare and execute query
        $stmt = $conn->prepare($sql);
        $stmt->execute([':uname' => $uname]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if user exists and password matches (plain text comparison)
        if ($user && $pass === $user['password']) {
            $_SESSION['role'] = $roleName;

            // Set session ID and redirect based on role
            switch ($roleName) {
                case 'Admin':
                    $_SESSION['admin_id'] = $user['admin_id'];
                    header("Location: ../Administrator/index.php");
                    break;
                case 'Officer':
                    $_SESSION['officer_id'] = $user['officer_id'];
                    header("Location: ../Police Officer/index.php");
                    break;
                case 'Investigator':
                    $_SESSION['investigator_id'] = $user['investigator_id'];
                    header("Location: ../Investigator/index.php");
                    break;
            }
            exit;
        } else {
            $em = "Incorrect Username or Password";
            header("Location: ../login.php?error=" . urlencode($em));
            exit;
        }

    } catch (PDOException $e) {
        $em = "Database error: " . $e->getMessage();
        header("Location: ../login.php?error=" . urlencode($em));
        exit;
    }

} else {
    // If form not submitted correctly, redirect to login page
    header("Location: ../login.php");
    exit;
}
?>

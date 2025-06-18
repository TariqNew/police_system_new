<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (
    isset($_SESSION['admin_id']) &&
    isset($_SESSION['role']) &&
    $_SESSION['role'] === 'Admin' &&
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {
    // CSRF Protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header("Location: ../Administrator/police_officer.php?error=Invalid CSRF token");
        exit;
    }

    require_once "../DB_connection.php";

    // Sanitize and validate input
    function sanitize($data) {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    $officer_id       = intval($_POST['officer_id']);
    $fname            = sanitize($_POST['fname']);
    $lname            = sanitize($_POST['lname']);
    $username         = sanitize($_POST['username']);
    $address          = sanitize($_POST['address']);
    $employee_number  = intval($_POST['employee_number']);
    $date_of_birth    = $_POST['date_of_birth']; // assumed valid date
    $phone_number     = sanitize($_POST['phone_number']);
    $qualification    = sanitize($_POST['qualification']);
    $email_address    = filter_var($_POST['email_address'], FILTER_SANITIZE_EMAIL);
    $gender           = ($_POST['gender'] === 'Male' || $_POST['gender'] === 'Female') ? $_POST['gender'] : null;

    if (!$gender) {
        header("Location: ../Administrator/police_officer.php?error=Invalid gender selected");
        exit;
    }

    try {
        $sql = "UPDATE officers SET
                    fname = ?, lname = ?, username = ?, address = ?, 
                    employee_number = ?, date_of_birth = ?, phone_number = ?, 
                    qualification = ?, email_address = ?, gender = ?
                WHERE officer_id = ?";
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute([
            $fname, $lname, $username, $address,
            $employee_number, $date_of_birth, $phone_number,
            $qualification, $email_address, $gender,
            $officer_id
        ]);

        if ($result) {
            header("Location: ../Administrator/police_officer.php?success=Officer updated successfully");
            exit;
        } else {
            header("Location: ../Administrator/police_officer.php?error=Failed to update officer");
            exit;
        }
    } catch (PDOException $e) {
        header("Location: ../Administrator/police_officer.php?error=" . urlencode("Database error: " . $e->getMessage()));
        exit;
    }

} else {
    header("Location: ../Administrator/police_officer.php?error=Unauthorized access");
    exit;
}

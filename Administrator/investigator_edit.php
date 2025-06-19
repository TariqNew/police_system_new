<?php
// Debug & Dependencies
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once "../DB_connection.php";
require_once "../req/logger.php";
require_once "data/investigator.php";

// Sanitize Function
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// ========================
// 🟢 HANDLE FORM SUBMISSION (POST)
// ========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        isset($_SESSION['admin_id'], $_SESSION['role']) &&
        $_SESSION['role'] === 'Admin' &&
        isset($_POST['csrf_token'], $_SESSION['csrf_token']) &&
        $_POST['csrf_token'] === $_SESSION['csrf_token']
    ) {
        $investigator_id   = intval($_POST['investigator_id']);
        $fname             = sanitize($_POST['fname']);
        $lname             = sanitize($_POST['lname']);
        $username          = sanitize($_POST['username']);
        $address           = sanitize($_POST['address']);
        $employee_number   = intval($_POST['employee_number']);
        $date_of_birth     = $_POST['date_of_birth'];
        $phone_number      = sanitize($_POST['phone_number']);
        $qualification     = sanitize($_POST['qualification']);
        $email_address     = filter_var($_POST['email_address'], FILTER_SANITIZE_EMAIL);
        $gender            = ($_POST['gender'] === 'Male' || $_POST['gender'] === 'Female') ? $_POST['gender'] : null;

        if (!$gender) {
            header("Location: investigator_edit.php?error=Invalid gender selected");
            exit;
        }

        try {
            $stmt = $conn->prepare("UPDATE investigators SET
                fname=?, lname=?, username=?, address=?, employee_number=?, 
                date_of_birth=?, phone_number=?, qualification=?, 
                email_address=?, gender=?
                WHERE investigator_id=?");

            $updated = $stmt->execute([
                $fname, $lname, $username, $address, $employee_number,
                $date_of_birth, $phone_number, $qualification,
                $email_address, $gender, $investigator_id
            ]);

            if ($updated) {
                header("Location: investigator.php?success=Investigator updated successfully");
            } else {
                header("Location: investigator.php?error=Failed to update investigator");
            }
            exit;
        } catch (PDOException $e) {
            header("Location: investigator.php?error=" . urlencode("DB Error: " . $e->getMessage()));
            exit;
        }
    } else {
        header("Location: investigator.php?error=Unauthorized or invalid CSRF");
        exit;
    }
}

// ========================
// 🟡 SHOW EDIT FORM (GET)
// ========================
if (
    isset($_SESSION['admin_id'], $_SESSION['role'], $_GET['investigator_id']) &&
    $_SESSION['role'] === 'Admin'
) {
    $investigator_id = intval($_GET['investigator_id']);
    $investigator = getInvestigatorById($investigator_id, $conn);

    if (!$investigator) {
        header("Location: investigator.php?error=Investigator not found");
        exit;
    }

    // CSRF Token Setup
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    $csrf_token = $_SESSION['csrf_token'];

    // Log
    logAction(
        $conn,
        $_SESSION['admin_id'],
        $_SESSION['role'],
        "VIEW INVESTIGATOR EDIT PAGE",
        "Viewed edit form for investigator_id=$investigator_id"
    );
} else {
    header("Location: investigator.php?error=Unauthorized access");
    exit;
}
?>

<!-- ========================
🔵 HTML FORM FOR EDITING
========================= -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Edit Investigator</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" href="../logo.png" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <style>
    body { overflow: hidden; }
    main { height: calc(100vh - 56px); overflow-y: auto; }
  </style>
</head>
<body>

<?php include "inc/navbar.php"; ?>
<div class="container-fluid">
  <div class="row" style="padding-top: 56px;">
    <?php include "inc/sidebar.php"; ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4 mt-4">
      <a href="investigator.php" class="btn btn-dark mb-3">← Go Back</a>

      <form method="post" class="shadow p-4 bg-white form-w">
        <h4 class="mb-3">Edit Investigator</h4>
        <hr />

        <?php if (isset($_GET['error'])) : ?>
          <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['success'])) : ?>
          <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>

        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>" />
        <input type="hidden" name="investigator_id" value="<?= $investigator['investigator_id'] ?>" />

        <?php
        $fields = [
          'fname' => 'First name',
          'lname' => 'Last name',
          'username' => 'Username',
          'address' => 'Address',
          'employee_number' => 'Employee number',
          'date_of_birth' => 'Date of birth',
          'phone_number' => 'Phone number',
          'qualification' => 'Qualification',
          'email_address' => 'Email address',
        ];
        foreach ($fields as $key => $label): ?>
          <div class="mb-3">
            <label class="form-label"><?= $label ?></label>
            <input type="<?= $key === 'date_of_birth' ? 'date' : 'text' ?>"
                   class="form-control"
                   name="<?= $key ?>"
                   value="<?= htmlspecialchars($investigator[$key]) ?>"
                   required />
          </div>
        <?php endforeach; ?>

        <div class="mb-3">
          <label class="form-label">Gender</label><br />
          <input type="radio" name="gender" value="Male" required <?= $investigator['gender'] === 'Male' ? 'checked' : '' ?> /> Male
          &nbsp;&nbsp;
          <input type="radio" name="gender" value="Female" required <?= $investigator['gender'] === 'Female' ? 'checked' : '' ?> /> Female
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
      </form>
    </main>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

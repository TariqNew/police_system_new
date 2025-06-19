<?php
// Debug & Dependencies
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once "../DB_connection.php";
require_once "../req/logger.php";

// Sanitize function
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
        $officer_id       = intval($_POST['officer_id']);
        $fname            = sanitize($_POST['fname']);
        $lname            = sanitize($_POST['lname']);
        $username         = sanitize($_POST['username']);
        $address          = sanitize($_POST['address']);
        $employee_number  = sanitize($_POST['employee_number']);
        $date_of_birth    = $_POST['date_of_birth'];
        $phone_number     = sanitize($_POST['phone_number']);
        $qualification    = sanitize($_POST['qualification']);
        $email_address    = filter_var($_POST['email_address'], FILTER_SANITIZE_EMAIL);
        $gender           = ($_POST['gender'] === 'Male' || $_POST['gender'] === 'Female') ? $_POST['gender'] : null;

        if (!$gender) {
            header("Location: police_officer_edit.php?error=Invalid gender selected");
            exit;
        }

        try {
            $stmt = $conn->prepare("UPDATE officers SET 
                fname=?, lname=?, username=?, address=?, employee_number=?, 
                date_of_birth=?, phone_number=?, qualification=?, 
                email_address=?, gender=? WHERE officer_id=?");

            $updated = $stmt->execute([
                $fname, $lname, $username, $address, $employee_number,
                $date_of_birth, $phone_number, $qualification,
                $email_address, $gender, $officer_id
            ]);

            if ($updated) {
                header("Location: police_officer.php?success=Officer updated successfully");
            } else {
                header("Location: police_officer.php?error=Failed to update officer");
            }
            exit;
        } catch (PDOException $e) {
            header("Location: police_officer.php?error=" . urlencode("DB Error: " . $e->getMessage()));
            exit;
        }
    } else {
        header("Location: police_officer.php?error=Unauthorized or invalid CSRF");
        exit;
    }
}

// ========================
// 🟡 SHOW EDIT FORM (GET)
// ========================
if (
    isset($_SESSION['admin_id'], $_SESSION['role'], $_GET['officer_id']) &&
    $_SESSION['role'] === 'Admin'
) {
    $officer_id = intval($_GET['officer_id']);
    $stmt = $conn->prepare("SELECT * FROM officers WHERE officer_id = ?");
    $stmt->execute([$officer_id]);
    $officer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$officer) {
        header("Location: police_officer.php?error=Officer not found");
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
        "VIEW POLICE OFFICER EDIT PAGE",
        "Viewed edit form for officer_id=$officer_id"
    );
} else {
    header("Location: police_officer.php?error=Unauthorized access");
    exit;
}
?>

<!-- ========================
🔵 HTML FORM FOR EDITING OFFICER
========================= -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Edit Police Officer</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="icon" href="../logo.png" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
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
      <a href="police_officer.php" class="btn btn-dark mb-3">← Go Back</a>

      <form method="post" class="shadow p-4 bg-white form-w">
        <h4 class="mb-3">Edit Police Officer</h4>
        <hr />

        <?php if (isset($_GET['error'])) : ?>
          <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['success'])) : ?>
          <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>

        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>" />
        <input type="hidden" name="officer_id" value="<?= $officer['officer_id'] ?>" />

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
                   value="<?= htmlspecialchars($officer[$key]) ?>"
                   required />
          </div>
        <?php endforeach; ?>

        <div class="mb-3">
          <label class="form-label">Gender</label><br />
          <input type="radio" name="gender" value="Male" required <?= $officer['gender'] === 'Male' ? 'checked' : '' ?> /> Male
          &nbsp;&nbsp;
          <input type="radio" name="gender" value="Female" required <?= $officer['gender'] === 'Female' ? 'checked' : '' ?> /> Female
        </div>

        <button type="submit" class="btn btn-primary">Update Officer</button>
      </form>
    </main>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

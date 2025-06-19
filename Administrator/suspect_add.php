<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$validRoles = ['Officer', 'Admin'];
$currentRole = $_SESSION['role'] ?? null;
$currentId = $_SESSION['officer_id'] ?? $_SESSION['admin_id'] ?? null;

if (!$currentId || !in_array($currentRole, $validRoles)) {
    header("Location: ../login.php");
    exit;
}

include "../DB_connection.php";

// Handle POST submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    $case_title = trim($_POST['case_title'] ?? '');
    $case_description = trim($_POST['case_description'] ?? '');
    $case_date = $_POST['case_date'] ?? null;

    if (!$fname || !$lname || !$username || !$password || !$date_of_birth) {
        $error = "Please fill all required fields";
    } else {
        $check = $conn->prepare("SELECT suspect_id FROM suspects WHERE username = ?");
        $check->execute([$username]);
        if ($check->rowCount() > 0) {
            $error = "Username already exists";
        } else {
            $pass_hashed = password_hash($password, PASSWORD_DEFAULT);

            try {
                $conn->beginTransaction();

                $stmt = $conn->prepare("INSERT INTO suspects 
                (username, password, fname, lname, grade, section, address, gender, email_address, date_of_birth, parent_fname, parent_lname, parent_phone_number) 
                VALUES (?, ?, ?, ?, 0, 0, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$username, $pass_hashed, $fname, $lname, $address, $gender, $email_address, $date_of_birth, $parent_fname, $parent_lname, $parent_phone_number]);

                $suspect_id = $conn->lastInsertId();

                if ($case_title) {
                    $stmtCase = $conn->prepare("INSERT INTO criminal_cases (suspect_id, case_title, case_description, date_reported) VALUES (?, ?, ?, ?)");
                    $date_reported = $case_date ?: date('Y-m-d');
                    $stmtCase->execute([$suspect_id, $case_title, $case_description, $date_reported]);
                }

                $conn->commit();
                $success = "Suspect and case added successfully.";
                header("Location: suspect.php");
            } catch (Exception $e) {
                $conn->rollBack();
                $error = "Failed to add suspect or case: " . $e->getMessage();
            }
        }
    }
}

// Repopulate form values on error
$old = $_POST ?? [];

function esc($v) {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Add Suspect</title>
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="icon" href="../logo.png" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
  <style>
    body, html {
      height: 100%;
      margin: 0;
    }
    .sidebar {
      position: fixed;
      top: 56px;
      bottom: 0;
      left: 0;
      width: 250px;
      background-color: #f8f9fa;
      overflow-y: auto;
    }
    main {
      margin-left: 250px;
      padding-top: 56px;
      height: calc(100vh - 56px);
      overflow-y: auto;
    }
  </style>
</head>
<body>
<?php include "./inc/navbar.php"; ?>

<div class="container-fluid">
  <div class="row" style="padding-top: 56px">
    <div class="sidebar col-md-3 d-none d-md-block">
      <?php include "./inc/sidebar.php"; ?>
    </div>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
      <h3 class="mb-4">Add New Suspect</h3>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= esc($error) ?></div>
      <?php elseif (!empty($success)): ?>
        <div class="alert alert-success"><?= esc($success) ?></div>
      <?php endif; ?>

      <form method="post" class="shadow p-4 bg-white rounded">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">First Name</label>
            <input type="text" class="form-control" name="fname" value="<?= esc($old['fname'] ?? '') ?>" required>
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Last Name</label>
            <input type="text" class="form-control" name="lname" value="<?= esc($old['lname'] ?? '') ?>" required>
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Address</label>
            <input type="text" class="form-control" name="address" value="<?= esc($old['address'] ?? '') ?>">
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" class="form-control" name="email_address" value="<?= esc($old['email_address'] ?? '') ?>">
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Date of Birth</label>
            <input type="date" class="form-control" name="date_of_birth" value="<?= esc($old['date_of_birth'] ?? '') ?>" required>
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Gender</label><br />
            <div class="form-check form-check-inline">
              <input type="radio" name="gender" class="form-check-input" value="Male" <?= (!isset($old['gender']) || $old['gender'] == 'Male') ? 'checked' : '' ?>>
              <label class="form-check-label">Male</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="radio" name="gender" class="form-check-input" value="Female" <?= (isset($old['gender']) && $old['gender'] == 'Female') ? 'checked' : '' ?>>
              <label class="form-check-label">Female</label>
            </div>
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Username</label>
            <input type="text" class="form-control" name="username" value="<?= esc($old['username'] ?? '') ?>" required>
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Parent First Name</label>
            <input type="text" class="form-control" name="parent_fname" value="<?= esc($old['parent_fname'] ?? '') ?>">
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Parent Last Name</label>
            <input type="text" class="form-control" name="parent_lname" value="<?= esc($old['parent_lname'] ?? '') ?>">
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Parent Phone Number</label>
            <input type="text" class="form-control" name="parent_phone_number" value="<?= esc($old['parent_phone_number'] ?? '') ?>">
          </div>
        </div>

        <hr>
        <h5>Case Details (optional)</h5>

        <div class="mb-3">
          <label class="form-label">Case Title</label>
          <input type="text" class="form-control" name="case_title" value="<?= esc($old['case_title'] ?? '') ?>">
        </div>

        <div class="mb-3">
          <label class="form-label">Case Description</label>
          <textarea class="form-control" name="case_description" rows="3"><?= esc($old['case_description'] ?? '') ?></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">Case Date</label>
          <input type="date" class="form-control" name="case_date" value="<?= esc($old['case_date'] ?? '') ?>">
        </div>

        <button type="submit" class="btn btn-primary mt-2">Register Suspect & Case</button>
      </form>
    </main>
  </div>
</div>

<script>
document.getElementById("gBtn").addEventListener("click", function(e) {
  e.preventDefault();
  const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
  let result = "";
  for (let i = 0; i < 8; i++) {
    result += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  document.getElementById("passInput").value = result;
});
</script>
</body>
</html>

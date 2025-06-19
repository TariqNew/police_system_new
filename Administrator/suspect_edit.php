<?php
// Debug & Dependencies
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once "../DB_connection.php";
require_once "../req/logger.php";
require_once "data/suspect.php";

// Check authorization
if (
    !isset($_SESSION['admin_id'], $_SESSION['role'], $_GET['suspect_id']) ||
    $_SESSION['role'] !== 'Admin'
) {
    header("Location: suspect.php?error=Unauthorized access");
    exit;
}

$suspect_id = intval($_GET['suspect_id']);
$suspect = getSuspectById($suspect_id, $conn);
$case = getCaseBySuspectId($suspect_id, $conn);

if (!$suspect) {
    header("Location: suspect.php?error=Suspect not found");
    exit;
}

// CSRF token setup
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Log the action
logAction(
    $conn,
    $_SESSION['admin_id'],
    $_SESSION['role'],
    "VIEW SUSPECT EDIT PAGE",
    "Viewed edit form for suspect_id=$suspect_id"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Edit Suspect</title>
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
      <a href="suspect.php" class="btn btn-dark mb-3">← Go Back</a>

      <form method="post" class="shadow p-4 bg-white" action="req/suspect-edit.php">
        <h4 class="mb-3">Edit Suspect</h4>
        <hr />

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>

        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>" />
        <input type="hidden" name="suspect_id" value="<?= htmlspecialchars($suspect['suspect_id']) ?>" />
        <input type="hidden" name="case_id" value="<?= htmlspecialchars($case['case_id'] ?? '') ?>" />

        <?php
        $suspectFields = [
            'fname' => 'First Name',
            'lname' => 'Last Name',
            'address' => 'Address',
            'email_address' => 'Email Address',
            'date_of_birth' => 'Date of Birth',
            'username' => 'Username',
            'parent_fname' => 'Parent First Name',
            'parent_lname' => 'Parent Last Name',
            'parent_phone_number' => 'Parent Phone Number'
        ];
        foreach ($suspectFields as $key => $label): ?>
            <div class="mb-3">
              <label class="form-label"><?= $label ?></label>
              <input 
                type="<?= $key === 'date_of_birth' ? 'date' : 'text' ?>"
                class="form-control"
                name="<?= $key ?>"
                value="<?= htmlspecialchars($suspect[$key]) ?>"
              />
            </div>
        <?php endforeach; ?>

        <div class="mb-3">
            <label class="form-label">Gender</label><br />
            <input type="radio" name="gender" value="Male" <?= $suspect['gender'] === 'Male' ? 'checked' : '' ?> /> Male
            &nbsp;&nbsp;
            <input type="radio" name="gender" value="Female" <?= $suspect['gender'] === 'Female' ? 'checked' : '' ?> /> Female
        </div>

        <hr />
        <h5>Case Information</h5>

        <div class="mb-3">
            <label class="form-label">Case Title</label>
            <input type="text" name="case_title" class="form-control" value="<?= htmlspecialchars($case['case_title'] ?? '') ?>" />
        </div>

        <div class="mb-3">
            <label class="form-label">Case Description</label>
            <textarea name="case_description" class="form-control" rows="4"><?= htmlspecialchars($case['case_description'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update Suspect</button>
      </form>
    </main>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  $(document).ready(function () {
    $("#navLinks li:nth-child(3) a").addClass('active');
  });
</script>
</body>
</html>

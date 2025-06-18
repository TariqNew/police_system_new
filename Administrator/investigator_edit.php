<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once "../DB_connection.php";
require_once "../req/logger.php";  


// Check user is admin and investigator_id provided
if (
    isset($_SESSION['admin_id'], $_SESSION['role'], $_GET['investigator_id']) &&
    $_SESSION['role'] === 'Admin'
) {
    include "data/investigator.php";

    $investigator_id = intval($_GET['investigator_id']);
    $investigator = getInvestigatorById($investigator_id, $conn);

    if (!$investigator) {
        // Investigator not found - redirect
        header("Location: investigator.php");
        exit;
    }

    // Log the view action here
    $user_id = $_SESSION['admin_id'];
    $role = $_SESSION['role'];
    $action = 'VIEW INVESTIGATOR EDIT PAGE';
    $description = "Viewed investigator edit page for investigator_id=$investigator_id";

    logAction($conn, $user_id, $role, $action, $description);

    // CSRF token setup
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    $csrf_token = $_SESSION['csrf_token'];
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin - Edit Investigator</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="icon" href="../logo.png" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
  <style>
    body {
      overflow: hidden;
    }
    main {
      height: calc(100vh - 56px);
      overflow-y: auto;
    }
    .form-w {
      max-width: 700px;
      margin: auto;
    }
  </style>
</head>
<body>

<?php include "inc/navbar.php"; ?>

<div class="container-fluid">
  <div class="row" style="padding-top: 56px;">

    <!-- Sidebar -->
    <?php include "inc/sidebar.php"; ?>

    <!-- Main content -->
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4 mt-4">

      <a href="investigator.php" class="btn btn-dark mb-3">← Go Back</a>

      <!-- Edit Form -->
      <form method="post" class="shadow p-4 w-100 bg-white" action="../req/investigator_edit.php">
        <h4 class="mb-3">Edit Investigator</h4>
        <hr />

        <?php if (isset($_GET['error'])) : ?>
          <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['success'])) : ?>
          <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>

        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>" />

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
        foreach ($fields as $name => $label): ?>
          <div class="mb-3">
            <label class="form-label"><?= $label ?></label>
            <input
              type="<?= $name === 'date_of_birth' ? 'date' : 'text' ?>"
              class="form-control"
              name="<?= $name ?>"
              value="<?= htmlspecialchars($investigator[$name]) ?>"
              required
            />
          </div>
        <?php endforeach; ?>

        <div class="mb-3">
          <label class="form-label">Gender</label><br />
          <input type="radio" value="Male" name="gender" required <?= $investigator['gender'] === 'Male' ? 'checked' : '' ?> /> Male &nbsp;&nbsp;
          <input type="radio" value="Female" name="gender" required <?= $investigator['gender'] === 'Female' ? 'checked' : '' ?> /> Female
        </div>

        <input type="hidden" name="investigator_id" value="<?= htmlspecialchars($investigator['investigator_id']) ?>" />
        <button type="submit" class="btn btn-primary">Update</button>
      </form>
    </main>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  $(document).ready(function () {
    $("#navLinks li:nth-child(2) a").addClass("active");
  });

  function makePass(length) {
    let result = "";
    const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%";
    for (let i = 0; i < length; i++) {
      result += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById("passInput").value = result;
    document.getElementById("passInput2").value = result;
  }

  document.getElementById("gBtn").addEventListener("click", function (e) {
    e.preventDefault();
    makePass(10);
  });
</script>
</body>
</html>

<?php
} else {
    // Redirect if not admin or no investigator id
    header("Location: investigator.php");
    exit;
}
?>

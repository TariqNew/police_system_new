<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (
    isset($_SESSION['admin_id']) &&
    isset($_SESSION['role']) &&
    isset($_GET['officer_id'])
) {
    if ($_SESSION['role'] === 'Admin') {
        include "../DB_connection.php";
        include "data/police_officer.php";

        $officer_id = intval($_GET['officer_id']);
        $officer = getOfficerById($officer_id, $conn);

        if ($officer == 0) {
            header("Location: police_officer.php");
            exit;
        }

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
  <title>Admin - Edit Officer</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <link rel="stylesheet" href="../css/style.css" />
  <link rel="icon" href="../logo.png" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
  <style>
    body {
      height: 100vh;
      overflow: hidden;
    }
    .sidebar {
      position: fixed;
      top: 56px;
      left: 0;
      height: calc(100vh - 56px);
      overflow-y: auto;
      background-color: #f8f9fa;
      padding: 1rem;
    }
    .main-content {
      margin-left: 16.666667%; /* col-md-2 */
      height: calc(100vh - 56px);
      overflow-y: auto;
      padding: 2rem;
    }
  </style>
</head>
<body class="d-flex flex-column">
  <?php include "inc/navbar.php"; ?>

  <div class="container-fluid flex-grow-1">
    <div class="row h-100" style="padding-top: 56px">
      <!-- Sidebar -->
      <div class="col-md-2 d-none d-md-block sidebar">
        <?php include "inc/sidebar.php"; ?>
      </div>

      <!-- Main content -->
      <main class="col-md-10 ms-auto main-content">
        <a href="police_officer.php" class="btn btn-dark mb-3">← Go Back</a>

        <!-- Edit Officer Form -->
        <form method="post" class="shadow p-3 w-100" action="police_officer_edit.php">
          <h3>Edit Officer</h3>
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
          ?>

          <?php foreach ($fields as $name => $label) : ?>
            <div class="mb-3">
              <label class="form-label"><?= htmlspecialchars($label) ?></label>
              <input
                type="<?= $name === 'date_of_birth' ? 'date' : 'text' ?>"
                class="form-control"
                name="<?= htmlspecialchars($name) ?>"
                value="<?= htmlspecialchars($officer[$name]) ?>"
                required
              />
            </div>
          <?php endforeach; ?>

          <div class="mb-3">
            <label class="form-label">Gender</label><br />
            <input type="radio" value="Male" name="gender" required <?= $officer['gender'] === 'Male' ? 'checked' : '' ?> /> Male &nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" value="Female" name="gender" required <?= $officer['gender'] === 'Female' ? 'checked' : '' ?> /> Female
          </div>

          <input type="hidden" name="officer_id" value="<?= htmlspecialchars($officer['officer_id']) ?>" />
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
      var result = "";
      var characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%";
      for (var i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * characters.length));
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
        header("Location: police_officer.php");
        exit;
    }
} else {
    header("Location: police_officer.php");
    exit;
}
?>

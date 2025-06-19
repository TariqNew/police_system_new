<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (
    (isset($_SESSION['officer_id']) && $_SESSION['role'] === 'Officer') ||
    (isset($_SESSION['investigator_id']) && $_SESSION['role'] === 'Investigator')
) {

    include "../DB_connection.php";

    $fname = $_GET['fname'] ?? '';
    $lname = $_GET['lname'] ?? '';
    $uname = $_GET['uname'] ?? '';
    $address = $_GET['address'] ?? '';
    $email = $_GET['email'] ?? '';
    $pfn = $_GET['pfn'] ?? '';
    $pln = $_GET['pln'] ?? '';
    $ppn = $_GET['ppn'] ?? '';
    $case_title = $_GET['case_title'] ?? '';
    $case_description = $_GET['case_description'] ?? '';
    $case_date = $_GET['case_date'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= $_SESSION['role'] ?> - Add Suspect & Case</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="icon" href="../logo.png" />
  <style>
    body {
      overflow-x: hidden;
    }
    .sidebar {
      position: fixed;
      top: 56px; /* height of navbar */
      bottom: 0;
      left: 0;
      width: 250px;
      overflow-y: auto;
      background-color: #f8f9fa;
      border-right: 1px solid #dee2e6;
      padding-top: 1rem;
    }
    main {
      margin-left: 250px;
    }
    @media (max-width: 768px) {
      .sidebar {
        position: static;
        width: 100%;
      }
      main {
        margin-left: 0;
      }
    }
  </style>
</head>
<body>

<?php include "./inc/navbar.php"; ?>

<div class="container-fluid">
  <div class="row vh-100">
    <div class="col-md-3 col-lg-2 sidebar">
      <?php include "./inc/sidebar.php"; ?>
    </div>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4 mt-3">
      <a href="suspect.php" class="btn btn-dark mb-4">Go Back</a>

      <form method="post" class="shadow p-4 bg-light" action="req/suspect_add.php">
        <h3>Add New Suspect</h3>
        <hr>

        <?php if (isset($_GET['error'])): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
          <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>

        <div class="mb-3">
          <label class="form-label">First Name</label>
          <input type="text" class="form-control" name="fname" value="<?= htmlspecialchars($fname) ?>" required />
        </div>

        <div class="mb-3">
          <label class="form-label">Last Name</label>
          <input type="text" class="form-control" name="lname" value="<?= htmlspecialchars($lname) ?>" required />
        </div>

        <div class="mb-3">
          <label class="form-label">Address</label>
          <input type="text" class="form-control" name="address" value="<?= htmlspecialchars($address) ?>" />
        </div>

        <div class="mb-3">
          <label class="form-label">Email Address</label>
          <input type="email" class="form-control" name="email_address" value="<?= htmlspecialchars($email) ?>" />
        </div>

        <div class="mb-3">
          <label class="form-label">Date of Birth</label>
          <input type="date" class="form-control" name="date_of_birth" required />
        </div>

        <div class="mb-3">
          <label class="form-label">Gender</label><br />
          <input type="radio" name="gender" value="Male" checked /> Male
          &nbsp;&nbsp;&nbsp;
          <input type="radio" name="gender" value="Female" /> Female
        </div>

        <hr />

        <div class="mb-3">
          <label class="form-label">Username</label>
          <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($uname) ?>" required />
        </div>

        <div class="mb-3">
          <label class="form-label">Password</label>
          <div class="input-group">
            <input type="text" class="form-control" name="pass" id="passInput" required />
            <button class="btn btn-secondary" id="gBtn">Random</button>
          </div>
        </div>

        <hr />

        <div class="mb-3">
          <label class="form-label">Parent First Name</label>
          <input type="text" class="form-control" name="parent_fname" value="<?= htmlspecialchars($pfn) ?>" />
        </div>

        <div class="mb-3">
          <label class="form-label">Parent Last Name</label>
          <input type="text" class="form-control" name="parent_lname" value="<?= htmlspecialchars($pln) ?>" />
        </div>

        <div class="mb-3">
          <label class="form-label">Parent Phone Number</label>
          <input type="text" class="form-control" name="parent_phone_number" value="<?= htmlspecialchars($ppn) ?>" />
        </div>

        <hr />
        <h4>Related Case Details (optional)</h4>

        <div class="mb-3">
          <label class="form-label">Case Title</label>
          <input type="text" class="form-control" name="case_title" value="<?= htmlspecialchars($case_title) ?>" />
        </div>

        <div class="mb-3">
          <label class="form-label">Case Description</label>
          <textarea class="form-control" name="case_description" rows="3"><?= htmlspecialchars($case_description) ?></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">Case Date</label>
          <input type="date" class="form-control" name="case_date" value="<?= htmlspecialchars($case_date) ?>" />
        </div>

        <button type="submit" class="btn btn-primary">Register Suspect & Case</button>
      </form>
    </main>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  $(document).ready(function () {
    $("#navLinks li:nth-child(3) a").addClass("active");
  });

  function makePass(length) {
    let result = "";
    const characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    for (let i = 0; i < length; i++) {
      result += characters.charAt(Math.floor(Math.random() * characters.length));
    }
    document.getElementById("passInput").value = result;
  }

  document.getElementById("gBtn").addEventListener("click", function (e) {
    e.preventDefault();
    makePass(6);
  });
</script>
</body>
</html>

<?php 
} else {
  header("Location: ../login.php");
  exit;
} 
?>

<?php
session_start();
require_once './data/log.php';
require_once '../req/logger.php';
require_once "../DB_connection.php";

// Escape output for HTML
function esc($v)
{
  return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}

// Map internal fields to human-friendly names for error messages
$fieldNames = [
  'fname' => 'First Name',
  'lname' => 'Last Name',
  'uname' => 'Username',
  'pass' => 'Password',
  'address' => 'Address',
  'en' => 'Employee Number',
  'pn' => 'Phone Number',
  'qf' => 'Qualification',
  'email_address' => 'Email Address',
  'gender' => 'Gender',
  'date_of_birth' => 'Date of Birth'
];

// Flash data and error/success messages
$old = $_SESSION['form_data'] ?? [];
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['form_data'], $_SESSION['error'], $_SESSION['success']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    // Check required fields
    foreach ($fieldNames as $field => $label) {
      if (empty($_POST[$field])) {
        throw new Exception("The field '$label' is required.");
      }
    }

    // Trim and assign variables
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $username = trim($_POST['uname']);
    $password = $_POST['pass'];
    $address = trim($_POST['address']);
    $employee_number = trim($_POST['en']);
    $phone_number = trim($_POST['pn']);
    $qualification = trim($_POST['qf']);
    $email = trim($_POST['email_address']);
    $gender = $_POST['gender'];
    $dob = $_POST['date_of_birth'];

    // Validate username length
    if (strlen($username) < 3 || strlen($username) > 50) {
      throw new Exception("Username must be between 3 and 50 characters.");
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      throw new Exception("Invalid email address format.");
    }

    // Validate gender
    if (!in_array($gender, ['Male', 'Female'])) {
      throw new Exception("Invalid gender selected.");
    }

    // Check unique username
    $stmt = $conn->prepare("SELECT officer_id FROM officers WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->rowCount() > 0) {
      throw new Exception("Username already exists.");
    }

    // Check unique email
    $stmt = $conn->prepare("SELECT officer_id FROM officers WHERE email_address = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
      throw new Exception("Email address already exists.");
    }

    // Hash password securely
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new officer
    $stmt = $conn->prepare("INSERT INTO officers 
  (fname, lname, username, password, address, employee_number, phone_number, qualification, email_address, gender, date_of_birth)
  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");


    $stmt->execute([
      $fname,
      $lname,
      $username,
      $hashed_password,
      $address,
      $employee_number,
      $phone_number,
      $qualification,
      $email,
      $gender,
      $dob
    ]); 

    $user_id = $_SESSION['admin_id'] ?? 0;
    $role = $_SESSION['role'] ?? 'Unknown';
    $action = 'ADD POLICE OFFICER';
    $description = "Added new police officer: username='$username', employee_number='$employee_number'";

    logAction($conn, $user_id, $role, $action, $description);

    $logger->log('info', "New officer registered: $username");

    $_SESSION['success'] = "Officer added successfully.";
    header("Location: police_officer.php");
    exit;

  } catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    $_SESSION['form_data'] = $_POST;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Add Police Officer</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="icon" href="../ifm-logo.jpg">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body class="bg-light">

  <?php include "./inc/navbar.php"; ?>

  <div class="container-fluid">
    <div class="row" style="padding-top: 56px;">
      <?php include "./inc/sidebar.php"; ?>

      <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4 mt-3">

        <h2 class="mb-4">Add New Police Officer</h2>

        <?php if ($error): ?>
          <div class="alert alert-danger"><?= esc($error) ?></div>
        <?php elseif ($success): ?>
          <div class="alert alert-success"><?= esc($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= esc($_SERVER['PHP_SELF']) ?>" class="shadow p-4 bg-white rounded">

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">First Name</label>
              <input type="text" name="fname" class="form-control" value="<?= esc($old['fname'] ?? '') ?>" required />
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Last Name</label>
              <input type="text" name="lname" class="form-control" value="<?= esc($old['lname'] ?? '') ?>" required />
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Username</label>
              <input type="text" name="uname" class="form-control" value="<?= esc($old['uname'] ?? '') ?>" required />
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Password</label>
              <div class="input-group">
                <input type="text" name="pass" id="passInput" class="form-control" required />
                <button class="btn btn-secondary" id="gBtn">Random</button>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Address</label>
              <input type="text" name="address" class="form-control" value="<?= esc($old['address'] ?? '') ?>"
                required />
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Employee Number</label>
              <input type="text" name="en" class="form-control" value="<?= esc($old['en'] ?? '') ?>" required />
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Phone Number</label>
              <input type="text" name="pn" class="form-control" value="<?= esc($old['pn'] ?? '') ?>" required />
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Qualification</label>
              <input type="text" name="qf" class="form-control" value="<?= esc($old['qf'] ?? '') ?>" required />
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Email Address</label>
              <input type="email" name="email_address" class="form-control"
                value="<?= esc($old['email_address'] ?? '') ?>" required />
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Gender</label><br />
              <div class="form-check form-check-inline">
                <input type="radio" name="gender" value="Male" id="genderMale" class="form-check-input"
                  <?= (isset($old['gender']) && $old['gender'] === 'Male') ? 'checked' : '' ?> required />
                <label for="genderMale" class="form-check-label">Male</label>
              </div>
              <div class="form-check form-check-inline">
                <input type="radio" name="gender" value="Female" id="genderFemale" class="form-check-input"
                  <?= (isset($old['gender']) && $old['gender'] === 'Female') ? 'checked' : '' ?> />
                <label for="genderFemale" class="form-check-label">Female</label>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Date of Birth</label>
              <input type="date" name="date_of_birth" class="form-control"
                value="<?= esc($old['date_of_birth'] ?? '') ?>" required />
            </div>
            <div class="col-md-6 mb-3 d-flex justify-content-center">
              <a href="../req/fingerprint.php" class="text-decoration-none text-center">
                <div class="border rounded p-3 shadow-sm" style="width: 160px; cursor: pointer;">
                  <img src="../img/fingerPrint_01.png" alt="Fingerprint Icon" style="width: 80px; height: 90px;">
                  <div class="mt-2 text-dark" style="font-size: 14px;">Add a fingerprint</div>
                </div>
              </a>
            </div>

          </div>

          <button type="submit" class="btn btn-primary mt-3">Add Officer</button>
        </form>

      </main>
    </div>
  </div>

  <script>
    document.getElementById('gBtn').addEventListener('click', function (e) {
      e.preventDefault();
      const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
      let result = '';
      for (let i = 0; i < 8; i++) {
        result += chars.charAt(Math.floor(Math.random() * chars.length));
      }
      document.getElementById('passInput').value = result;
    });
  </script>

</body>

</html>
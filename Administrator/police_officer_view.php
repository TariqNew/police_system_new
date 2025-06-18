<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Admin') {
        include "../DB_connection.php";
        include "data/police_officer.php";

        if (isset($_GET['officer_id'])) {

            $officer_id = $_GET['officer_id'];
            $officer = getOfficerById($officer_id, $conn);

            if ($officer != 0) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin - Officer Details</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="icon" href="../logo.png" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
</head>
<body>

<?php include "inc/navbar.php"; ?>

<div class="container-fluid">
  <div class="row" style="padding-top: 56px;">

    <!-- Sidebar -->
    <?php include "inc/sidebar.php"; ?>

    <!-- Main content -->
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4 mt-4">

      <div class="card shadow-lg p-3">
        <div class="row g-0">

          <!-- Officer Image -->
          <div class="col-md-4 text-center">
            <img src="../img/officer-Male.png" alt="Officer" class="img-fluid rounded-start" style="max-height: 300px;">
            <h5 class="mt-3">@<?= htmlspecialchars($officer['username']) ?></h5>
          </div>

          <!-- Officer Details -->
          <div class="col-md-8">
            <div class="card-body">
              <h4 class="card-title mb-3">Officer Details</h4>

              <div class="row mb-3">
                <div class="col-sm-6"><strong>First Name:</strong> <?= htmlspecialchars($officer['fname']) ?></div>
                <div class="col-sm-6"><strong>Last Name:</strong> <?= htmlspecialchars($officer['lname']) ?></div>
              </div>

              <div class="row mb-3">
                <div class="col-sm-6"><strong>Username:</strong> <?= htmlspecialchars($officer['username']) ?></div>
                <div class="col-sm-6"><strong>Employee Number:</strong> <?= htmlspecialchars($officer['employee_number']) ?></div>
              </div>

              <div class="row mb-3">
                <div class="col-sm-6"><strong>Date of Birth:</strong> <?= htmlspecialchars($officer['date_of_birth']) ?></div>
                <div class="col-sm-6"><strong>Gender:</strong> <?= htmlspecialchars($officer['gender']) ?></div>
              </div>

              <div class="row mb-3">
                <div class="col-sm-6"><strong>Phone Number:</strong> <?= htmlspecialchars($officer['phone_number']) ?></div>
                <div class="col-sm-6"><strong>Email Address:</strong> <?= htmlspecialchars($officer['email_address']) ?></div>
              </div>

              <div class="row mb-3">
                <div class="col-sm-6"><strong>Address:</strong> <?= htmlspecialchars($officer['address']) ?></div>
                <div class="col-sm-6"><strong>Qualification:</strong> <?= htmlspecialchars($officer['qualification']) ?></div>
              </div>

              <div class="mt-4 text-end">
                <a href="police_officer.php" class="btn btn-secondary">← Go Back</a>
              </div>

            </div>
          </div>

        </div>
      </div>

    </main>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>  
<script>
  $(document).ready(function () {
    $("#navLinks li:nth-child(2) a").addClass('active');
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

    } else {
        header("Location: ../login.php");
        exit;
    }
} else {
    header("Location: ../login.php");
    exit;
}
?>

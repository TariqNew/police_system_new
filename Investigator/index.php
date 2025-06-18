<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (isset($_SESSION['investigator_id']) && isset($_SESSION['role'])) {

  if ($_SESSION['role'] == 'Investigator') {
    include "../DB_connection.php";
    include "data/investigator.php";

    $investigator_id = $_SESSION['investigator_id'];
    $investigator = getInvestigatorById($investigator_id, $conn);
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Investigator - Home</title>
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
      <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
      <link rel="icon" href="">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    </head>

    <body>
      <!-- Navbar -->
      <?php include "./inc/navbar.php"; ?>

      <div class="container-fluid">
        <div class="row" style="padding-top: 56px;">
          <!-- Sidebar -->
          <?php include "./inc/sidebar.php"; ?>

          <!-- Main content -->
          <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4">
            <?php if ($investigator != 0): ?>
              <div class="container shadow-sm p-4 rounded bg-white">
                <div class="row mb-4">
                  <div class="col-md-3 text-center">
                    <img src="../img/officer-<?= htmlspecialchars($investigator['gender']) ?>.png" alt="Profile Image"
                      class="img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                  </div>
                  <div class="col-md-9 d-flex flex-column justify-content-center">
                    <h3>@<?= htmlspecialchars($investigator['username']) ?></h3>
                    <p class="text-muted">Investigator</p>
                  </div>
                </div>

                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label fw-bold">First Name:</label>
                    <div><?= htmlspecialchars($investigator['fname']) ?></div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-bold">Last Name:</label>
                    <div><?= htmlspecialchars($investigator['lname']) ?></div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-bold">Username:</label>
                    <div><?= htmlspecialchars($investigator['username']) ?></div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-bold">Employee Number:</label>
                    <div><?= htmlspecialchars($investigator['employee_number']) ?></div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-bold">Gender:</label>
                    <div><?= htmlspecialchars($investigator['gender']) ?></div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-bold">Date of Birth:</label>
                    <div><?= htmlspecialchars($investigator['date_of_birth']) ?></div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-bold">Phone Number:</label>
                    <div><?= htmlspecialchars($investigator['phone_number']) ?></div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-bold">Email Address:</label>
                    <div><?= htmlspecialchars($investigator['email_address']) ?></div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-bold">Address:</label>
                    <div><?= htmlspecialchars($investigator['address']) ?></div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-bold">Date Joined:</label>
                    <div><?= htmlspecialchars($investigator['date_of_joined']) ?></div>
                  </div>
                  <div class="col-md-12">
                    <label class="form-label fw-bold">Qualification:</label>
                    <div><?= htmlspecialchars($investigator['qualification']) ?></div>
                  </div>
                </div>
              </div>
            <?php else: ?>
              <div class="alert alert-danger">Error loading investigator profile.</div>
            <?php endif; ?>
          </main>

        </div>
      </div>

      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
      <script>
        $(document).ready(function () {
          $("#navLinks li:nth-child(1) a").addClass('active');
        });
      </script>
    </body>

    </html>
    <?php
  } else {
    header("Location: ../login.php");
    exit;
  }
} else {
  header("Location: ../login.php");
  exit;
}
?>

<?php
session_start();
if (
  isset($_SESSION['admin_id']) &&
  isset($_SESSION['role']) &&
  $_SESSION['role'] == 'Admin'
) {
  include "../DB_connection.php";
  include "data/investigator.php";

  if (isset($_GET['investigator_id'])) {
    $investigator_id = $_GET['investigator_id'];
    $investigator = getInvestigatorById($investigator_id, $conn);
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Admin - Investigator Details</title>
      <link rel="icon" href="../logo.png">
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
      <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    </head>

    <body>
      <?php include "inc/navbar.php"; ?>

      <?php if ($investigator != 0) { ?>
        <div class="container-fluid">
          <div class="row" style="padding-top: 56px;">
            <?php include "inc/sidebar.php"; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4 mt-4">
              <div class="card shadow-lg p-3">
                <div class="row g-0">

                  <!-- Image -->
                  <div class="col-md-4 text-center">
                    <img src="../img/officer-<?= $investigator['gender'] ?>.png" alt="Investigator" class="img-fluid rounded-start" style="max-height: 300px;">
                    <h5 class="mt-3">@<?= htmlspecialchars($investigator['username']) ?></h5>
                  </div>

                  <!-- Details -->
                  <div class="col-md-8">
                    <div class="card-body">
                      <h4 class="card-title mb-3">Investigator Details</h4>
                      <div class="row mb-4">
                        <div class="col-sm-6"><strong>First Name:</strong> <?= htmlspecialchars($investigator['fname']) ?></div>
                        <div class="col-sm-6"><strong>Last Name:</strong> <?= htmlspecialchars($investigator['lname']) ?></div>
                      </div>
                      <div class="row mb-4">
                        <div class="col-sm-6"><strong>Username:</strong> <?= htmlspecialchars($investigator['username']) ?></div>
                        <div class="col-sm-6"><strong>Employee No:</strong> <?= htmlspecialchars($investigator['employee_number']) ?></div>
                      </div>
                      <div class="row mb-4">
                        <div class="col-sm-6"><strong>Date of Birth:</strong> <?= htmlspecialchars($investigator['date_of_birth']) ?></div>
                        <div class="col-sm-6"><strong>Gender:</strong> <?= htmlspecialchars($investigator['gender']) ?></div>
                      </div>
                      <div class="row mb-4">
                        <div class="col-sm-6"><strong>Phone Number:</strong> <?= htmlspecialchars($investigator['phone_number']) ?></div>
                        <div class="col-sm-6"><strong>Email:</strong> <?= htmlspecialchars($investigator['email_address']) ?></div>
                      </div>
                      <div class="row mb-4">
                        <div class="col-sm-6"><strong>Qualification:</strong> <?= htmlspecialchars($investigator['qualification']) ?></div>
                        <div class="col-sm-6"><strong>Date Joined:</strong> <?= htmlspecialchars($investigator['date_of_joined']) ?></div>
                      </div>
                      <div class="row mb-4">
                        <div class="col-12"><strong>Address:</strong> <?= htmlspecialchars($investigator['address']) ?></div>
                      </div>
                      <div class="mt-4 text-end">
                        <a href="investigator.php" class="btn btn-secondary">← Go Back</a>
                      </div>
                    </div>
                  </div>

                </div>
              </div>
            </main>
          </div>
        </div>
      <?php } else {
        header("Location: investigator.php");
        exit;
      } ?>

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
    header("Location: investigator.php");
    exit;
  }
} else {
  header("Location: ../login.php");
  exit;
}
?>

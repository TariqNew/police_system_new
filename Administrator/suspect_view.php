<?php
session_start();
if (
  isset($_SESSION['officer_id']) &&
  isset($_SESSION['role']) &&
  $_SESSION['role'] == 'Officer'
) {
  include "../DB_connection.php";
  include "data/suspect.php";

  if (isset($_GET['suspect_id'])) {
    $suspect_id = $_GET['suspect_id'];
    $suspect = getSuspectById($suspect_id, $conn);

    function getCriminalCasesBySuspectId($conn, $suspect_id) {
      $stmt = $conn->prepare("SELECT * FROM criminal_cases WHERE suspect_id = ?");
      $stmt->execute([$suspect_id]);
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $cases = getCriminalCasesBySuspectId($conn, $suspect_id);
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title>Officer - Suspect Details</title>
      <link rel="icon" href="../logo.png" />
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" />
      <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    </head>

    <body>
      <?php include "inc/navbar.php"; ?>

      <?php if ($suspect != 0) { ?>
        <div class="container-fluid">
          <div class="row" style="padding-top: 56px;">
            <?php include "inc/sidebar.php"; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4 mt-4">
              <div class="card shadow-lg p-3 mb-5">
                <div class="row g-0">

                  <!-- Image -->
                  <div class="col-md-4 text-center">
                    <img src="../img/suspect-<?= $suspect['gender'] ?>.png" alt="Suspect" class="img-fluid rounded-start" style="max-height: 300px;">
                    <h5 class="mt-3">@<?= htmlspecialchars($suspect['username']) ?></h5>
                  </div>

                  <!-- Details -->
                  <div class="col-md-8">
                    <div class="card-body">
                      <h4 class="card-title mb-3">Suspect Details</h4>

                      <div class="row mb-4">
                        <div class="col-sm-6"><strong>First Name:</strong> <?= htmlspecialchars($suspect['fname']) ?></div>
                        <div class="col-sm-6"><strong>Last Name:</strong> <?= htmlspecialchars($suspect['lname']) ?></div>
                      </div>

                      <div class="row mb-4">
                        <div class="col-sm-6"><strong>Username:</strong> <?= htmlspecialchars($suspect['username']) ?></div>
                        <div class="col-sm-6"><strong>Gender:</strong> <?= htmlspecialchars($suspect['gender']) ?></div>
                      </div>

                      <div class="row mb-4">
                        <div class="col-sm-6"><strong>Date of Birth:</strong> <?= htmlspecialchars($suspect['date_of_birth']) ?></div>
                        <div class="col-sm-6"><strong>Email:</strong> <?= htmlspecialchars($suspect['email_address']) ?></div>
                      </div>

                      <div class="row mb-4">
                        <div class="col-sm-6"><strong>Address:</strong> <?= htmlspecialchars($suspect['address']) ?></div>
                        <div class="col-sm-6"><strong>Date Joined:</strong> <?= htmlspecialchars($suspect['date_of_joined']) ?></div>
                      </div>

                      <div class="row mb-4">
                        <div class="col-sm-6"><strong>Parent First Name:</strong> <?= htmlspecialchars($suspect['parent_fname']) ?></div>
                        <div class="col-sm-6"><strong>Parent Last Name:</strong> <?= htmlspecialchars($suspect['parent_lname']) ?></div>
                      </div>

                      <div class="row mb-4">
                        <div class="col-sm-6"><strong>Parent Phone:</strong> <?= htmlspecialchars($suspect['parent_phone_number']) ?></div>
                      </div>

                      <div class="mt-4 text-end">
                        <a href="suspect.php" class="btn btn-secondary">← Go Back</a>
                      </div>
                    </div>
                  </div>

                </div>
              </div>

              <!-- CASES TABLE -->
              <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                  Criminal Case(s) Accused
                </div>
                <div class="card-body p-0">
                  <?php if (count($cases) > 0): ?>
                    <div class="table-responsive">
                      <table class="table table-bordered m-0">
                        <thead class="table-light">
                          <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Date Reported</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($cases as $index => $case): ?>
                            <tr>
                              <td><?= $index + 1 ?></td>
                              <td><?= htmlspecialchars($case['case_title']) ?></td>
                              <td><?= htmlspecialchars($case['case_description']) ?></td>
                              <td><?= htmlspecialchars($case['date_reported']) ?></td>
                            </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    </div>
                  <?php else: ?>
                    <div class="p-3 text-muted">No cases found for this suspect.</div>
                  <?php endif; ?>
                </div>
              </div>

            </main>
          </div>
        </div>
      <?php } else {
        header("Location: suspect.php");
        exit;
      } ?>

      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
      <script>
        $(document).ready(function () {
          $("#navLinks li:nth-child(3) a").addClass('active');
        });
      </script>
    </body>

    </html>
    <?php
  } else {
    header("Location: suspect.php");
    exit;
  }
} else {
  header("Location: ../login.php");
  exit;
}
?>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (isset($_SESSION['investigator_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Investigator') {

  include "../DB_connection.php";
  include "data/suspect.php";
  $suspects = getAllSuspects($conn);
  ?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Investigator - Suspects</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" href="../logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
      .clickable-row:hover {
        background-color: #f1f1f1;
      }
    </style>
  </head>

  <body>

    <?php include "./inc/navbar.php"; ?>

    <div class="container-fluid">
      <div class="row" style="padding-top: 56px;">

        <!-- Sidebar -->
        <?php include "./inc/sidebar.php"; ?>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4 mt-3">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Suspects</h2>
            <a href="suspect_add.php" class="btn btn-dark">Add New Suspect</a>
          </div>

          <!-- Search Form -->
          <form action="suspect_search.php" method="get" class="mb-4">
            <div class="input-group">
              <input type="text" name="searchKey" class="form-control" placeholder="Search...">
              <button class="btn btn-primary" type="submit">
                <i class="fa fa-search"></i>
              </button>
            </div>
          </form>

          <!-- Alerts -->
          <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
          <?php endif; ?>

          <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-info"><?= htmlspecialchars($_GET['success']) ?></div>
          <?php endif; ?>

          <!-- Table -->
          <?php if ($suspects != 0): ?>
            <div class="table-responsive">
              <table class="table table-bordered table-hover">
                <thead class="table-dark">
                  <tr>
                    <th>#</th>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Username</th>
                    <th>Crime Committed</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i = 1;
                  foreach ($suspects as $suspect):
                    $latest_case = getLatestCaseTitleBySuspect($conn, $suspect['suspect_id']);
                    ?>
                    <tr class="clickable-row" data-href="suspect_view.php?suspect_id=<?= $suspect['suspect_id'] ?>">
                      <td><?= $i++ ?></td>
                      <td><?= $suspect['suspect_id'] ?></td>
                      <td><?= htmlspecialchars($suspect['fname']) ?></td>
                      <td><?= htmlspecialchars($suspect['lname']) ?></td>
                      <td><?= htmlspecialchars($suspect['username']) ?></td>
                      <td><?= htmlspecialchars($latest_case) ?></td>
                      <td>
                        <a href="suspect_edit.php?suspect_id=<?= $suspect['suspect_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="suspect_delete.php?suspect_id=<?= $suspect['suspect_id'] ?>" class="btn btn-danger btn-sm">Delete</a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <div class="alert alert-info">No suspects found.</div>
          <?php endif; ?>
        </main>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      $(document).ready(function () {
        $(".clickable-row").css("cursor", "pointer").click(function (e) {
          if (!$(e.target).closest("a").length) {
            window.location = $(this).data("href");
          }
        });

        $("#navLinks li:nth-child(3) a").addClass('active');
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

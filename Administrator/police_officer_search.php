<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {

  if (isset($_GET['searchKey'])) {
    include "../DB_connection.php";
    include "data/police_officer.php";

    $search_key = htmlspecialchars($_GET['searchKey']);
    $officers = searchOfficers($search_key, $conn);
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Admin - Search Officers</title>
      <link rel="icon" href="../logo.png">
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    </head>

    <body>

      <?php include "inc/navbar.php"; ?>

      <div class="container-fluid">
        <div class="row" style="padding-top: 56px;">
          <?php include "inc/sidebar.php"; ?>

          <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4 mt-3">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <a href="police_officer.php" class="btn btn-dark mb-3">← Go Back</a>
            </div>

            <form action="police_officer_search.php" method="get" class="mb-4">
              <div class="input-group">
                <input type="text" name="searchKey" class="form-control" placeholder="Search..." value="<?= $search_key ?>" required>
                <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i></button>
              </div>
            </form>

            <?php if (isset($_GET['error'])) { ?>
              <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php } ?>
            <?php if (isset($_GET['success'])) { ?>
              <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php } ?>

            <?php if ($officers != 0) { ?>
              <div class="table-responsive">
                <table class="table table-bordered table-striped">
                  <thead class="table-dark">
                    <tr>
                      <th>#</th>
                      <th>ID</th>
                      <th>First Name</th>
                      <th>Last Name</th>
                      <th>Username</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $i = 0;
                    foreach ($officers as $officer) {
                      $i++; ?>
                      <tr>
                        <td><?= $i ?></td>
                        <td><?= htmlspecialchars($officer['officer_id']) ?></td>
                        <td>
                          <a href="police_officer_view.php?officer_id=<?= htmlspecialchars($officer['officer_id']) ?>">
                            <?= htmlspecialchars($officer['fname']) ?>
                          </a>
                        </td>
                        <td><?= htmlspecialchars($officer['lname']) ?></td>
                        <td><?= htmlspecialchars($officer['username']) ?></td>
                        <td>
                          <a href="police_officer_edit.php?officer_id=<?= htmlspecialchars($officer['officer_id']) ?>"
                             class="btn btn-warning btn-sm">Edit</a>
                          <a href="police_officer_delete.php?officer_id=<?= htmlspecialchars($officer['officer_id']) ?>"
                             class="btn btn-danger btn-sm">Delete</a>
                        </td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            <?php } else { ?>
              <div class="alert alert-info">No results found for <strong><?= $search_key ?></strong></div>
              <a href="police_officer.php" class="btn btn-dark">← Go Back</a>
            <?php } ?>
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
  header("Location: ../login.php");
  exit;
}
?>

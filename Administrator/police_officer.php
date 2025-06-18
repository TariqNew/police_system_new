<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (isset($_SESSION['admin_id']) && $_SESSION['role'] == 'Admin') {
  include "../DB_connection.php";
  include "data/police_officer.php";
  $officers = getAllOfficers($conn);
  ?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8">
    <title>CRMS - Police Officers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" href="../ifm-logo.jpg">
  </head>

  <body>

    <?php include "./inc/navbar.php"; ?>

    <div class="container-fluid">
      <div class="row" style="padding-top: 56px;">

        <?php include "./inc/sidebar.php"; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4 mt-3">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Police Officers</h2>
            <a href="police_officer_add.php" class="btn btn-dark">
              <i class="bi bi-plus-circle"></i> Add New Officer
            </a>
          </div>

          <!-- Search Form -->
          <form action="police_officer_search.php" method="get" class="mb-4">
            <div class="input-group">
              <input type="text" name="searchKey" class="form-control" placeholder="Search...">
              <button class="btn btn-primary" type="submit">
                <i class="bi bi-search"></i>
              </button>
            </div>
          </form>

          <!-- Alerts -->
          <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?= $_GET['error'] ?></div>
          <?php endif; ?>
          <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-info"><?= $_GET['success'] ?></div>
          <?php endif; ?>

          <!-- Table -->
          <?php if ($officers != 0): ?>
            <div class="table-responsive">
              <table class="table table-bordered table-hover">
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
                  <?php $i = 1;
                  foreach ($officers as $officer): ?>
                    <tr class="cursor-pointer"
                      onclick="window.location.href='police_officer_view.php?officer_id=<?= $officer['officer_id'] ?>';"
                      style="cursor: pointer;">
                      <td><?= $i++ ?></td>
                      <td><?= $officer['officer_id'] ?></td>
                      <td><?= $officer['fname'] ?></td>
                      <td><?= $officer['lname'] ?></td>
                      <td><?= $officer['username'] ?></td>
                      <td>
                        <a href="police_officer_edit.php?officer_id=<?= $officer['officer_id'] ?>"
                          class="btn btn-warning btn-sm" onclick="event.stopPropagation();">
                          <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <a href="police_officer_delete.php?officer_id=<?= $officer['officer_id'] ?>"
                          class="btn btn-danger btn-sm" onclick="event.stopPropagation();">
                          <i class="bi bi-trash"></i> Delete
                        </a>
                      </td>
                    </tr>

                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <div class="alert alert-info">No police officers found.</div>
          <?php endif; ?>
        </main>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </body>

  </html>
<?php
} else {
  header("Location: ../login.php");
  exit;
}
?>
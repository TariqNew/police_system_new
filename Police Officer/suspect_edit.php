<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (
  isset($_SESSION['officer_id']) &&
  isset($_SESSION['role']) &&
  $_SESSION['role'] == 'Officer' &&
  isset($_GET['suspect_id'])
) {
  include "../DB_connection.php";
  include "data/suspect.php";

  $suspect_id = $_GET['suspect_id'];
  $suspect = getSuspectById($suspect_id, $conn);
  $case = getCaseBySuspectId($suspect_id, $conn);

  if ($suspect == 0) {
    header("Location: suspect.php");
    exit;
  }
  ?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8">
    <title>Edit Suspect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" href="../logo.png">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  </head>

  <body class="bg-light">
    <?php include "./inc/navbar.php"; ?>

    <div class="container-fluid">
      <div class="row" style="padding-top: 56px;">
        <?php include "./inc/sidebar.php"; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4" style="height: 100vh; overflow: hidden;">
          <h2 class="my-3">Edit Suspect Info</h2>

          <div class="row g-4" style="height: calc(100vh - 100px);">
            <!-- Full-width form column with scroll -->
            <div class="col-12 bg-white shadow rounded p-4 overflow-auto" style="height: 100%; width: 100%;">

              <a href="suspect.php" class="btn btn-dark mb-3">Go Back</a>
              <h4>Update Suspect</h4>
              <hr>

              <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger"><?= $_GET['error'] ?></div>
              <?php endif; ?>
              <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success"><?= $_GET['success'] ?></div>
              <?php endif; ?>

              <form method="post" action="req/suspect-edit.php">
                <input type="hidden" name="suspect_id" value="<?= $suspect['suspect_id'] ?>">
                <input type="hidden" name="case_id" value="<?= $case['case_id'] ?? '' ?>">

                <div class="mb-3"><label>First name</label>
                  <input type="text" name="fname" class="form-control" value="<?= $suspect['fname'] ?>">
                </div>
                <div class="mb-3"><label>Last name</label>
                  <input type="text" name="lname" class="form-control" value="<?= $suspect['lname'] ?>">
                </div>
                <div class="mb-3"><label>Address</label>
                  <input type="text" name="address" class="form-control" value="<?= $suspect['address'] ?>">
                </div>
                <div class="mb-3"><label>Email address</label>
                  <input type="email" name="email_address" class="form-control" value="<?= $suspect['email_address'] ?>">
                </div>
                <div class="mb-3"><label>Date of birth</label>
                  <input type="date" name="date_of_birth" class="form-control" value="<?= $suspect['date_of_birth'] ?>">
                </div>
                <div class="mb-3">
                  <label>Gender</label><br>
                  <input type="radio" name="gender" value="Male" <?= $suspect['gender'] == 'Male' ? 'checked' : '' ?>> Male
                  &nbsp;&nbsp;
                  <input type="radio" name="gender" value="Female" <?= $suspect['gender'] == 'Female' ? 'checked' : '' ?>>
                  Female
                </div>
                <div class="mb-3"><label>Username</label>
                  <input type="text" name="username" class="form-control" value="<?= $suspect['username'] ?>">
                </div>

                <hr>
                <div class="mb-3"><label>Parent First Name</label>
                  <input type="text" name="parent_fname" class="form-control" value="<?= $suspect['parent_fname'] ?>">
                </div>
                <div class="mb-3"><label>Parent Last Name</label>
                  <input type="text" name="parent_lname" class="form-control" value="<?= $suspect['parent_lname'] ?>">
                </div>
                <div class="mb-3"><label>Parent Phone Number</label>
                  <input type="text" name="parent_phone_number" class="form-control"
                    value="<?= $suspect['parent_phone_number'] ?>">
                </div>

                <hr>
                <h5>Case Information</h5>
                <div class="mb-3"><label>Case Title</label>
                  <input type="text" name="case_title" class="form-control" value="<?= $case['case_title'] ?? '' ?>">
                </div>
                <div class="mb-3"><label>Case Description</label>
                  <textarea name="case_description" class="form-control"
                    rows="4"><?= $case['case_description'] ?? '' ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
              </form>
            </div>
          </div>
        </main>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      $(document).ready(function () {
        $("#navLinks li:nth-child(3) a").addClass('active');
      });

      function makePass(length) {
        let result = '';
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        for (let i = 0; i < length; i++) {
          result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('passInput').value = result;
        document.getElementById('passInput2').value = result;
      }

      document.getElementById('gBtn').addEventListener('click', function (e) {
        e.preventDefault();
        makePass(6);
      });
    </script>
  </body>

  </html>
  <?php
} else {
  header("Location: suspect.php");
  exit;
}
?>
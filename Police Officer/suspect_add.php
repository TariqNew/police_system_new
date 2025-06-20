<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (isset($_SESSION['officer_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Officer') {

  include "../DB_connection.php";

  // Preserve form values on error
  $fname = $_GET['fname'] ?? '';
  $lname = $_GET['lname'] ?? '';
  $uname = $_GET['uname'] ?? '';
  $address = $_GET['address'] ?? '';
  $email = $_GET['email'] ?? '';
  $pfn = $_GET['pfn'] ?? '';
  $pln = $_GET['pln'] ?? '';
  $ppn = $_GET['ppn'] ?? '';

  // Case fields (optional prefill)
  $case_title = $_GET['case_title'] ?? '';
  $case_description = $_GET['case_description'] ?? '';
  $case_date = $_GET['case_date'] ?? '';
  ?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Officer - Add Suspect & Case</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" href="../logo.png" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  </head>

  <body>

    <?php include "./inc/navbar.php"; ?>

    <div class="container-fluid">
      <div class="row" style="padding-top: 56px;">
        <?php include "./inc/sidebar.php"; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4 mt-3"
          style="height: calc(100vh - 56px); overflow-y: auto;">
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

            <!-- Suspect Details -->
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
            <!-- Case Details -->
            <h4>Related Case Details (optional)</h4>

            <div class="mb-3">
              <label class="form-label">Case Title</label>
              <input type="text" class="form-control" name="case_title" value="<?= htmlspecialchars($case_title) ?>" />
            </div>

            <div class="mb-3">
              <label class="form-label">Case Description</label>
              <textarea class="form-control" name="case_description"
                rows="3"><?= htmlspecialchars($case_description) ?></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label">Case Date</label>
              <input type="date" class="form-control" name="case_date" value="<?= htmlspecialchars($case_date) ?>" />
            </div>

            <div class="col-md-12 d-flex justify-content-center mb-3">
              <a href="../req/fingerprint.php" class="text-decoration-none text-center">
                <div class="border rounded p-3 shadow-sm bg-light" style="width: 160px; cursor: pointer;">
                  <img src="../img/fingerPrint_01.png" alt="Fingerprint Icon" style="width: 80px; height: 90px;">
                  <div class="mt-2 text-dark" style="font-size: 14px;">Add a fingerprint</div>
                </div>
              </a>
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
        const characters =
          "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
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
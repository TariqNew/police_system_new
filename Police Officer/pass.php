<?php
session_start();
if (isset($_SESSION['officer_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Officer') {
?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Officer - Change Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" href="../logo.png">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  </head>

  <body>

    <?php include "inc/navbar.php"; ?>

    <div class="container-fluid">
      <div class="row" style="padding-top: 56px;">
        <?php include "inc/sidebar.php"; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4">
          <div class="d-flex justify-content-center align-items-center flex-column">
            <form method="post" action="req/officer-change.php" class="shadow p-4 mb-5 bg-white rounded" style="max-width: 500px; width: 100%;">
              <h3 class="text-center">Change Password</h3>
              <hr>

              <?php if (isset($_GET['perror'])) { ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_GET['perror']) ?></div>
              <?php } ?>
              <?php if (isset($_GET['psuccess'])) { ?>
                <div class="alert alert-success"><?= htmlspecialchars($_GET['psuccess']) ?></div>
              <?php } ?>

              <div class="mb-3">
                <label class="form-label">Old Password</label>
                <input type="password" class="form-control" name="old_pass" required>
              </div>

              <div class="mb-3">
                <label class="form-label">New Password</label>
                <input type="password" class="form-control" name="new_pass" required>
              </div>

              <div class="mb-3">
                <label class="form-label">Confirm New Password</label>
                <input type="password" class="form-control" name="c_new_pass" required>
              </div>

              <button type="submit" class="btn btn-primary w-100">Change Password</button>
            </form>
          </div>
        </main>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      $(document).ready(function () {
        $("#navLinks li:nth-child(5) a").addClass('active');
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

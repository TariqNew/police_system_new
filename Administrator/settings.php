<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role'])) {
  if ($_SESSION['role'] == 'Admin') {

    include "../DB_connection.php";
    include "data/setting.php";
    $setting = getSetting($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - System Settings</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="icon" href="../logo.png">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>

  <?php include "./inc/navbar.php"; ?>

  <div class="container-fluid">
    <div class="row" style="padding-top: 56px;">
      
      <!-- Sidebar -->
      <?php include "./inc/sidebar.php"; ?>

      <!-- Main content -->
      <div class="col-md-9 mt-3">
        <form method="post" action="../req/setting-edit.php" class="shadow p-4 bg-white rounded">
          <h4 class="mb-4"><i class="bi bi-gear-fill me-2"></i>Edit System Settings</h4>
          
          <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?= $_GET['error'] ?></div>
          <?php endif; ?>

          <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?= $_GET['success'] ?></div>
          <?php endif; ?>

          <div class="mb-3">
            <label class="form-label">System Name</label>
            <input type="text" class="form-control" name="system_name" value="<?= htmlspecialchars($setting['system_name']) ?>" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Tagline</label>
            <input type="text" class="form-control" name="tagline" value="<?= htmlspecialchars($setting['tagline']) ?>" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" rows="4" required><?= htmlspecialchars($setting['description']) ?></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">System Year</label>
            <input type="number" class="form-control" name="system_year" value="<?= htmlspecialchars($setting['system_year']) ?>" required>
          </div>

          <div class="mb-3">
            <label class="form-label">System Phase</label>
            <input type="text" class="form-control" name="system_phase" value="<?= htmlspecialchars($setting['system_phase']) ?>" required>
          </div>

          <button type="submit" class="btn btn-primary">Update Settings</button>
        </form>
      </div>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    $(document).ready(function () {
      $("#navLinks li:nth-child(10) a").addClass('active');
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

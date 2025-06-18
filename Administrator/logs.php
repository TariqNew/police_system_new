<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include '../DB_connection.php';

// Only admin allowed
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
  header('Location: ../dashboard.php');
  exit;
}

// Fetch logs
$stmt = $conn->query("SELECT * FROM system_logs ORDER BY timestamp DESC LIMIT 100");
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>CRMS - System Logs</title>
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
          <h2>System Logs</h2>
        </div>

        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead class="table-dark">
              <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Role</th>
                <th>Action</th>
                <th>Description</th>
                <th>IP</th>
                <th>Browser</th>
                <th>Time</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($logs as $log): ?>
              <tr>
                <td><?= htmlspecialchars($log['id']) ?></td>
                <td><?= htmlspecialchars($log['user_id']) ?></td>
                <td><?= htmlspecialchars($log['role']) ?></td>
                <td><?= htmlspecialchars($log['action']) ?></td>
                <td><?= htmlspecialchars($log['description']) ?></td>
                <td><?= htmlspecialchars($log['ip_address']) ?></td>
                <td><?= htmlspecialchars(substr($log['user_agent'], 0, 50)) ?>...</td>
                <td><?= htmlspecialchars($log['timestamp']) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </main>
    </div>
  </div>

</body>
</html>

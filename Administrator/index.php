<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (isset($_SESSION['admin_id']) && $_SESSION['role'] == 'Admin') {
  include "../DB_connection.php";

  // Totals
  $totalOfficers = $conn->query("SELECT COUNT(*) FROM officers")->fetchColumn();
  $totalSuspects = $conn->query("SELECT COUNT(*) FROM suspects")->fetchColumn();
  $totalInvestigators = $conn->query("SELECT COUNT(*) FROM investigators")->fetchColumn();
  $totalCases = $conn->query("SELECT COUNT(*) FROM cases")->fetchColumn();

  $totalOpenCases = $conn->query("SELECT COUNT(*) FROM cases WHERE status = 'open'")->fetchColumn();
  $totalInvestigatingCases = $conn->query("SELECT COUNT(*) FROM cases WHERE status = 'investigating'")->fetchColumn();
  $totalClosedCases = $conn->query("SELECT COUNT(*) FROM cases WHERE status = 'closed'")->fetchColumn();

  // Fetch system logs - limit to 10 for dashboard preview
  $stmt = $conn->query("SELECT * FROM system_logs ORDER BY timestamp DESC LIMIT 10");
  $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
  ?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8">
    <title>CRMS Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
      .criminal-record {
        background-color: #343a40;
      }

      .status-card {
        background-color: #6c757d;
        color: white;
      }

      .status-open {
        background-color:#052617 !important;
      }

      .status-investigating {
        background-color:#a37c05 !important;
      }

      .status-run {
        background-color:#080c36 !important;
      }

      /* Add margin below cards */
      .dashboard-logs {
        margin-top: 3rem;
      }
    </style>
  </head>

  <body>

    <?php include "./inc/navbar.php"; ?>

    <div class="container-fluid">
      <div class="row" style="padding-top: 56px;">
        <?php include "./inc/sidebar.php"; ?>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4 mt-3"
          style="height: calc(100vh - 56px); overflow-y: auto;">
          <h2 class="mb-4">Admin Dashboard</h2>

          <!-- Statistics Cards -->
          <div class="row g-3 mb-4">
            <div class="col-md-4">
              <div class="card criminal-record text-white text-bg-primary">
                <div class="card-body">
                  <h5 class="card-title">Total Officers</h5>
                  <h2 class="display-4 mb-0"><?= $totalOfficers ?></h2>
                  <a href="police_officer.php" class="text-white text-decoration-none">View Officers</a>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card criminal-record text-white text-bg-success">
                <div class="card-body">
                  <h5 class="card-title">Total Investigators</h5>
                  <h2 class="display-4 mb-0"><?= $totalInvestigators ?></h2>
                  <a href="investigator.php" class="text-white text-decoration-none">View Investigators</a>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card criminal-record text-white text-bg-warning">
                <div class="card-body">
                  <h5 class="card-title">Total Criminals</h5>
                  <h2 class="display-4 mb-0"><?= $totalSuspects ?></h2>
                  <a href="police_officer.php" class="text-white text-decoration-none">View Suspects</a>
                </div>
              </div>
            </div>
          </div>

          <!-- System Logs Section -->
          <div class="dashboard-logs">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h3>Recent System Logs</h3>
              <a href="system_logs.php" class="btn btn-primary btn-sm">View More</a>
            </div>

            <div class="table-responsive">
              <table class="table table-bordered table-striped align-middle">
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
                  <?php if (!empty($logs)): ?>
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
                  <?php else: ?>
                    <tr>
                      <td colspan="8" class="text-center">No logs found.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>

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
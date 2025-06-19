<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Officer') {
  header("Location: ../login.php");
  exit;
}

include "../DB_connection.php";
require_once "../fpdf/fpdf.php"; // Make sure path is correct

// Fetch all investigations
$sql = "SELECT i.investigation_id, i.case_title, i.description, i.status, i.date_started, i.date_closed,
               s.fname AS suspect_fname, s.lname AS suspect_lname,
               inv.fname AS investigator_fname, inv.lname AS investigator_lname
        FROM investigations i
        LEFT JOIN suspects s ON i.suspect_id = s.suspect_id
        LEFT JOIN investigators inv ON i.investigator_id = inv.investigator_id
        ORDER BY i.date_started DESC";

$stmt = $conn->prepare($sql);
$stmt->execute();
$investigations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// PDF Export
if (isset($_GET['export']) && $_GET['export'] === 'pdf') {
    $pdf = new FPDF();
    $pdf->AddPage('L');
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Investigations Report', 0, 1, 'C');
    $pdf->Ln(5);

    $pdf->SetFont('Arial', 'B', 10);
    $headers = ['ID', 'Case Title', 'Description', 'Status', 'Date Started', 'Date Closed', 'Suspect', 'Investigator'];
    foreach ($headers as $header) {
        $pdf->Cell(35, 10, $header, 1);
    }
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 9);
    foreach ($investigations as $inv) {
        $pdf->Cell(35, 10, $inv['investigation_id'], 1);
        $pdf->Cell(35, 10, substr($inv['case_title'], 0, 20), 1);
        $pdf->Cell(35, 10, substr($inv['description'], 0, 20), 1);
        $pdf->Cell(35, 10, $inv['status'], 1);
        $pdf->Cell(35, 10, $inv['date_started'], 1);
        $pdf->Cell(35, 10, $inv['date_closed'] ?? 'N/A', 1);
        $pdf->Cell(35, 10, $inv['suspect_fname'] . ' ' . $inv['suspect_lname'], 1);
        $pdf->Cell(35, 10, $inv['investigator_fname'] . ' ' . $inv['investigator_lname'], 1);
        $pdf->Ln();
    }

    $pdf->Output('D', 'investigations_report.pdf');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Investigator - Investigations Report</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="icon" href="../logo.png">
</head>

<body style="background-color: #f8f9fa;">
  <?php include 'inc/navbar.php'; ?>

  <div class="container-fluid">
    <div class="row" style="padding-top: 56px;">
      <?php include 'inc/sidebar.php'; ?>

      <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h2>Investigations Report</h2>
          <a href="?export=pdf" class="btn btn-danger"><i class="bi bi-file-earmark-pdf"></i> Export PDF</a>
        </div>

        <div class="table-responsive">
          <table class="table table-striped table-hover table-bordered align-middle">
            <thead class="table-dark">
              <tr>
                <th>ID</th>
                <th>Case Title</th>
                <th>Description</th>
                <th>Status</th>
                <th>Date Started</th>
                <th>Date Closed</th>
                <th>Suspect</th>
                <th>Investigator</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($investigations as $inv): ?>
                <tr>
                  <td><?= htmlspecialchars($inv['investigation_id']) ?></td>
                  <td><?= htmlspecialchars($inv['case_title']) ?></td>
                  <td><?= htmlspecialchars($inv['description']) ?></td>
                  <td>
                    <span class="badge bg-<?= match ($inv['status']) {
                      'Pending' => 'secondary',
                      'In Progress' => 'info',
                      'Completed' => 'success',
                      'Closed' => 'dark',
                      default => 'light'
                    } ?>">
                      <?= htmlspecialchars($inv['status']) ?>
                    </span>
                  </td>
                  <td><?= htmlspecialchars($inv['date_started']) ?></td>
                  <td><?= htmlspecialchars($inv['date_closed'] ?? 'N/A') ?></td>
                  <td><?= htmlspecialchars($inv['suspect_fname'] . ' ' . $inv['suspect_lname']) ?></td>
                  <td><?= htmlspecialchars($inv['investigator_fname'] . ' ' . $inv['investigator_lname']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

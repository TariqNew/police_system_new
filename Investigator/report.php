<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Investigator') {
  header("Location: ../login.php");
  exit;
}

include "../DB_connection.php";
require_once "../fpdf/fpdf.php"; // Include FPDF here

// Fetch all investigations with joined data
$sql = "SELECT 
    i.investigation_id, 
    cc.case_title, 
    cc.case_description AS description,
    i.status, 
    i.date_started, 
    i.date_closed,
    s.fname AS suspect_fname, 
    s.lname AS suspect_lname,
    inv.fname AS investigator_fname, 
    inv.lname AS investigator_lname
FROM investigations i
LEFT JOIN criminal_cases cc ON i.case_id = cc.case_id
LEFT JOIN suspects s ON cc.suspect_id = s.suspect_id
LEFT JOIN investigators inv ON i.investigator_id = inv.investigator_id
ORDER BY i.date_started DESC";

$stmt = $conn->prepare($sql);
$stmt->execute();
$investigations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle PDF export
if (isset($_GET['export']) && $_GET['export'] === 'pdf') {
  $pdf = new FPDF('L', 'mm', 'A4'); // Landscape, mm, A4
  $pdf->AddPage();
  $pdf->SetFont('Arial', 'B', 14);
  $pdf->Cell(0, 10, 'Investigations Report', 0, 1, 'C');
  $pdf->Ln(5);

  // Table header
  $pdf->SetFont('Arial', 'B', 10);
  $headerWidths = [15, 40, 60, 25, 25, 25, 40, 40];
  $headers = ['ID', 'Case Title', 'Description', 'Status', 'Date Started', 'Date Closed', 'Suspect', 'Investigator'];
  foreach ($headers as $key => $header) {
    $pdf->Cell($headerWidths[$key], 10, $header, 1);
  }
  $pdf->Ln();

  // Table body
  $pdf->SetFont('Arial', '', 9);
  foreach ($investigations as $inv) {
    $pdf->Cell($headerWidths[0], 10, $inv['investigation_id'], 1);
    $pdf->Cell($headerWidths[1], 10, substr($inv['case_title'] ?? 'N/A', 0, 25), 1);
    $pdf->Cell($headerWidths[2], 10, substr($inv['description'] ?? '', 0, 40), 1);
    $pdf->Cell($headerWidths[3], 10, $inv['status'], 1);
    $pdf->Cell($headerWidths[4], 10, $inv['date_started'], 1);
    $pdf->Cell($headerWidths[5], 10, $inv['date_closed'] ?? 'N/A', 1);
    $pdf->Cell($headerWidths[6], 10, trim(($inv['suspect_fname'] ?? '') . ' ' . ($inv['suspect_lname'] ?? '')), 1);
    $pdf->Cell($headerWidths[7], 10, trim(($inv['investigator_fname'] ?? '') . ' ' . ($inv['investigator_lname'] ?? '')), 1);
    $pdf->Ln();
  }

  $pdf->Output('D', 'investigations_report.pdf');
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Investigator - Investigations Report</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="icon" href="../logo.png" />
</head>

<body style="background-color: #f8f9fa;">
  <?php include 'inc/navbar.php'; ?>

  <div class="container-fluid">
    <div class="row" style="padding-top: 56px;">
      <?php include 'inc/sidebar.php'; ?>

      <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h2>Investigations Report</h2>
          <a href="?export=pdf" class="btn btn-danger">
            <i class="bi bi-file-earmark-pdf"></i> Export PDF
          </a>
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
              <?php if (!empty($investigations)): ?>
                <?php foreach ($investigations as $inv): ?>
                  <tr>
                    <td><?= htmlspecialchars($inv['investigation_id']) ?></td>
                    <td><?= htmlspecialchars($inv['case_title'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($inv['description'] ?? '') ?></td>
                    <td>
                      <span class="badge bg-<?= match ($inv['status'] ?? '') {
                        'Pending' => 'secondary',
                        'In Progress' => 'info',
                        'Completed' => 'success',
                        'Closed' => 'dark',
                        default => 'light'
                      } ?>">
                        <?= htmlspecialchars($inv['status'] ?? '') ?>
                      </span>
                    </td>
                    <td><?= htmlspecialchars($inv['date_started'] ?? '') ?></td>
                    <td><?= htmlspecialchars($inv['date_closed'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars(trim(($inv['suspect_fname'] ?? '') . ' ' . ($inv['suspect_lname'] ?? ''))) ?></td>
                    <td><?= htmlspecialchars(trim(($inv['investigator_fname'] ?? '') . ' ' . ($inv['investigator_lname'] ?? ''))) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="8" class="text-center">No investigations found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </main>
    </div>
  </div>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

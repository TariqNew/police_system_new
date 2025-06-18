<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include '../DB_connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../dashboard.php');
    exit;
}

$start_date = $_GET['start_date'] ?? null;
$end_date = $_GET['end_date'] ?? null;

function dateCondition($field, $start, $end) {
    if ($start && $end) {
        return " AND $field BETWEEN :start_date AND :end_date ";
    }
    return "";
}

// Fetch summary stats
$total_suspects = $conn->query("SELECT COUNT(*) FROM suspects")->fetchColumn();
$total_cases = $conn->query("SELECT COUNT(*) FROM criminal_cases")->fetchColumn();

$cases_period_sql = "SELECT COUNT(*) FROM criminal_cases WHERE 1=1 ";
$cases_period_sql .= dateCondition("date_reported", $start_date, $end_date);
$cases_period_stmt = $conn->prepare($cases_period_sql);
if ($start_date && $end_date) {
    $cases_period_stmt->bindParam(':start_date', $start_date);
    $cases_period_stmt->bindParam(':end_date', $end_date);
}
$cases_period_stmt->execute();
$cases_in_period = $cases_period_stmt->fetchColumn();

$total_investigations = $conn->query("SELECT COUNT(*) FROM investigations")->fetchColumn();

$crime_trend = "N/A";
if ($start_date && $end_date) {
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $interval = $start->diff($end);
    $prev_end = (clone $start)->modify('-1 day');
    $prev_start = (clone $prev_end)->sub($interval);

    $prev_cases_sql = "SELECT COUNT(*) FROM criminal_cases WHERE date_reported BETWEEN :prev_start AND :prev_end";
    $prev_cases_stmt = $conn->prepare($prev_cases_sql);
    $prev_start_str = $prev_start->format('Y-m-d');
    $prev_end_str = $prev_end->format('Y-m-d');
    $prev_cases_stmt->bindParam(':prev_start', $prev_start_str);
    $prev_cases_stmt->bindParam(':prev_end', $prev_end_str);
    $prev_cases_stmt->execute();
    $prev_cases_count = $prev_cases_stmt->fetchColumn();

    if ($prev_cases_count == 0 && $cases_in_period == 0) {
        $crime_trend = "No cases in both periods";
    } elseif ($prev_cases_count == 0) {
        $crime_trend = "Increase (no cases previously)";
    } else {
        $diff = $cases_in_period - $prev_cases_count;
        $percent_change = ($diff / $prev_cases_count) * 100;
        if ($percent_change > 0) {
            $crime_trend = "Increase by " . round($percent_change, 2) . "%";
        } elseif ($percent_change < 0) {
            $crime_trend = "Decrease by " . abs(round($percent_change, 2)) . "%";
        } else {
            $crime_trend = "No change";
        }
    }
}

// Fetch detailed lists for the table and export

// Criminal Cases with suspect names
$cases_sql = "SELECT cc.case_id, cc.case_title, cc.case_description, cc.date_reported, 
              s.fname AS suspect_fname, s.lname AS suspect_lname 
              FROM criminal_cases cc
              LEFT JOIN suspects s ON cc.suspect_id = s.suspect_id
              WHERE 1=1 ";
$cases_sql .= dateCondition("cc.date_reported", $start_date, $end_date);
$cases_stmt = $conn->prepare($cases_sql);
if ($start_date && $end_date) {
    $cases_stmt->bindParam(':start_date', $start_date);
    $cases_stmt->bindParam(':end_date', $end_date);
}
$cases_stmt->execute();
$cases_list = $cases_stmt->fetchAll(PDO::FETCH_ASSOC);

// Investigations list
$investigations_sql = "SELECT investigation_id, case_title, description, status, date_started, date_closed FROM investigations WHERE 1=1 ";
$investigations_sql .= dateCondition("date_started", $start_date, $end_date);
$investigations_stmt = $conn->prepare($investigations_sql);
if ($start_date && $end_date) {
    $investigations_stmt->bindParam(':start_date', $start_date);
    $investigations_stmt->bindParam(':end_date', $end_date);
}
$investigations_stmt->execute();
$investigations_list = $investigations_stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle CSV export requests
if (isset($_GET['export'])) {
    $exportType = $_GET['export'];

    // Common function to output CSV
    function outputCSV($filename, $header, $rows) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $output = fopen('php://output', 'w');
        fputcsv($output, $header);
        foreach ($rows as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit;
    }

    if ($exportType === 'summary') {
        $header = ['Metric', 'Value'];
        $rows = [
            ['Total Criminals', $total_suspects],
            ['Total Cases', $total_cases],
            ['Cases in Period', $cases_in_period],
            ['Total Investigations', $total_investigations],
            ['Crime Trend', $crime_trend],
        ];
        outputCSV('crms_summary_report.csv', $header, $rows);
    } elseif ($exportType === 'cases') {
        $header = ['Case ID', 'Case Title', 'Description', 'Date Reported', 'Suspect First Name', 'Suspect Last Name'];
        $rows = [];
        foreach ($cases_list as $case) {
            $rows[] = [
                $case['case_id'],
                $case['case_title'],
                $case['case_description'],
                $case['date_reported'],
                $case['suspect_fname'],
                $case['suspect_lname'],
            ];
        }
        outputCSV('crms_cases_report.csv', $header, $rows);
    } elseif ($exportType === 'investigations') {
        $header = ['Investigation ID', 'Case Title', 'Description', 'Status', 'Date Started', 'Date Closed'];
        $rows = [];
        foreach ($investigations_list as $inv) {
            $rows[] = [
                $inv['investigation_id'],
                $inv['case_title'],
                $inv['description'],
                $inv['status'],
                $inv['date_started'],
                $inv['date_closed'],
            ];
        }
        outputCSV('crms_investigations_report.csv', $header, $rows);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>CRMS - System Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="icon" href="../ifm-logo.jpg" />
</head>

<body>
    <?php include "./inc/navbar.php"; ?>

    <div class="container-fluid">
        <div class="row" style="padding-top: 56px;">
            <?php include "./inc/sidebar.php"; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4 mt-3">
                <h2>System-wide Reports</h2>

                <form method="get" class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" id="start_date" name="start_date" class="form-control"
                            value="<?= htmlspecialchars($start_date) ?>" />
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" id="end_date" name="end_date" class="form-control"
                            value="<?= htmlspecialchars($end_date) ?>" />
                    </div>
                    <div class="col-md-4 align-self-end">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="report.php" class="btn btn-secondary">Reset</a>
                    </div>
                </form>

                <div class="row gy-4 mb-3">

                    <div class="col-md-3">
                        <div class="card text-bg-primary">
                            <div class="card-body">
                                <h5 class="card-title">Total Criminals</h5>
                                <p class="card-text display-6"><?= number_format($total_suspects) ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card text-bg-success">
                            <div class="card-body">
                                <h5 class="card-title">Total Cases</h5>
                                <p class="card-text display-6"><?= number_format($total_cases) ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card text-bg-warning">
                            <div class="card-body">
                                <h5 class="card-title">Cases in Period</h5>
                                <p class="card-text display-6"><?= number_format($cases_in_period) ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card text-bg-info">
                            <div class="card-body">
                                <h5 class="card-title">Total Investigations</h5>
                                <p class="card-text display-6"><?= number_format($total_investigations) ?></p>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="mb-4">
                    <h4>Crime Trend (Compared to previous period)</h4>
                    <p><?= $crime_trend ?></p>
                </div>

                <hr />

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3>Criminal Cases</h3>
                    <a href="?export=cases<?= $start_date && $end_date ? "&start_date=$start_date&end_date=$end_date" : "" ?>" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-download"></i> Export Cases CSV
                    </a>
                </div>
                <?php if (count($cases_list) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Case ID</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Date Reported</th>
                                <th>Suspect First Name</th>
                                <th>Suspect Last Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cases_list as $case): ?>
                            <tr>
                                <td><?= htmlspecialchars($case['case_id']) ?></td>
                                <td><?= htmlspecialchars($case['case_title']) ?></td>
                                <td><?= htmlspecialchars($case['case_description']) ?></td>
                                <td><?= htmlspecialchars($case['date_reported']) ?></td>
                                <td><?= htmlspecialchars($case['suspect_fname']) ?></td>
                                <td><?= htmlspecialchars($case['suspect_lname']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p>No criminal cases found for selected period.</p>
                <?php endif; ?>

                <hr />

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3>Investigations</h3>
                    <a href="?export=investigations<?= $start_date && $end_date ? "&start_date=$start_date&end_date=$end_date" : "" ?>" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-download"></i> Export Investigations CSV
                    </a>
                </div>
                <?php if (count($investigations_list) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Case Title</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Date Started</th>
                                <th>Date Closed</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($investigations_list as $inv): ?>
                            <tr>
                                <td><?= htmlspecialchars($inv['investigation_id']) ?></td>
                                <td><?= htmlspecialchars($inv['case_title']) ?></td>
                                <td><?= htmlspecialchars($inv['description']) ?></td>
                                <td><?= htmlspecialchars($inv['status']) ?></td>
                                <td><?= htmlspecialchars($inv['date_started']) ?></td>
                                <td><?= htmlspecialchars($inv['date_closed'] ?? '') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p>No investigations found for selected period.</p>
                <?php endif; ?>

                <hr />

                <div class="mb-4">
                    <h4>Export Summary Data</h4>
                    <a href="?export=summary<?= $start_date && $end_date ? "&start_date=$start_date&end_date=$end_date" : "" ?>" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-download"></i> Export Summary CSV
                    </a>
                </div>

            </main>
        </div>
    </div>
</body>

</html>

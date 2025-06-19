<?php
include "../DB_connection.php";

// Fetch investigations count by month and case title (last 6 months)
$caseTypesOverTime = $conn->query("
  SELECT 
    DATE_FORMAT(date_started, '%Y-%m') AS month,
    case_title,
    COUNT(*) AS total
  FROM investigations
  WHERE date_started >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
  GROUP BY month, case_title
  ORDER BY month
")->fetchAll(PDO::FETCH_ASSOC);

// Organize data
$groupedData = [];
$months = [];
$titles = [];

foreach ($caseTypesOverTime as $row) {
    $month = $row['month'];
    $title = $row['case_title'];
    $total = (int) $row['total'];

    $groupedData[$title][$month] = $total;
    $months[$month] = true;
    $titles[$title] = true;
}

$months = array_keys($months);
sort($months);
$titles = array_keys($titles);

// Prepare datasets for Chart.js
$finalData = [];
foreach ($titles as $title) {
    $data = [];
    foreach ($months as $month) {
        $data[] = $groupedData[$title][$month] ?? 0;
    }
    $finalData[] = [
        'label' => $title,
        'data' => $data,
        'backgroundColor' => '#' . substr(md5($title), 0, 6),
    ];
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Case Trends - Criminal Types</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" href="../logo.png">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <?php include "inc/navbar.php"; ?>
    <div class="container-fluid">
        <div class="row" style="padding-top: 56px;"></div>
        <div class="row">
            <?php include "inc/sidebar.php"; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <h2 class="mb-4 text-center">Criminal Case Trends by Type</h2>

                <div class="card shadow-sm">
                    <div class="card-header text-center">
                        <h5 class="mb-0">Case Trends (Last 6 Months)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="caseTypeTrendChart" height="100"></canvas>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        const trendCtx = document.getElementById('caseTypeTrendChart').getContext('2d');
        const trendChart = new Chart(trendCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($months) ?>,
                datasets: <?= json_encode($finalData) ?>
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            precision: 0,  // whole numbers only
                            callback: function (value) {
                                return Number.isInteger(value) ? value : null;  // show only integers on y axis
                            }
                        },
                        title: {
                            display: true,
                            text: 'Number of Cases'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Month'
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    },
                    legend: {
                        position: 'top'
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
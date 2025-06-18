<?php
include "../DB_connection.php";

// Fetch convictions count by status
$convictionData = $conn->query("
  SELECT status, COUNT(*) as total 
  FROM convictions 
  GROUP BY status
")->fetchAll(PDO::FETCH_ASSOC);

// Fetch cases count by month (last 12 months)
$casesData = $conn->query("
  SELECT DATE_FORMAT(date_reported, '%Y-%m') as month, COUNT(*) as total 
  FROM criminal_cases
  WHERE date_reported >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
  GROUP BY month
  ORDER BY month
")->fetchAll(PDO::FETCH_ASSOC);

function array_column_values($array, $key)
{
    return array_map(fn($v) => $v[$key], $array);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Statistics - Criminal Cases</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
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
                <h2 class="mb-4 text-center">Statistics Dashboard</h2>

                <div class="row justify-content-center gy-4">
                    <div class="col-md-6 col-lg-5">
                        <div class="card shadow-sm">
                            <div class="card-header text-center">
                                <h5 class="mb-0">Convictions by Status</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="convictionChart" class="mx-auto d-block"
                                    style="max-width:100%; height: 380px;"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-5">
                        <div class="card shadow-sm">
                            <div class="card-header text-center">
                                <h5 class="mb-0">Cases Reported Per Month (Last 12 Months)</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="casesChart" class="mx-auto d-block"
                                    style="max-width:100%; height: 380px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

        </div>
    </div>

    <script>
        const convictionCtx = document.getElementById('convictionChart').getContext('2d');
        const convictionChart = new Chart(convictionCtx, {
            type: 'pie',
            data: {
                labels: <?= json_encode(array_column_values($convictionData, 'status')) ?>,
                datasets: [{
                    data: <?= json_encode(array_column_values($convictionData, 'total')) ?>,
                    backgroundColor: ['#dc3545', '#28a745', '#ffc107', '#007bff', '#6c757d']
                }]
            }
        });

        const casesCtx = document.getElementById('casesChart').getContext('2d');
        const casesChart = new Chart(casesCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column_values($casesData, 'month')) ?>,
                datasets: [{
                    label: 'Cases',
                    data: <?= json_encode(array_column_values($casesData, 'total')) ?>,
                    backgroundColor: '#007bff'
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        stepSize: 1,
                        suggestedMax: 5  // or another value to increase max Y
                    }
                }
            }

        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
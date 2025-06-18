<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (isset($_SESSION['officer_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Officer') {

    include "../DB_connection.php";
    include "data/police_officer.php";

    $officer_id = $_SESSION['officer_id'];
    $officer = getOfficerById($officer_id, $conn);
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Officer - Home</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <style>
            html, body {
                height: 100%;
                margin: 0;
                overflow: hidden;
            }
            .main-scrollable {
                height: calc(100vh - 56px); /* Adjust according to your navbar height */
                overflow-y: auto;
                padding-bottom: 20px;
            }
        </style>
    </head>

    <body>
        <!-- Navbar -->
        <?php include "./inc/navbar.php"; ?>

        <div class="container-fluid">
            <div class="row" style="padding-top: 56px;">
                <!-- Sidebar -->
                <?php include "./inc/sidebar.php"; ?>

                <!-- Main content -->
                <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4 main-scrollable">
                    <?php if ($officer != 0): ?>
                        <div class="container shadow-sm p-4 rounded bg-white">
                            <div class="row mb-4">
                                <div class="col-md-3 text-center">
                                    <img src="../img/officer-<?= htmlspecialchars($officer['gender']) ?>.png"
                                         alt="Profile Image"
                                         class="img-thumbnail rounded-circle"
                                         style="width: 150px; height: 150px; object-fit: cover;">
                                </div>
                                <div class="col-md-9 d-flex flex-column justify-content-center">
                                    <h3>@<?= htmlspecialchars($officer['username']) ?></h3>
                                    <p class="text-muted"><?= htmlspecialchars($officer['rank'] ?? 'Officer') ?></p>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">First Name:</label>
                                    <div><?= htmlspecialchars($officer['fname']) ?></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Last Name:</label>
                                    <div><?= htmlspecialchars($officer['lname']) ?></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Username:</label>
                                    <div><?= htmlspecialchars($officer['username']) ?></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Employee Number:</label>
                                    <div><?= htmlspecialchars($officer['employee_number']) ?></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Gender:</label>
                                    <div><?= htmlspecialchars($officer['gender']) ?></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Date of Birth:</label>
                                    <div><?= htmlspecialchars($officer['date_of_birth']) ?></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Phone Number:</label>
                                    <div><?= htmlspecialchars($officer['phone_number']) ?></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Email Address:</label>
                                    <div><?= htmlspecialchars($officer['email_address']) ?></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Address:</label>
                                    <div><?= htmlspecialchars($officer['address']) ?></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Date Joined:</label>
                                    <div><?= htmlspecialchars($officer['date_of_joined']) ?></div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-danger">Error loading officer profile.</div>
                    <?php endif; ?>
                </main>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            $(document).ready(function () {
                $("#navLinks li:nth-child(1) a").addClass('active');
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

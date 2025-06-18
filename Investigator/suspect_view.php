<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (isset($_SESSION['officer_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Officer') {
        include "../DB_connection.php";
        include "data/suspect.php";

        if (isset($_GET['suspect_id'])) {
            $suspect_id = $_GET['suspect_id'];
            $suspect = getSuspectWithInvestigatorById($suspect_id, $conn);

            if ($suspect != 0) {
                ?>
                <!DOCTYPE html>
                <html lang="en">

                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Suspect - Profile</title>
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
                    <link rel="stylesheet" href="../css/style.css">
                    <link rel="icon" href="../logo.png">
                    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
                </head>

                <body>

                    <?php include "inc/navbar.php"; ?>

                    <div class="container mt-5">
                        <div class="card" style="width: 22rem;">
                            <img src="../img/student-<?= $suspect['gender'] ?>.png" class="card-img-top" alt="...">
                            <div class="card-body">
                                <h5 class="card-title text-center">@<?= $suspect['username'] ?></h5>
                            </div>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">First Name: <?= $suspect['fname'] ?></li>
                                <li class="list-group-item">Last Name: <?= $suspect['lname'] ?></li>
                                <li class="list-group-item">Username: <?= $suspect['username'] ?></li>
                                <li class="list-group-item">Address: <?= $suspect['address'] ?></li>
                                <li class="list-group-item">Date of Birth: <?= $suspect['date_of_birth'] ?></li>
                                <li class="list-group-item">Email Address: <?= $suspect['email_address'] ?></li>
                                <li class="list-group-item">Gender: <?= $suspect['gender'] ?></li>
                                <li class="list-group-item">Date Joined: <?= $suspect['date_of_joined'] ?></li>

                                <li class="list-group-item">Parent First Name: <?= $suspect['parent_fname'] ?></li>
                                <li class="list-group-item">Parent Last Name: <?= $suspect['parent_lname'] ?></li>
                                <li class="list-group-item">Parent Phone Number: <?= $suspect['parent_phone_number'] ?></li>

                                <li class="list-group-item">Investigator:
                                    <?php if (!empty($suspect['investigator_id'])): ?>
                                        <a href="officer_view.php?officer_id=<?= $suspect['investigator_id'] ?>">
                                            <?= $suspect['investigator_fname'] . ' ' . $suspect['investigator_lname'] ?>
                                        </a>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </li>
                            </ul>
                            <div class="card-body text-center">
                                <a href="suspect.php" class="btn btn-dark">Go Back</a>
                            </div>
                        </div>
                    </div>

                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
                    <script>
                        $(document).ready(function () {
                            $("#navLinks li:nth-child(3) a").addClass('active');
                        });
                    </script>

                </body>

                </html>
            <?php
            } else {
                header("Location: suspect.php");
                exit;
            }
        } else {
            header("Location: suspect.php");
            exit;
        }
    } else {
        header("Location: ../login.php");
        exit;
    }
} else {
    header("Location: ../login.php");
    exit;
}
?>
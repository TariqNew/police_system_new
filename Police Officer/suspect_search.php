<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (isset($_SESSION['officer_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Officer') {
    if (isset($_GET['searchKey'])) {

        $search_key = $_GET['searchKey'];
        include "../DB_connection.php";
        include "data/suspect.php";

        $suspects = searchSuspects($search_key, $conn);
        ?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <title>Officer - Search Suspects</title>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
            <link rel="icon" href="../logo.png">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

            <style>
                html, body {
                    height: 100%;
                    margin: 0;
                    overflow: hidden;
                }

                .scrollable-main {
                    height: calc(100vh - 56px);
                    overflow-y: auto;
                    padding-bottom: 20px;
                }
            </style>
        </head>

        <body>

            <?php include "inc/navbar.php"; ?>

            <div class="container-fluid">
                <div class="row" style="padding-top: 56px;">
                    <?php include "inc/sidebar.php"; ?>

                    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4 mt-3 scrollable-main">
                        <h2 class="mb-4">Search Suspects</h2>

                        <a href="suspect.php" class="btn btn-dark mb-3">← Go Back</a>

                        <form action="suspect_search.php" method="get" class="mb-3">
                            <div class="input-group">
                                <input type="text" class="form-control" name="searchKey" placeholder="Search...">
                                <button class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i></button>
                            </div>
                        </form>

                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger"><?= $_GET['error'] ?></div>
                        <?php endif; ?>
                        <?php if (isset($_GET['success'])): ?>
                            <div class="alert alert-success"><?= $_GET['success'] ?></div>
                        <?php endif; ?>

                        <?php if ($suspects != 0): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>ID</th>
                                            <th>First Name</th>
                                            <th>Last Name</th>
                                            <th>Username</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 0;
                                        foreach ($suspects as $suspect):
                                            $i++; ?>
                                            <tr>
                                                <td><?= $i ?></td>
                                                <td><?= $suspect['suspect_id'] ?></td>
                                                <td>
                                                    <a href="suspect_view.php?suspect_id=<?= $suspect['suspect_id'] ?>">
                                                        <?= $suspect['fname'] ?>
                                                    </a>
                                                </td>
                                                <td><?= $suspect['lname'] ?></td>
                                                <td><?= $suspect['username'] ?></td>
                                                <td>
                                                    <a href="suspect_edit.php?suspect_id=<?= $suspect['suspect_id'] ?>"
                                                        class="btn btn-warning btn-sm">Edit</a>
                                                    <a href="suspect_delete.php?suspect_id=<?= $suspect['suspect_id'] ?>"
                                                        class="btn btn-danger btn-sm">Delete</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">No Results Found</div>
                            <a href="suspect.php" class="btn btn-dark mt-2">Go Back</a>
                        <?php endif; ?>
                    </main>
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
    header("Location: ../login.php");
    exit;
}
?>

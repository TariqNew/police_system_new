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

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['case_title'];
    $description = $_POST['description'];
    $criminal_id = $_POST['criminal_id'];
    $status = $_POST['status'] ?? 'Pending';
    $investigator_id = $_SESSION['investigator_id'];

    if (!empty($title) && !empty($description) && !empty($criminal_id)) {
        $stmt = $conn->prepare("INSERT INTO investigations 
        (case_title, description, suspect_id, investigator_id, status, date_started) 
        VALUES (?, ?, ?, ?, ?, NOW())");

        if ($stmt->execute([$title, $description, $criminal_id, $investigator_id, $status])) {
            $success = "Investigation added successfully!";
        } else {
            $error = "Failed to add investigation.";
        }
    } else {
        $error = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Investigation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

    <!-- Optional custom CSS -->
    <link rel="icon" href="../logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body style="background-color: #f8f9fa;">

    <?php include "inc/navbar.php"; ?>

    <div class="container-fluid">
        <div class="row" style="padding-top: 56px; min-height: 100vh;">

            <?php include "inc/sidebar.php"; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <h2 class="mb-4">Add New Investigation</h2>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php elseif (!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <div class="card shadow-sm">
                    <div class="card-body bg-white">
                        <form method="POST" class="needs-validation" novalidate>

                            <!-- Case Title -->
                            <div class="mb-3">
                                <label for="case_title" class="form-label">Case Title</label>
                                <input type="text" class="form-control" id="case_title" name="case_title" required>
                                <div class="invalid-feedback">Please enter a case title.</div>
                            </div>

                            <!-- Description -->
                            <div class="mb-3">
                                <label for="description" class="form-label">Case Details</label>
                                <textarea class="form-control" id="description" name="description" rows="4"
                                    required></textarea>
                                <div class="invalid-feedback">Please provide case details.</div>
                            </div>

                            <!-- Criminal Selection -->
                            <div class="mb-3">
                                <label for="criminal_id" class="form-label">Select Criminal</label>
                                <select class="form-select" name="criminal_id" id="criminal_id" required>
                                    <option value="" disabled selected>-- Select --</option>
                                    <?php
                                    try {
                                        $stmt = $conn->query("SELECT * FROM suspects");
                                        foreach ($stmt as $c) {
                                            $id = htmlspecialchars($c['suspect_id']);
                                            $name = htmlspecialchars($c['username']);
                                            echo "<option value='$id'>$name</option>";
                                        }
                                    } catch (Exception $e) {
                                        echo "<option disabled>Error loading criminals</option>";
                                    }
                                    ?>
                                </select>
                                <div class="invalid-feedback">Please select a criminal.</div>
                            </div>

                            <!-- Status -->
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" name="status" id="status" required>
                                    <option value="Pending" selected>Pending</option>
                                    <option value="In Progress">In Progress</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Closed">Closed</option>
                                </select>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary w-100" style="font-weight: 600;">
                                Add Investigation
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Bootstrap form validation script -->
                <script>
                    (function () {
                        'use strict'
                        var forms = document.querySelectorAll('.needs-validation')
                        Array.prototype.slice.call(forms).forEach(function (form) {
                            form.addEventListener('submit', function (event) {
                                if (!form.checkValidity()) {
                                    event.preventDefault()
                                    event.stopPropagation()
                                }
                                form.classList.add('was-validated')
                            }, false)
                        })
                    })()
                </script>
            </main>

        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
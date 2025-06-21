<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['investigator_id']) || $_SESSION['role'] !== 'Investigator') {
    header("Location: ../login.php");
    exit;
}

include "../DB_connection.php";

// Initialize form variables (for repopulating after error)
$fname = $_GET['fname'] ?? '';
$lname = $_GET['lname'] ?? '';
$address = $_GET['address'] ?? '';
$email = $_GET['email'] ?? '';
$uname = $_GET['uname'] ?? '';
$pfn = $_GET['pfn'] ?? '';
$pln = $_GET['pln'] ?? '';
$ppn = $_GET['ppn'] ?? '';
$case_title = $_GET['case_title'] ?? '';
$case_description = $_GET['case_description'] ?? '';
$case_date = $_GET['case_date'] ?? '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect POST data
    $fname = trim($_POST['fname'] ?? '');
    $lname = trim($_POST['lname'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $email_address = trim($_POST['email_address'] ?? '');
    $date_of_birth = $_POST['date_of_birth'] ?? '';
    $gender = $_POST['gender'] ?? 'Male';
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['pass'] ?? '');
    $parent_fname = trim($_POST['parent_fname'] ?? '');
    $parent_lname = trim($_POST['parent_lname'] ?? '');
    $parent_phone_number = trim($_POST['parent_phone_number'] ?? '');
    $case_title = trim($_POST['case_title'] ?? '');
    $case_description = trim($_POST['case_description'] ?? '');
    $case_date = $_POST['case_date'] ?? null;

    // Validate required fields
    if (!$fname || !$lname || !$username || !$password || !$date_of_birth) {
        $error = "Please fill all required fields";
    } else {
        // Check username uniqueness
        $check = $conn->prepare("SELECT suspect_id FROM suspects WHERE username = ?");
        $check->execute([$username]);
        if ($check->rowCount() > 0) {
            $error = "Username already exists";
        } else {
            // Hash password
            $pass_hashed = password_hash($password, PASSWORD_DEFAULT);

            try {
                $conn->beginTransaction();

                // Insert suspect
                $stmt = $conn->prepare("INSERT INTO suspects (username, password, fname, lname, grade, section, address, gender, email_address, date_of_birth, parent_fname, parent_lname, parent_phone_number) VALUES (?, ?, ?, ?, 0, 0, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$username, $pass_hashed, $fname, $lname, $address, $gender, $email_address, $date_of_birth, $parent_fname, $parent_lname, $parent_phone_number]);

                $suspect_id = $conn->lastInsertId();

                // Insert case if provided
                if ($case_title) {
                    $stmtCase = $conn->prepare("INSERT INTO criminal_cases (suspect_id, case_title, case_description, date_reported) VALUES (?, ?, ?, ?)");
                    $date_reported = $case_date ?: date('Y-m-d');
                    $stmtCase->execute([$suspect_id, $case_title, $case_description, $date_reported]);
                }

                $conn->commit();

                header("Location: ../suspect.php?success=Suspect and case added successfully");
                exit;
            } catch (Exception $e) {
                $conn->rollBack();
                $error = "Failed to add suspect or case: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= htmlspecialchars($_SESSION['role']) ?> - Add Suspect & Case</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" href="../logo.png" />
    <style>
        body {
            overflow-x: hidden;
        }

        .sidebar {
            position: fixed;
            top: 56px;
            /* height of navbar */
            bottom: 0;
            left: 0;
            width: 250px;
            overflow-y: auto;
            background-color: #f8f9fa;
            border-right: 1px solid #dee2e6;
            padding-top: 1rem;
        }

        main {
            margin-left: 250px;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: static;
                width: 100%;
            }

            main {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>

    <?php include "./inc/navbar.php"; ?>

    <div class="container-fluid">
        <div class="row vh-100">
            <div class="col-md-3 col-lg-2 sidebar">
                <?php include "./inc/sidebar.php"; ?>
            </div>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4 mt-3">
                <a href="suspect.php" class="btn btn-dark mb-4">Go Back</a>

                <form method="post" class="shadow p-4 bg-light" action="">
                    <h3>Add New Suspect</h3>
                    <hr>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">First Name</label>
                        <input type="text" class="form-control" name="fname" value="<?= htmlspecialchars($fname) ?>" required />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-control" name="lname" value="<?= htmlspecialchars($lname) ?>" required />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <input type="text" class="form-control" name="address" value="<?= htmlspecialchars($address) ?>" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" name="email_address" value="<?= htmlspecialchars($email) ?>" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" class="form-control" name="date_of_birth" value="<?= htmlspecialchars($date_of_birth ?? '') ?>" required />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Gender</label><br />
                        <input type="radio" name="gender" value="Male" <?= ($gender ?? '') === 'Male' ? 'checked' : '' ?> /> Male
                        &nbsp;&nbsp;&nbsp;
                        <input type="radio" name="gender" value="Female" <?= ($gender ?? '') === 'Female' ? 'checked' : '' ?> /> Female
                    </div>

                    <hr />

                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($uname) ?>" required />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="pass" required />
                    </div>

                    <hr />

                    <div class="mb-3">
                        <label class="form-label">Parent First Name</label>
                        <input type="text" class="form-control" name="parent_fname" value="<?= htmlspecialchars($pfn) ?>" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Parent Last Name</label>
                        <input type="text" class="form-control" name="parent_lname" value="<?= htmlspecialchars($pln) ?>" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Parent Phone Number</label>
                        <input type="text" class="form-control" name="parent_phone_number" value="<?= htmlspecialchars($ppn) ?>" />
                    </div>

                    <hr />
                    <h4>Related Case Details (optional)</h4>

                    <div class="mb-3">
                        <label class="form-label">Case Title</label>
                        <input type="text" class="form-control" name="case_title" value="<?= htmlspecialchars($case_title) ?>" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Case Description</label>
                        <textarea class="form-control" name="case_description" rows="3"><?= htmlspecialchars($case_description) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Case Date</label>
                        <input type="date" class="form-control" name="case_date" value="<?= htmlspecialchars($case_date) ?>" />
                    </div>

                    <div class="col-md-12 d-flex justify-content-center mb-3">
                        <a href="../req/fingerprint.php" class="text-decoration-none text-center">
                            <div class="border rounded p-3 shadow-sm bg-light" style="width: 160px; cursor: pointer;">
                                <img src="../img/fingerPrint_01.png" alt="Fingerprint Icon" style="width: 80px; height: 90px;">
                                <div class="mt-2 text-dark" style="font-size: 14px;">Add a fingerprint</div>
                            </div>
                        </a>
                    </div>

                    <button type="submit" class="btn btn-primary">Register Suspect & Case</button>
                </form>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

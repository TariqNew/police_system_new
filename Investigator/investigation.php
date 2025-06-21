<?php
session_start();
if (
    !(
        (isset($_SESSION['officer_id']) && $_SESSION['role'] === 'Officer') ||
        (isset($_SESSION['investigator_id']) && $_SESSION['role'] === 'Investigator')
    )
) {
    header("Location: ../login.php");
    exit;
}

require_once "../DB_connection.php"; // Make sure $conn is a PDO instance

$selected_case_id = '';
$date_started = '';
$date_closed = '';
$success_msg = '';
$error_msg = '';

// Fetch all criminal cases with suspect info
$cases = [];
try {
    $stmt = $conn->query("SELECT cc.case_id, cc.case_title, s.suspect_id, s.fname, s.lname
                        FROM criminal_cases cc
                        JOIN suspects s ON cc.suspect_id = s.suspect_id
                        ORDER BY cc.case_title ASC");
    $cases = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_msg = "Error fetching cases: " . htmlspecialchars($e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_case_id = $_POST['case_id'] ?? '';
    $date_started = $_POST['date_started'] ?? '';
    $date_closed = $_POST['date_closed'] ?? null;

    if (empty($selected_case_id) || empty($date_started)) {
        $error_msg = "Case and start date are required.";
    } else {
        $investigator_id = ($_SESSION['role'] === 'Officer') ? $_SESSION['officer_id'] : $_SESSION['investigator_id'];

        try {
            $sql = "INSERT INTO investigations 
              (case_id, investigator_id, status, date_started, date_closed, created_at, updated_at)
              VALUES (:case_id, :investigator_id, 'Pending', :date_started, :date_closed, NOW(), NOW())";

            $stmt = $conn->prepare($sql);
            // If $date_closed is empty string, convert to null
            $date_closed = $date_closed ?: null;

            $stmt->bindValue(':case_id', $selected_case_id, PDO::PARAM_INT);
            $stmt->bindValue(':investigator_id', $investigator_id, PDO::PARAM_INT);
            $stmt->bindValue(':date_started', $date_started, PDO::PARAM_STR);
            $stmt->bindValue(':date_closed', $date_closed, $date_closed === null ? PDO::PARAM_NULL : PDO::PARAM_STR);

            $stmt->execute();

            $success_msg = "Investigation added successfully.";
            $selected_case_id = '';
            $date_started = '';
            $date_closed = '';
        } catch (PDOException $e) {
            $error_msg = "Database error: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add Investigation</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="icon" href="../logo.png">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script>
        const cases = <?= json_encode($cases) ?>;

        function onCaseChange() {
            const select = document.getElementById('caseSelect');
            const selectedCaseId = select.value;
            const suspectFnameInput = document.getElementById('suspectFname');
            const suspectLnameInput = document.getElementById('suspectLname');

            const selectedCase = cases.find(c => c.case_id == selectedCaseId);
            if (selectedCase) {
                suspectFnameInput.value = selectedCase.fname;
                suspectLnameInput.value = selectedCase.lname;
            } else {
                suspectFnameInput.value = '';
                suspectLnameInput.value = '';
            }
        }

        window.onload = () => onCaseChange();
    </script>
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
    <?php include "inc/navbar.php"; ?>

    <div class="container-fluid">
        <div class="row" style="padding-top: 56px;">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2">
                <?php include "inc/sidebar.php"; ?>
            </div>

            <!-- Main Content -->
            <main class="col-md-9 col-lg-10 py-4">
                <h3>Add New Investigation</h3>

                <?php if ($error_msg): ?>
                    <div class="alert alert-danger"><?= $error_msg ?></div>
                <?php endif; ?>

                <?php if ($success_msg): ?>
                    <div class="alert alert-success"><?= $success_msg ?></div>
                <?php endif; ?>

                <form method="post" class="shadow p-4 bg-light">

                    <div class="mb-3">
                        <label for="caseSelect" class="form-label">Select Case</label>
                        <select id="caseSelect" name="case_id" class="form-select" onchange="onCaseChange()" required>
                            <option value="">-- Select a case --</option>
                            <?php foreach ($cases as $case): ?>
                                <option value="<?= htmlspecialchars($case['case_id']) ?>"
                                    <?= ($case['case_id'] == $selected_case_id) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($case['case_title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <h5>Suspect Info (readonly)</h5>
                    <div class="mb-3">
                        <label class="form-label">First Name</label>
                        <input type="text" id="suspectFname" class="form-control" readonly />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text" id="suspectLname" class="form-control" readonly />
                    </div>

                    <hr />

                    <div class="mb-3">
                        <label for="dateStarted" class="form-label">Investigation Start Date</label>
                        <input type="date" id="dateStarted" name="date_started" class="form-control"
                            value="<?= htmlspecialchars($date_started) ?>" required />
                    </div>

                    <div class="mb-3">
                        <label for="dateClosed" class="form-label">Investigation Close Date (optional)</label>
                        <input type="date" id="dateClosed" name="date_closed" class="form-control"
                            value="<?= htmlspecialchars($date_closed) ?>" />
                    </div>

                    <button type="submit" class="btn btn-primary">Save Investigation</button>
                </form>
            </main>
        </div>
    </div>

    <script>
        const cases = <?= json_encode($cases) ?>;

        function onCaseChange() {
            const select = document.getElementById('caseSelect');
            const selectedCaseId = select.value;
            const suspectFnameInput = document.getElementById('suspectFname');
            const suspectLnameInput = document.getElementById('suspectLname');

            const selectedCase = cases.find(c => c.case_id == selectedCaseId);
            if (selectedCase) {
                suspectFnameInput.value = selectedCase.fname;
                suspectLnameInput.value = selectedCase.lname;
            } else {
                suspectFnameInput.value = '';
                suspectLnameInput.value = '';
            }
        }

        window.onload = () => onCaseChange();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
</body>


</html>
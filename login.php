<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Login - CRMS</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="css/style.css">
	<link rel="icon" href="ifm_4.png">
</head>

<body class="body-login">
	<div class="black-fill"><br /> <br />
		<div class="d-flex justify-content-center align-items-center flex-column">
			<form class="login" method="post" action="req/login.php">

				<div class="text-center">
					<img src="img/police.png" width="100">
				</div>
				<h5 class="text-center mx-4">CRMS | LOGIN</h5>
				<?php if (isset($_GET['error'])) { ?>
					<div class="alert alert-danger" role="alert">
						<?= $_GET['error'] ?>
					</div>
				<?php } ?>
				<div class="mb-3">
					<label class="form-label">Username</label>
					<input type="text" class="form-control" name="uname">
				</div>

				<div class="mb-3">
					<label class="form-label">Password</label>
					<input type="password" class="form-control" name="pass">
				</div>

				<div class="mb-3">
					<label class="form-label">Login As</label>
					<select class="form-control" name="role">
						<option value="1">Administrator</option>
						<option value="2">Police Officer</option>
						<option value="3">Investigator</option>
					</select>
				</div>

				<button type="submit" class="btn btn-primary">Login</button>
				<a href="index.php" class="text-decoration-none">Home</a>
			</form>
			<div class="fingeprint-section text-center fs-5 mt-3" style="color: blue">
				<hr>
				<a href="req/fingerprint.php">use the fingerprint authentication</a>
			</div>
			<br /><br />
			<div class="text-center mt-5">
				Copyright &copy; 2024 . All rights reserved.
			</div>
		</div>
	</div>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
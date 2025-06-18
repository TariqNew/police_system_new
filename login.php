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
					<img src="" width="100">
				</div>
				<h3>LOGIN</h3>
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
			<div class="fingeprint-section text-center text-white fs-5">
				<hr>
				<span>OR</span>
				<div>use the fingerprint authentication</div>
				<div class="img-div">
					<a href="req/fingerprint.php">
						<img src="img/fingerprint.jpg" alt="">
					</a>
				</div>
			</div>
			<br /><br />
			<div class="text-center text-light">
				Copyright &copy; 2024 . All rights reserved.
			</div>

		</div>
	</div>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
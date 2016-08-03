<?php

	include('header.php');
	//die($_SESSION['redirect']);


?>

	<h1>Login</h1>

	<hr class="short-fat" />

	<?php
	if(!empty($_POST)){

		if(logged_in()) {
			if(!empty($_SESSION['redirect'])){

				$tmp = $_SESSION['redirect'];
				$_SESSION['redirect'] = "";
				echo "<script>window.location = '{$tmp}';</script>";
			}
			else if(logged_in()) echo "<script>window.location = 'admin.php';</script>";
		}
		if(!logged_in()){
			?>
			<div class="row">
				<div class="col-md-6 col-md-offset-3">
					<div class="alert alert-danger" role="alert">Invalid Username or Password</div>
				</div>
			</div>
			<?php

		}
	}
	if(logged_in()) echo "<script>window.location = 'admin.php';</script>";
	?>

	<form method="post">
		<div class="row">
			<div class="col-md-6 col-md-offset-3">

				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label class="sr-only" for="username" required>Username</label>
							<input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label class="sr-only" for="password" required>Password</label>
							<input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<p>By logging into English Learning Hub's platform you agree to the <a href="terms.php">Terms of Use</a></p>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="checkbox-inline">
								<input type="checkbox" id="remember" name="remember" value="yes"> Remember Me?
							</label>

						</div>
					</div>
					<div class="col-md-6 text-right">
							<a href="forgotPassword.php">Forgot Password?</a>
					</div>
				</div>

					<div class="row">
					<div class="col-md-12">
						<div class="form-group text-right">
							<input type="submit" class="btn btn-primary" value="Login">
						</div>
					</div>
				</div>
			</div>
		</div>

	</form>

<?php



?>
<?php include('footer.php'); ?>
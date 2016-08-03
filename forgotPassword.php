<?php

include('header.php');

if(logged_in()) echo "<script>window.location = 'admin.php';</script>";

?>

	<h1>Forgot Password</h1>

	<hr class="short-fat" />

<?php
if(!empty($_POST)){

	$pw = substr(md5(uniqid()), 0, 8);
	$id = get_teacher_id_by_email($_POST['email']);
	if($id) {
		update_teacher($id, ['password' => $pw]);

		//TODO: Make password reset email

		$to = $_POST['email'];
		$subject = 'ELH Password Reset';
		$message = 'Your new password is: '.$pw;
		$headers = 'From: webmaster@englishlessonhub.com' . "\r\n" .
			'X-Mailer: PHP/' . phpversion();

		mail($to, $subject, $message, $headers);
	}

	echo "<div class='alert alert-success' role='alert'><b>Password Changed</b> Please check your email address</div>";

}

?>

	<form method="post">
		<div class="row">
			<div class="col-md-6 col-md-offset-3">

				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label class="sr-only" for="email" required>Email Address</label>
							<input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<p>Entering your email and pressing Reset Password will cause a new password to be generated and emailed to your email address.</p>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group text-right">
							<input type="submit" class="btn btn-primary" value="Reset Password">
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>

<?php



?>
<?php include('footer.php'); ?>
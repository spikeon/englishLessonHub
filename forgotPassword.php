<?php

include('header.php');

if(logged_in()) echo "<script>window.location = 'admin.php';</script>";

?>

	<h1>Forgot Password</h1>

	<hr class="short-fat" />

<?php
if(!empty($_POST)){

	$pw = substr(md5(uniqid()), 0, 8);
	$type = 'teacher';
	$id = get_teacher_id_by_email($_POST['email']);
	if(!$id){
		$type = student;
		$id = get_student_id_by_email($_POST['email']);
	}

	if($id) {

		if($type == 'teacher') update_teacher($id, ['password' => $pw]);
		else update_student($id, ['password' => $pw] );

		$to = $_POST['email'];

		$subject = 'ELH Password Reset';

		$message = "Hello {$type},
 		<p>Your new password is: {$pw}</p>
		<p>Feel free to contact us if you have any problems: <a href='".BASE_URL."/contact.php'>Contact Form</a></p>";

		send_mail($to, $subject, $message);

	}

	echo "<div class='alert alert-success' role='alert'><b>Password Changed</b> Please check your email address.</div>";

}
else{

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

}

?>
<?php include('footer.php'); ?>

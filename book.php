<?php include('header.php'); ?>
<?php
	$teacher = new teacher($_GET['teacher']);
	if(login_type() == 'teacher'){
		// Error

		?><div class="alert alert-danger">You cannot sign up for a class as a teacher</div><?php

	}
	else if(login_type() == 'admin'){
		// Error
		?><div class="alert alert-danger">You cannot sign up for a class as an admin</div><?php
	}
	else if(login_type() == 'student'){
		$failed = true;
		if($teacher->freeclass == '1') {
			$failed = false;
			foreach ((array)$teacher->classes as $c) {
				if ($c['student_id'] == login_id()) $failed = true;
			}

			$student = new student(login_id());
			if ($student->free_classes >= $billing_info['maxFreeClasses']) $failed = true;
		}
		if(!$failed){
			// Free Class, don't go to paypal
			$sth = $db->prepare("INSERT INTO class (teacher_id, student_id, start_time, end_time, status, free) VALUES (?,?,?,?,?, 1)");
			$sth->execute(array($teacher->id, login_id(), $_GET['time'], ($_GET['time']/1000 + ($teacher->duration * 60))*1000, 'free'));
			?><div class="alert alert-success">You are successfully signed up for this class</div><?php
		} else {
			// This class is not free, go to paypal
			$sth = $db->prepare("INSERT INTO class (teacher_id, student_id, start_time, end_time, status) VALUES (?,?,?,?,?)");
			$sth->execute(array($teacher->id, login_id(), $_GET['time'], ($_GET['time']/1000 + ($teacher->duration * 60))*1000, 'init'));
			$iid = $db->lastInsertId();
			?><script>window.location = 'payment_start.php?id=<?php echo $iid;?>&price=<?php echo $teacher->price;?>';</script><?php
		}

	}
	else{
		$_SESSION['redirect'] = "book.php?teacher={$_GET['teacher']}&time={$_GET['time']}";
		?>
			<h1>Account Required</h1>

			<hr class="short-fat" />
			<div class="row">
				<div class="col-md-8 col-md-offset-2">


					<p style="text-align: center;">You must have an account to book a course.  Please choose from the options below</p>

					<div class="row">
						<div class="col-md-6">
							<h2>Sign In</h2>
							<form method="post" action="login.php">
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
							</form>

						</div>
						<div class="col-md-6">
							<h2>Register</h2>

							<p>Sign up for an account at English Lesson Hub to unlock this feature. </p>

							<a href="register_student.php" class="btn btn-primary">Register</a>

						</div>
					</div>
				</div>
			</div>

		<?php
	}
?>
<?php include('footer.php'); ?>
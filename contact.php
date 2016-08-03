<?php

	include('header.php');
?>
	<h1>Contact Us</h1>
	<hr class="short-fat" />
<?php
	if(!empty($_POST)){

		if (!empty($_SERVER['HTTP_CLIENT_IP'])) $ip = $_SERVER['HTTP_CLIENT_IP'];
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else $ip = $_SERVER['REMOTE_ADDR'];


		$opts = array('http' =>
			array(
				'method'  => 'POST',
				'header'  => 'Content-type: application/x-www-form-urlencoded',
				'content' => http_build_query(
					array(
						'secret' => "6LeZFRoTAAAAAFkYrIE3hhmp4XUoxWLlAChxoMM_",
						'response' => $_POST['g-recaptcha-response'],
						'remoteip' => $ip
					)
				)
			)
		);
		$context  = stream_context_create($opts);
		$result = json_decode( file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context));

		if($result->success == false) echo "<div class='alert alert-danger'> BOT ALERT </div>";
		else {

			$to = $admin_info['email'];
			$subject = "New Message from ELH Contact Form";
			$message = "New message from ELH Contact Form\r\n\r\n";
			$message .= "Name: {$_POST['name']}\r\n";
			$message .= "Email: {$_POST['email']}\r\n";
			$message .= "Phone: {$_POST['phone']}\r\n";
			$message .= "User Type: {$_POST['usertype']}\r\n\r\n";
			$message .= "Message: \r\n\r\n";
			$message .= $_POST['message'];
			$headers = 'From: ' . $_POST['name'] . ' <'.$_POST['email'] . '>'."\r\n" .
				'X-Mailer: PHP/' . phpversion();

			mail($to, $subject, $message, $headers);
			echo "<div class='alert alert-success'> Message Sent </div>";
		}
	}
	else {
		$user = false;
		if(login_type() == 'teacher') $user = get_teacher(login_id());
		else if (login_type() == 'student') $user = get_student(login_id());
		?>
		<form method="post">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label class="sr-only" for="name">Name</label>
						<input type="text" class="form-control" id="name" placeholder="Name" name="name" value="<?php if(logged_in()) echo $user->name; ?>" required>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label class="sr-only" for="usertype">User Type</label>
						<select class="form-control" id="usertype" name="usertype">
							<option disabled selected>User Type</option>
							<option value="teacher"  <?php if(logged_in() && login_type() == 'teacher') echo "SELECTED"; ?> >Teacher</option>
							<option value="student" <?php if(logged_in() && login_type() == 'student') echo "SELECTED"; ?> >Student</option>
						</select>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label class="sr-only" for="phone">Phone Number</label>
						<input type="phone" class="form-control" id="phone" name="phone" placeholder="Phone Number">
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label class="sr-only" for="email">Email address</label>
						<input type="email" class="form-control" id="email" name="email" placeholder="Email"  value="<?php if(logged_in()) echo $user->email; ?>" required>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
					<textarea class="form-control" rows="3" name="message" placeholder="Message" required></textarea>
				</div>
			</div>

			<div class="row" style="margin-top: 10px;">
				<div class="col-md-7"></div>
				<div class="col-md-3 text-right">
					<div class="g-recaptcha" data-sitekey="6LeZFRoTAAAAAJAdCKUCvHtDTFlEJl-DsnadUtmB"></div>
				</div>
				<div class="col-md-2 text-right">
					<input class="btn btn-primary" type="submit" value="Send">
				</div>
			</div>
		</form>
		<?php

	}

?>
<?php include('footer.php'); ?>
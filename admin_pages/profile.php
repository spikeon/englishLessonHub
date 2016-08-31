<?php

if(login_type() == 'teacher'){

	if(!empty($_POST)){

		$info = array();
		foreach ($_POST as $k => $v) {
			$info[$k] = $v;
		}
		if(!isset($info['freeclass'])) $info['freeclass'] = false;

		$uuid = uniqid();
		$File_Name = strtolower($_FILES['FileInput']['name']);
		$File_Ext = substr($File_Name, strrpos($File_Name, '.'));

		switch (strtolower($_FILES['FileInput']['type'])) {
			//allowed file types
			case 'image/png':
			case 'image/gif':
			case 'image/jpeg':
			case 'image/pjpeg':
			case 'text/plain':
			case 'text/html': //html file
			case 'application/x-zip-compressed':
			case 'application/pdf':
			case 'application/msword':
			case 'application/vnd.ms-excel':
			case 'video/mp4':
				if (move_uploaded_file($_FILES['FileInput']['tmp_name'], 'uploads/' . $uuid  . $File_Ext)) $info['photo'] = $uuid  . $File_Ext;
				break;
		}

		update_teacher(login_id(), $info);

		echo "<div class='alert alert-success' role='alert'>Profile Updated</div>";

	}
	$teacher = get_teacher(login_id());
	?>

		<form method="post" enctype="multipart/form-data">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="firstname" required>First Name</label>
						<input type="text" class="form-control" id="firstname" name="first_name" value="<?php echo $teacher->first_name; ?>" placeholder="First Name">
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="lastname" required>Last Name</label>
						<input type="text" class="form-control" id="lastname" name="last_name" value="<?php echo $teacher->last_name; ?>" placeholder="Last Name">
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="email" required>Email address</label>
						<input type="email" class="form-control" id="email" name="email" placeholder="Email"  value="<?php echo $teacher->email; ?>" disabled>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="username" required>Username</label>
						<input type="text" class="form-control" id="username" name="username" placeholder="Username"  value="<?php echo $teacher->username; ?>" disabled>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="skype">Skype Name</label>
						<input type="text" class="form-control" id="skype" name="skype" placeholder="Skype Name"  value="<?php echo $teacher->skype; ?>" required>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="paypal">Paypal Address</label>
						<input type="text" class="form-control" id="paypal" name="paypal" placeholder="Paypal Address"  value="<?php echo $teacher->paypal; ?>" required>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-3">
					<input type="hidden" name="photo"  value="<?php echo $teacher->photo; ?>" >
					<img src="<?php echo $teacher->thumb; ?>" style="margin-bottom: 10px;">
				</div>
				<div class="col-md-3">

							<h4>Choose Photo</h4>
							<input type="file" name="FileInput">

				</div>
				<div class="col-md-6">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="price">Price</label>

								<div class="input-group">
									<span class="input-group-addon">&euro;</span>
									<input type="number" class="form-control" id="price" name="price" placeholder="Price" min="1" value="<?php echo $teacher->payment; ?>" required>
									<span class="input-group-addon">.00</span>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="duration">Duration</label>

								<div class="input-group">
									<input type="number" class="form-control" id="duration" name="duration" placeholder="Duration"  value="<?php echo $teacher->duration; ?>" required>
									<span class="input-group-addon">Minutes</span>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="checkbox-inline">
									<input type="checkbox" name="freeclass" id="freeclass" value="1" <?php echo $teacher->freeclass ? "checked" : ""; ?>> Allow free first
									lesson
								</label>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="country">Nationality</label>
								<input type="text" class="form-control" id="country" name="country" placeholder="Nationality"  value="<?php echo $teacher->country; ?>" required>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="method">Method</label>
								<input type="text" class="form-control" id="method" name="method" placeholder="Method"  value="<?php echo $teacher->method; ?>" required>
							</div>
						</div>
					</div>

				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
					<label for="description">Description</label>
					<textarea class="form-control" id="description" rows="3" placeholder="Description" name="description" required><?php echo $teacher->description; ?></textarea>
				</div>
			</div>

			<div class="row" style="margin-top: 10px;">

				<div class="col-md-12 text-right">
					<input class="btn btn-primary" type="submit" id="submit" value="Send">
				</div>
			</div>
		</form>
	<?php


}
else if(login_type() == 'student'){

if(!empty($_POST)){

	$info = array();
	foreach ($_POST as $k => $v) $info[$k] = $v;
	update_student(login_id(), $info);

	echo "<div class='alert alert-success' role='alert'>Profile Updated</div>";

}
	$student = get_student(login_id());
	?>
	<form method="post" enctype="multipart/form-data">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="firstname" required>First Name</label>
					<input type="text" class="form-control" id="firstname" name="first_name" placeholder="First Name" value="<?php echo $student->first_name; ?>">
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="lastname" required>Last Name</label>
					<input type="text" class="form-control" id="lastname" name="last_name" placeholder="Last Name" value="<?php echo $student->last_name; ?>">
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="email" required>Email address</label>
					<input type="email" class="form-control" id="email" name="email" placeholder="Email"  value="<?php echo $student->email; ?>" disabled>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="username" required>Username</label>
					<input type="text" class="form-control" id="username" name="username" placeholder="Username"  value="<?php echo $student->username; ?>" disabled>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="skype">Skype Name</label>
					<input type="text" class="form-control" id="skype" name="skype" placeholder="Skype Name"  value="<?php echo $student->skype; ?>" required>
				</div>
			</div>
		</div>
		<div class="row" style="margin-top: 10px;">
			<div class="col-md-12 text-right">
				<input class="btn btn-primary" type="submit" id="submit" value="Save">
			</div>
		</div>
	</form>
	<?php

}

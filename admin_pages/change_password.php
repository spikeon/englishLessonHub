<?php
if(!empty($_POST)){
	if($_POST['pw1'] == "" || $_POST['pw2'] == "") echo "<div class='alert alert-danger' role='alert'>Passwords Cannot Be Blank</div>";
	else if($_POST['pw1'] == $_POST['pw2']) {
		if(login_type() == 'teacher') update_teacher(login_id(), ['password' => $_POST['pw1']]);
		else update_student(login_id(), ['password' => $_POST['pw1']]);
		echo "<div class='alert alert-success' role='alert'>Password Changed</div>";
	}
	else echo "<div class='alert alert-danger' role='alert'>Passwords Must Match</div>";
}
else{
?>

<form method="post" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label class="sr-only" for="password1">First Name</label>
				<input type="password" class="form-control" id="password1" name="pw1" value="" placeholder="Password" required>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label class="sr-only" for="password2">First Name</label>
				<input type="password" class="form-control" id="password2" name="pw2" value="" placeholder="Verify Password" required>
			</div>
		</div>
	</div>
	<div class="row" style="margin-top: 10px;">
		<div class="col-md-12 text-right">
			<input class="btn btn-primary" type="submit" id="submit" value="Send">
		</div>
	</div>

</form>
<?php } ?>

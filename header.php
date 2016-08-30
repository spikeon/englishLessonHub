<?php
	include('config.php');
	if(!empty($_POST['username'])) auth($_POST['username'], $_POST['password'], !empty($_POST['remember']));
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="fragment" content="!">
	<title>English Lesson Hub</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

	<?php /* <script src="//code.jquery.com/jquery-1.11.3.min.js"></script> */ ?>
	<script src="node_modules/jquery/dist/jquery.min.js"></script>
	<script src="node_modules/html5formshim/build/jquery.html5form.min.js"></script>

	<link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap-theme.min.css">

	<link rel="stylesheet" href="style.css">

	<script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>

	<?php if(login_type() != 'admin'){ ?>
		<script src="node_modules/ckeditor/ckeditor.js"></script>
	<?php } else { ?>
		<script src="ckeditor/ckeditor.js"></script>
	<?php } ?>
	<script src='https://www.google.com/recaptcha/api.js'></script>
	<script src="node_modules/angular/angular.min.js"></script>


	<link href='fullcalendar/fullcalendar.css' rel='stylesheet' />
	<link href='fullcalendar/fullcalendar.print.css' rel='stylesheet' media='print' />
	<script src='fullcalendar/lib/moment.min.js'></script>

	<script src='fullcalendar/fullcalendar.min.js'></script>


	<script src='availability.js'></script>


	<script src="angular/app.js"></script>

	<script>
		$(function(){
			var wh = $( window ).height();
			var hh = $(".header").outerHeight();
			var fh = $(".footer").outerHeight();
			var sfh = $(".sub-footer").outerHeight();
			$(".content").css("min-height", (wh - hh - fh - (sfh*2)) + "px");
		});
	</script>

	<link rel="icon" href="favicon.png">

</head>

<body>
<nav class="navbar navbar-default navbar-fixed-top hidden-lg hidden-xlg hidden-md hidden-sm">
	<div class="container">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="index.php">English Lesson Hub</a>
		</div>

		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav">

				<li><a href="about.php">About</a></li>
				<li><a href="how_it_works.php">How it Works</a></li>
				<li><a href="testimonials.php">Testimonials</a></li>
				<li><a href="contact.php">Contact</a></li>
				<?php if(!logged_in()) { ?>
					<li><a href="register_teacher.php">Teacher Register</a></li>
					<li><a href="register_student.php">Student Register</a></li>

					<li><a href="login.php">Login</a></li>
				<?php } else { ?>

					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dashboard <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li role="separator" class="divider"></li>
							<li><a href="admin.php">Dashboard</a></li>
							<?php if(login_type() == 'admin'){ ?>
								<li><a href="admin.php?page=editAbout">About</a></li>
								<li><a href="admin.php?page=editHowitworks">How it Works</a></li>
								<li><a href="admin.php?page=editPrivacy">Privacy Policy</a></li>
								<li><a href="admin.php?page=editTerms">Terms and Conditions</a></li>
								<li><a href="admin.php?page=testimonials">Testimonials</a></li>
							<?php } else if(login_type() == 'student'){ ?>
								<li><a href="admin.php?page=profile">Edit Profile</a></li>
							<?php } else if(login_type() == 'teacher'){ ?>
								<li><a href="admin.php?page=profile">Edit Profile</a></li>

							<?php } ?>


							<li role="separator" class="divider"></li>

						</ul>
					</li>

					<li><a href="logout.php">Logout</a></li>
				<?php } ?>
				<li><a href="privacy.php">Privacy Policy</a></li>
				<li><a href="terms.php">Terms and Conditions</a></li>

			</ul>
		</div>
	</div>
</nav>
<div class="hidden-md hidden-lg hidden-xlg hidden-sm" style="height: 50px;"></div>
<div class="header  hidden-xs">
	<div class="container">
		<nav class="col-md-4 col-sm-4 nav">
			<a href="about.php">About</a> |
			<a href="how_it_works.php">How It Works</a> |
			<a href="testimonials.php">Testimonials</a> |
			<a href="contact.php">Contact</a>
		</nav>

		<div  class="col-md-4 col-sm-3 logo">
			<a href="index.php">
				<img src="images/logo.png" style="width: 100%;" class="hidden-lg hidden-md">
				<img src="images/logo.png" class="hidden-sm">
			</a>
		</div>

		<div class="buttons col-md-4" >
			<?php if(!logged_in()){ ?>
				<a class="student-sign-up btn btn-primary" href="register_student.php"><span class="glyphicon glyphicon-education icon"></span><span class="top">STUDENT</span><span class="bottom">Sign Up</span></a>
				<a class="login  btn btn-danger" href="login.php"><span class="glyphicon glyphicon-log-in icon"></span>LOGIN</a>
				<a class="teacher-sign-up  btn btn-primary" href="register_teacher.php"><span class="glyphicon glyphicon-apple icon"></span><span class="top">TEACHERS</span><span class="bottom">Post Yourself for FREE</span></a>
			<?php } else { ?>
				<a class="dashboard btn btn-primary" href="admin.php"><span class="glyphicon glyphicon-dashboard icon"></span>DASHBOARD</a>
				<a class="logout btn btn-danger" href="logout.php"><span class="glyphicon glyphicon-log-out icon"></span>LOGOUT</a>
				<?php if(login_type() == "student") { ?>
					<a class="search-teachers  btn btn-primary" href="index.php"><span class="glyphicon glyphicon-apple icon"></span><span class="top">SEARCH</span><span class="bottom">for Teachers</span></a>
				<?php } else if (login_type() == "teacher") { ?>
					<a class="go-profile  btn btn-primary" href="teacher.php?id=<?php echo login_id(); ?>"><span class="glyphicon glyphicon-apple icon"></span><span class="top">PROFILE</span><span class="bottom">View your profile</span></a>
				<?php } ?>
			<?php } ?>
		</div>
	</div>
</div>
<div class="container content">

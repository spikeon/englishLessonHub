<?php

	include('header.php');

	if(!logged_in()) echo "<script>window.location = 'login.php';</script>";

	if(empty($_GET['page'])) $page = 'home';
	else $page = $_GET['page'];

	$titles = [
		'home' => "Dashboard",
		'profile' => "Edit Profile",
		'editAbout' => "Edit About Page",
		'editHowitworks' => "Edit How it Works Page",
		'editPrivacy' => "Edit Privacy Policy",
		'editTerms' => "Edit Terms of Use",
		'testimonials' => "Testimonials",
		'testimonial' => "Edit Testimonial",
		'availability' => "Update Availability",
		'change_password' => "Change Password",
		'userManagement' => "User Management",

	];

	echo "<h1>{$titles[$page]}</h1><hr class='short-fat'/>";

	if(!stristr($page,'edit')){

?>

<div class="row">
	<div class="col-md-2 hidden-xs">
		<ul class="nav nav-pills nav-stacked">
			<li role="presentation" <?php if($page == 'home') echo 'class="active"'; ?>><a href="?page=home">Dashboard</a></li>
			<?php
				if(login_type() == 'teacher' || login_type() == 'student') {
					?>
						<li role="presentation" <?php if($page == 'profile') echo 'class="active"'; ?>><a href="?page=profile">Edit Profile</a></li>
						<li role="presentation" <?php if($page == 'change_password') echo 'class="active"'; ?>><a href="?page=change_password">Change Password</a></li>

					<?php
				}
				if(login_type() == 'teacher'){
					?><li role="presentation" <?php if($page == 'availability') echo 'class="active"'; ?>><a href="?page=availability">Availability</a></li><?php
				}
				if(login_type() == 'admin'){
					?>
					<li role="presentation" <?php if($page == 'userManagement') echo 'class="active"'; ?>><a href="?page=userManagement">User Management</a></li>
					<li role="presentation" <?php if($page == 'editAbout') echo 'class="active"'; ?>><a href="?page=editAbout">About</a></li>
					<li role="presentation" <?php if($page == 'editHowitworks') echo 'class="active"'; ?>><a href="?page=editHowitworks">How It Works</a></li>
					<li role="presentation" <?php if($page == 'editTerms') echo 'class="active"'; ?>><a href="?page=editTerms">Terms and Conditions</a></li>
					<li role="presentation" <?php if($page == 'editPrivacy') echo 'class="active"'; ?>><a href="?page=editPrivacy">Privacy Policy</a></li>
					<li role="presentation" <?php if($page == 'testimonials') echo 'class="active"'; ?>><a href="?page=testimonials">Testimonials</a></li>



					<?php
				}


			?>
			<li role="presentation"><a href="logout.php">Logout</a></li>
		</ul>
	</div>
	<div class="col-md-10 col-xs-12">
		<?php
			}
			include('admin_pages/'.$page.".php");
			if(!stristr($page,'edit')){
		?>

	</div>
</div>

<?php } include('footer.php'); ?>
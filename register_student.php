<?php
include('header.php');
?>
	<script>
		jQuery(window).ready(function($){
			var check_fail = function(){
				if($('#password2').parent().hasClass('has-warning')) return true;
				if($('#username').parent().hasClass('has-warning')) return true;
				if($('#email').parent().hasClass('has-warning')) return true;
				return false;
			};
			var check_disable = function(){
				var fail = check_fail();
				$('#submit').prop("disabled",fail);
			};


			$('#password2').keyup(function(){
				if($(this).val() == $('#password').val()){
					$(this).parent().find('.form-control-feedback').hide();
					$(this).parent().removeClass('has-warning has-feedback');
					check_disable();
				}
				else {
					$(this).parent().find('.form-control-feedback').show();
					$(this).parent().addClass('has-warning has-feedback');
					check_disable();
				}
			});
			$('#username').blur(function(){
				$.getJSON(
					'ajax.php',
					{
						query : 'unique',
						type : 'username',
						value : $(this).val()
					},
					function(data){
						if(data.unique){
							$('#username').parent().find('.form-control-feedback').hide();
							$('#username').parent().find('.help-block').hide();
							$('#username').parent().removeClass('has-warning has-feedback');
							check_disable();
						}
						else {
							$('#username').parent().find('.form-control-feedback').show();
							$('#username').parent().find('.help-block').show();
							$('#username').parent().addClass('has-warning has-feedback');
							check_disable();
						}
					}
				);

			});
			$('#email').blur(function(){
				$.getJSON(
					'ajax.php',
					{
						query : 'unique',
						type : 'email',
						value : $(this).val()
					},
					function(data){
						if(data.unique){
							$('#email').parent().find('.form-control-feedback').hide();
							$('#email').parent().find('.help-block').hide();
							$('#email').parent().removeClass('has-warning has-feedback');
							check_disable();
						}
						else {
							$('#email').parent().find('.form-control-feedback').show();
							$('#email').parent().find('.help-block').show();
							$('#email').parent().addClass('has-warning has-feedback');
							check_disable();
						}
					}
				);
			});

			check_disable();

		});
	</script>
	<h1>Student Registration</h1>

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

	if($result->success == false) echo "<p>Apparently you are a bot.</p>";
	else {
		unset($_POST['g-recaptcha-response']);
		$info = array();
		foreach ($_POST as $k => $v) $info[$k] = $v;
		$student = new student(false, $info);
		?>
			<p>Thank you for registering. Please <a href="login.php">click here</a> to login. </p>
		<?php
	}

} else {
	?>
	<form method="post" enctype="multipart/form-data"  data-enable-shim="true">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label class="sr-only" for="firstname">First Name</label>
					<input type="text" class="form-control" id="firstname" name="first_name" placeholder="First Name" required>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label class="sr-only" for="lastname">Last Name</label>
					<input type="text" class="form-control" id="lastname" name="last_name" placeholder="Last Name" required>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label class="sr-only" for="email">Email address</label>
					<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true" style="display:none;"></span>
					<input type="email" class="form-control" id="email" name="email" placeholder="Email"  required>
					<span class="help-block"  style="display:none;">The chosen email address has already been used</span>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label class="sr-only" for="username">Username</label>
					<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true" style="display:none;"></span>
					<input type="text" class="form-control" id="username" name="username" placeholder="Username"  required>
					<span class="help-block"  style="display:none;">The chosen username has already been used</span>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label class="sr-only" for="password">Password</label>
					<input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label class="sr-only" for="password2">Verify Password</label>
					<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true" style="display:none;"></span>
					<input type="password" class="form-control" id="password2" placeholder="Verify Password" required>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label class="sr-only" for="skype">Skype Name</label>
					<input type="text" class="form-control" id="skype" name="skype" placeholder="Skype Name" required>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="terms">
					<h3>Terms and Conditions</h3>
					<?php
					$name = 'terms';
					$stmt2 = $db->prepare("SELECT * FROM pages WHERE name = ?");
					$stmt2->bindParam(1, $name);
					$stmt2->execute();
					$blackboard_data = $stmt2->fetch(PDO::FETCH_ASSOC);
					echo $blackboard_data['content'];
					?>
				</div>
			</div>
		</div>
		<div class="row" style="margin-top: 10px;">
			<div class="col-md-6">
				<div class="form-group">
					<label class="checkbox-inline">
						<input type="checkbox" id="iagree" value="yes" required> I agree to the terms and conditions
					</label>
				</div>
			</div>
			<div class="col-md-3 text-right">
				<div class="g-recaptcha" data-sitekey="6LeZFRoTAAAAAJAdCKUCvHtDTFlEJl-DsnadUtmB"></div>
			</div>
			<div class="col-md-3 text-right">
				<input class="btn btn-primary" type="submit" id="submit" value="Send" disabled>
			</div>
		</div>
	</form>
	<?php
}
include('footer.php');
?>

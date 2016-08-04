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

			$('#submit').prop("disabled",false);

		});
	</script>
	<h1>Teacher Registration</h1>

	<hr class="short-fat" />

	<?php
		if(!empty($_POST)){

			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			$postdata = http_build_query(
				array(
					'secret' => "6LeZFRoTAAAAAFkYrIE3hhmp4XUoxWLlAChxoMM_",
					'response' => $_POST['g-recaptcha-response'],
					'remoteip' => $ip
				)
			);

			$opts = array('http' =>
				array(
					'method'  => 'POST',
					'header'  => 'Content-type: application/x-www-form-urlencoded',
					'content' => $postdata
				)
			);

			$context  = stream_context_create($opts);

			$result = json_decode( file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context));

			if($result->success == false) {
				var_dump($result);
				?>
					<p>Apparently you are a bot.</p>
				<?php
			}
			else {
				unset($_POST['g-recaptcha-response']);


				$info = array();
				foreach ($_POST as $k => $v) {
					$info[$k] = $v;
				}

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

				$teacher = new teacher(false, $info);

				?>
					<p>Thank you for registering. Please <a href="login.php">click here</a> to login. </p>
				<?php
			}

		} else {
			?>
				<form method="post" enctype="multipart/form-data">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="sr-only" for="firstname" required>First Name</label>
								<input type="text" class="form-control" id="firstname" name="first_name" placeholder="First Name"  required>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="sr-only" for="lastname" required>Last Name</label>
								<input type="text" class="form-control" id="lastname" name="last_name" placeholder="Last Name"  required>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="sr-only" for="email" required>Email address</label>
								<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true" style="display:none;"></span>
								<input type="email" class="form-control" id="email" name="email" placeholder="Email"  required>
								<span class="help-block"  style="display:none;">The chosen email address has already been used</span>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="sr-only" for="username" required>Username</label>
								<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true" style="display:none;"></span>
								<input type="text" class="form-control" id="username" name="username" placeholder="Username"  required>
								<span class="help-block"  style="display:none;">The chosen username has already been used</span>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="sr-only" for="password" required>Password</label>
								<input type="password" class="form-control" id="password" name="password" placeholder="Password"  required>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="sr-only" for="password2" required>Verify Password</label>
								<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true" style="display:none;"></span>
								<input type="password" class="form-control" id="password2" placeholder="Verify Password"  required>
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
						<div class="col-md-6">
							<div class="form-group">
								<label class="sr-only" for="paypal">Paypal Address</label>
								<input type="text" class="form-control" id="paypal" name="paypal" placeholder="Paypal Address" required>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div style="position:relative;">
								<h4>Choose Photo</h4>
								<input type="file" required name="FileInput" >
							</div>
						</div>
						<div class="col-md-6">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="sr-only" for="price">Price</label>

										<div class="input-group">
											<span class="input-group-addon">&euro;</span>
											<input type="number" class="form-control" min="1" id="price" name="price" placeholder="Price" required>
											<span class="input-group-addon">.00</span>
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="sr-only" for="duration">Duration</label>

										<div class="input-group">
											<input type="number" class="form-control" id="duration" name="duration" placeholder="Duration" required>
											<span class="input-group-addon">Minutes</span>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label class="checkbox-inline">
											<input type="checkbox" name="freeclass" id="freeclass" value="1"> Allow free first
											lesson
										</label>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label class="sr-only" for="country">Nationality</label>
										<input type="text" class="form-control" id="country" name="country" placeholder="Nationality" required>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label class="sr-only" for="method">Method</label>
										<input type="text" class="form-control" id="method" name="method" placeholder="Method/Books" required>
									</div>
								</div>
							</div>

						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<label class="sr-only" for="description">Description</label>
							<textarea class="form-control" id="description" rows="3" placeholder="Description" name="description" required></textarea>
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
							<input class="btn btn-primary" type="submit" id="submit" value="Send"  disabled>
						</div>
					</div>
				</form>
			<?php
		}
		include('footer.php');
	?>

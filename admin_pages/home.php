<?php if(login_type() != 'admin'){ ?>
	<script>
		var cancelto = "<?php echo login_type() == 'teacher' ? 'student' : 'teacher'; ?>";
	<?php
		if(login_type() == 'teacher'){
			$teacher = new teacher(login_id());
			$classes = [];
			foreach($teacher->classes as $class){
				if(($class['status'] == 'free' || $class['status'] == 'Completed') && $class['end_time'] > time()*1000) $classes[] = $class;
			}
			echo "var classes = ". json_encode($classes).";";
		}
		else if(login_type() == 'student'){
			$student = new student(login_id());
			$classes = [];
			foreach($student->classes as $class){
				if(($class['status'] == 'free' || $class['status'] == 'Completed') && $class['end_time'] > time()*1000) $classes[] = $class;
			}
			echo "var classes = ". json_encode($classes).";";
		}

	?>
	</script>
	<?php
		if(!empty($_GET['canceled'])){
			if(login_type() == 'student') {
				?>
					<div class="alert alert-success">The class has been canceled, and you have been refunded</div>
				<?php
			}
			else if(login_type() == 'teacher') {
				?>
					<div class="alert alert-success">The class has been canceled, and the student has been refunded</div>
				<?php
			}

		}
	?>
	<div class="panel panel-default classpanel">
		<!-- Default panel contents -->
		<div class="panel-heading">Classes</div>
		<div class="panel-body"><p class="message"></p></div>

		<!-- List group -->
		<ul class="list-group">
			<!--<li class="list-group-item"></li>-->
		</ul>
	</div>


	<a class="btn btn-primary" href="appointment.php">Go to Blackboard</a>

	<h4>CANCELLATION POLICY:</h4>
	Cancellation of a lesson with 1 day or more = NO charge<br>
	Cancellation of a lesson under 1 day = NO refund<br>


	<script>
		/**
		 * Return a timestamp with the format "m/d/yy h:MM:ss TT"
		 * @type {Date}
		 */

		function timeStamp(now) {
			var time = [ now.getHours(), now.getMinutes() ];
			for ( var i = 1; i < 3; i++ ) {
				if ( time[i] < 10 ) {
					time[i] = "0" + time[i];
				}
			}
			return time.join(":");
		}

		function no_future_classes(){
			$('.classpanel').find('.list-group').hide();
			$('.classpanel').find('.message').text("You don't have any classes");
		}

		if(classes.length == 0){
			no_future_classes();
		}
		else {
			var failed = true;
			$.each(classes, function (id, c) {
				if (c.end_time > Date.now()) {
					failed = false;
					var end_time = new Date(c.end_time * 1);
					var start_time = new Date(c.start_time * 1);

					var $new = $("<li class='list-group-item'></li>");
					$new.html("<b>"+ c.partner + "</b> "+ c.formatted_date +" "+ timestamp(start_time));
					$new.append( $("<div class='btn btn-danger pull-right btn-sm' style='margin-top: -5px;'>Cancel</div>").data('whatever', c).click(function(){ $('#cancelModal').data('whatever', $(this).data('whatever')).modal(); } ) );
					$('.classpanel').find('.list-group').append($new);
				}
			});
			if (!failed) {
				$('.classpanel').find('.message').text("Below you will find a list of upcoming classes");
			}
			else {
				no_future_classes();
			}
		}
	</script>
	<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="cancelModalLabel">Cancel Class</h4>
				</div>
				<div class="modal-body">
					<form>
						<div class="form-group">
							<label for="message-text" class="control-label">Message:</label>
							<textarea class="form-control" id="message-text"></textarea>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary">Send message</button>
				</div>
			</div>
		</div>
	</div>
	<script>
		$('#cancelModal').on('show.bs.modal', function (event) {

			var c = $('#cancelModal').data('whatever');
			alert(c);

			$('#cancelModal .btn-primary').data('whatever', c).click(function(){
				var c = $(this).data('whatever');
				var message = $('#message-text');
				$.getJSON(
					'ajax.php',
					{
						query : 'refund',
						txnid : c.paypal,
						tid : c.teacher_id,
						sid : c.student_id,
						cid	: c.id,
						msg	: message.val(),
						to : cancelto
					},
					function(data){
						window.location = "?page=home&canceled=y";
					}
				);
			});
		});

	</script>

<?php } ?>

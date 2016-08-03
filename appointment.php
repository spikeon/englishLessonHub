<?php
	include('header.php');
	echo "<script src='apt.js'></script>";
	$is_teacher = login_type() == 'teacher' ? true : false;
	$user_id = login_id();
	$order_id = 0;
	$error = "";
	$col = login_type() == 'teacher' ? 'teacher_id' : 'student_id';
	$stmt = $db->prepare("SELECT * FROM class WHERE {$col} = ? AND end_time > ".(time()*1000)." AND status = 'Completed' OR status = 'free' ORDER BY start_time ASC LIMIT 1");
	$stmt->bindParam(1, $id);
	$id = login_id();
	$stmt->execute();

	if($stmt->rowCount() == 0) $error =  "You don't have any classes scheduled.";

	$token_data = $stmt->fetch(PDO::FETCH_ASSOC);

	$order_id = $token_data['id'];

	$class_id = $token_data['id'];

	$start_time = $token_data['start_time'];

	$end_time = $token_data['end_time'];

	$txnid = $token_data['paypal'];

	$teacher = new teacher($token_data['teacher_id']);
	$student = new student($token_data['student_id']);

	if(!empty($_GET['test'])){
		$start_time = (time() + 5 ) * 1000;

		$end_time = (time() + 1000 ) * 1000;
	}
	echo "<div style='margin-top: 20px;'></div>";
	if($error){
		echo "<h2>{$error}</h2>";
	}
	else {
		echo "<div class='info' data-starttime='{$start_time}' data-txnid='{$txnid}' data-endtime='{$end_time}' data-type='".login_type()."' data-uid='".login_id()."' data-sid='".$student->id."'  data-tid='".$teacher->id."' data-cid='".$order_id."' data-free='".$token_data['free']."' style='display:none;'>";

		?>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Lesson</h3>
			</div>
			<div class="panel-body">
				<p>You are now connected with: <?php echo $is_teacher ? $student->name : $teacher->name; ?></p>

				<script type="text/javascript" src="https://secure.skypeassets.com/i/scom/js/skype-uri.js"></script>
				<div id="SkypeButton_Call">
					<script type="text/javascript">
						Skype.ui({
							"name": "call",
							"element": "SkypeButton_Call",
							"participants": ["<?php echo $is_teacher ? $student->skype : $teacher->skype; ?>"],
							"imageSize": 32
						});
					</script>
				</div>

				<p></p>
			</div>
		</div>


		<?php
		if ($is_teacher) {
			?>


			<textarea name="blackboard" id="blackboard" rows="10" cols="80" data-lastchanged="<?php echo time(); ?>"
					  data-cid="<?php echo $order_id; ?>"><?php
				$stmt2 = $db->prepare("SELECT * FROM blackboard WHERE order_id = ?");
				$stmt2->bindParam(1, $order_id);
				$stmt2->execute();
				$blackboard_data = $stmt2->fetch(PDO::FETCH_ASSOC);
				echo $blackboard_data['content'];
				?></textarea>
			<script>
				CKEDITOR.replace('blackboard');
			</script>
			<?php
		} else {
			?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Notes from <?php echo $is_teacher ? $student->name : $teacher->name; ?></h3>
				</div>
				<div class="panel-body">


			<div class="blackboard" data-lastchanged="<?php echo time(); ?>" data-cid="<?php echo $order_id; ?>"><?php
			$stmt2 = $db->prepare("SELECT * FROM blackboard WHERE order_id = ?");
			$stmt2->bindParam(1, $order_id);
			$stmt2->execute();
			$blackboard_data = $stmt2->fetch(PDO::FETCH_ASSOC);
			echo $blackboard_data['content'];
			?></div>

				</div>
			</div>
					<?php
		}
		echo "</div>";
		?>
			<div class="msg before"  style='display:none;'>

				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Please Wait...</h3>
					</div>
					<div class="panel-body">
						<p>Please wait for the lesson to start in: <span class="days"></span> Days, <span class="hours"></span>:<span class="minutes"></span>:<span class="seconds"></span></p>
					</div>
				</div>
			</div>

		<div class="msg wait"  style='display:none;'>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Please Wait...</h3>
				</div>
				<div class="panel-body">
					Currently waiting for <?php echo $is_teacher ? $student->name : $teacher->name; ?> to join
				</div>
			</div>

		</div>


		<div class="msg after"  style='display:none;'>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Thanks for attending</h3>
				</div>
				<div class="panel-body">
					<p class="message"></p>
				</div>
			</div>

			</div>
		<?php
	}
	include('footer.php');

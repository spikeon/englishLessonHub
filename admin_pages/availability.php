<?php

	if (!empty($_POST)) {
		$db->exec("DELETE FROM availability WHERE teacher_id = ".login_id());

		$sth = $db->prepare('INSERT INTO availability (teacher_id, day, start_hour, start_minute, end_hour, end_minute) VALUES (?,?,?,?,?,?)');
		$avails = [];
		foreach((array) $_POST['avails'] as $avail){
			list($avail['start_hour'], $avail['start_minute']) = explode(':',$avail['start']);
			list($avail['end_hour'], $avail['end_minute']) = explode(':',$avail['end']);
			$avails[] = $avail;
		}
		if(!empty($_POST['new'])){
		foreach((array) $_POST['new'] as $avail){
			list($avail['start_hour'], $avail['start_minute']) = explode(':',$avail['start']);
			list($avail['end_hour'], $avail['end_minute']) = explode(':',$avail['end']);
			$avails[] = $avail;
		}}
		foreach($avails as $avail) $sth->execute(array(login_id(), $avail['day'], $avail['start_hour'] * 1, $avail['start_minute'] * 1, $avail['end_hour'] * 1, $avail['end_minute'] * 1 ));

	}

	echo "<div class=\"well\"><ol><li>Create your availability by selecting \"Add New\", then pick the day of the week and your available times.</li><li>To delete a day’s availability simply \"Remove\"</li></ol><p>Once your availability has been set each day’s availability will repeat each subsequent week on your calendar.</p></div>";

	$teacher = get_teacher(login_id());
	echo "<form method='post'>
			<div  id='availabilityadmin'>
			<div class='row'>
				<div class='col-md-3'>Day of Week</div>
				<div class='col-md-3'>Start Time</div>
				<div class='col-md-3'>End Time</div>
				<div class='col-md-3'><span class='btn btn-primary newavail'>Add New</span></div>
			</div>
			<div class='row'><br></div>
			<div class='row availtemplate' style='display:none;'>
				<div class='col-md-3'>
					<div class='form-group'>
						<select class='form-control day'>
							<option value='0'>Monday</option>
							<option value='1'>Tuesday</option>
							<option value='2'>Wednesday</option>
							<option value='3'>Thursday</option>
							<option value='4'>Friday</option>
							<option value='5'>Saturday</option>
							<option value='6'>Sunday</option>
						</select>
					</div>
				</div>
				<div class='col-md-3'>
					<div class='form-group'>
						<input type='time' class='form-control start' name='' value=''>
					</div>
				</div>
				<div class='col-md-3'>
					<div class='form-group'>
						<input type='time' class='form-control end' name=''  value=''>
					</div>
				</div>
				<div class='col-md-3'>
						<span class='btn btn-danger removeavail'>Remove</span>
				</div>

			</div>

	";

	foreach($teacher->availability as $availability){

		echo "
			<div class='row'>
				<div class='col-md-3'>
					<div class='form-group'>
						<select name='avails[{$availability['id']}][day]' class='form-control day'>
							<option value='0' " . ($availability['day'] == 0 ? "SELECTED" : "") . ">Monday</option>
							<option value='1' " . ($availability['day'] == 1 ? "SELECTED" : "") . ">Tuesday</option>
							<option value='2' " . ($availability['day'] == 2 ? "SELECTED" : "") . ">Wednesday</option>
							<option value='3' " . ($availability['day'] == 3 ? "SELECTED" : "") . ">Thursday</option>
							<option value='4' " . ($availability['day'] == 4 ? "SELECTED" : "") . ">Friday</option>
							<option value='5' " . ($availability['day'] == 5 ? "SELECTED" : "") . ">Saturday</option>
							<option value='6' " . ($availability['day'] == 6 ? "SELECTED" : "") . ">Sunday</option>
						</select>
					</div>
				</div>
				<div class='col-md-3'>
					<div class='form-group'>
						<input type='time' class='form-control start' name='avails[{$availability['id']}][start]' value='".($availability['start_hour'] < 10 ? "0" : "")."{$availability['start_hour']}:".($availability['start_minute'] < 10 ? "0" : "")."{$availability['start_minute']}'>
					</div>
				</div>
				<div class='col-md-3'>
					<div class='form-group'>
						<input type='time' class='form-control end' name='avails[{$availability['id']}][end]'  value='".($availability['end_hour'] < 10 ? "0" : "")."{$availability['end_hour']}:".($availability['end_minute'] < 10 ? "0" : "")."{$availability['end_minute']}'>
					</div>
				</div>
				<div class='col-md-3'>
						<span class='btn btn-danger removeavail'>Remove</span>
				</div>

			</div>
		";
	}
	echo "</div>
			<div class='row'><br></div>

			<div class='row'>
				<div class='col-md-3'></div>
				<div class='col-md-3'></div>
				<div class='col-md-3'></div>
				<div class='col-md-3'><input type='submit' class='btn btn-primary' value='Update'></div>
			</div>
		</form>
	";

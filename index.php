<?php 
	include('header.php');
	$teachers = [];
	foreach ($db->query('SELECT id FROM teacher WHERE ban = 0') as $row) {
		$teacher = get_teacher($row['id']);

		if(login_type() == 'student'){
			$failed = false;

			foreach ((array)$teacher->classes as $c) {
				if ($c['student_id'] == login_id()) $failed = true;
			}
			$student = new student(login_id());
			if ($student->free_classes >= $billing_info['maxFreeClasses']) $failed = true;
			if($failed) $teacher->freeclass = 0;
		}
		$teacher->password = "";
		$teacher->email = "";
		$teacher->skype = "";
		$teacher->paypal = "";

		$teachers[] = $teacher;
	}
	echo '<script> var teachers = ' . json_encode($teachers) . ';</script>';
	echo '<div ng-app="home"><home-page></home-page></div>';
	include('footer.php');

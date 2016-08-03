<?php include('header.php'); ?>
<script>
	var teachers = <?php
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

		$teachers[] = $teacher;
	}
	echo json_encode($teachers);
	?>;
</script>

<div ng-app="home"><home-page></home-page></div>

<?php include('footer.php'); ?>
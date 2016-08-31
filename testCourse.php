<?php
	include('header.php');
	$class = new course(60);
	var_dump($class);
	$class->teacher->sendmail('test','test');
	include('footer.php');

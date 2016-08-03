<!DOCTYPE html>
<html>
<head>

<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
</head>
<body>
<?php
	function get_stars($employee_id){
		$db = new PDO('mysql:host=localhost;dbname=english;charset=utf8', 'root', 'm00c0wz88');
		$count = 0;
		$total = 0;
		$r = "";
		foreach($db->query('SELECT * FROM rate WHERE employee_id = '.$employee_id) as $row) {
			$count++;
			$total+=$row['rating'];
		}
		$result = $count == 0 ? 0 : round($total/$count);
		for($i=1;$i <= 5; $i++){
			if($i > $result) $r .= "<span class='glyphicon glyphicon-star-empty'></span>";
			else $r .= "<span class='glyphicon glyphicon-star'></span>";
		}
		return $r;
	}
	echo get_stars(1);
?>
</body>
</html>

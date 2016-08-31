<?php

	include('config.php');

	$kill_id = $_SESSION['purchase_id'];

	//$stmt = $db->prepare("UPDATE class SET status = 'canceled' WHERE id = {$kill_id} ORDER BY start_time ASC LIMIT 1");
	//$stmt->execute();

	$db->query("DELETE FROM class WHERE id = {$kill_id}");

	echo "<script> window.location = '{$base_url}'; </script>";

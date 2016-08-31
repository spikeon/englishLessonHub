<?php

	include('config.php');

	$kill_id = $_SESSION['purchase_id'];

	$db->query("DELETE FROM class WHERE id = {$kill_id}");

	echo "<script> window.location = '{$base_url}'; </script>";

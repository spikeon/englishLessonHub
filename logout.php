<?php
	session_start();
	session_destroy();
	setcookie("user", "", time() - 3600);
?>
<script>window.location = 'index.php';</script>
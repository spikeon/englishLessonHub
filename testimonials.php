<?php include('header.php'); ?>

	<h1>Testimonials</h1>

	<hr class="short-fat" />

<?php

foreach($db->query("SELECT * FROM testimonials") as $row){
	?>
		<p><b><i>"<?php echo $row['text']; ?>"</i></b></p>
		<br>
		<div class="text-right">
			<b style="color: #27749a;"><?php echo $row['name']; ?></b><br>
			<?php echo $row['job']; ?><br><br><hr>
		</div>
	<?php
}

?>

<?php include('footer.php'); ?>
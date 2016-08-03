</div>

<div class="footer">
	<div class="container">
		<div class="col-md-5  hidden-xs hidden-sm">
			<h2>About English Lesson Hub</h2>
			<p>
			<?php
			$name = 'about';
			$stmt2 = $db->prepare("SELECT * FROM pages WHERE name = ?");
			$stmt2->bindParam(1, $name);
			$stmt2->execute();
			$blackboard_data = $stmt2->fetch(PDO::FETCH_ASSOC);
			echo nl2br(substr(strip_tags($blackboard_data['content']), 0, 250));
			?>
			</p><br>
			<a href="about.php" class="btn btn-danger">Read More</a>
		</div>
		<div class="col-md-2"></div>
		<div class="col-md-5">
			<h2>What people are saying</h2>
			<?php
			foreach($db->query("SELECT * FROM testimonials ORDER BY RAND() LIMIT 1") as $row){
				?>
				<p><?php echo $row['text']; ?></p>
				<br>
				<b><?php echo $row['name']; ?></b><br>
				<i><?php echo $row['job']; ?></i><br><br>
				<?php
			}

			?>

			<a href="testimonials.php" class="btn btn-danger">Read More</a>
		</div>
	</div>

</div>
<div class="sub-footer">
	<div class="container">
		<div class="col-md-6  hidden-xs col-sm-6">
			<a href="privacy.php">Privacy Policy</a> / <a href="terms.php">Terms and Conditions</a>
		</div>

		<div class="col-md-6 col-sm-6 right">
			<a href="http://fivepints.com">Site by 5 Pints Productions</a>
		</div>
	</div>
</div>
</body>

</html>
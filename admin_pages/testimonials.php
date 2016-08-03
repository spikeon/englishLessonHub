<div class="row text-right" style="margin-bottom: 10px;">
	<div class="col-md-12">
		<a href="admin.php?page=testimonial" class="btn btn-primary">Add New</a>
	</div>
</div>
<?php

foreach($db->query("SELECT * FROM testimonials") as $row){
	?>
	<div class="well">
		<p><?php echo $row['text']; ?></p>
		<br>
		<b><?php echo $row['name']; ?></b><br>
		<i><?php echo $row['job']; ?></i><br><br>
		<div class="row text-right">
			<div class="col-md-12">
				<a href="admin.php?page=testimonial&delete=y&id=<?php echo $row['id']; ?>" class="btn btn-danger">Delete</a>
				<a href="admin.php?page=testimonial&id=<?php echo $row['id']; ?>" class="btn btn-primary">Edit</a>
			</div>
		</div>

	</div>
	<?php
}

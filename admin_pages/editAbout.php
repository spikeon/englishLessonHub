<?php
if(!empty($_POST)){

	$stmt2 = $db->prepare("UPDATE pages SET content = ? WHERE name = ?");
	$stmt2->bindParam(1, $content);
	$stmt2->bindParam(2, $name);

	$name = 'about';
	$content = $_POST['content'];

	$stmt2->execute();

}
?>

<form method="post">
	<textarea name="content" id="content" rows="20" cols="80">
		<?php
			$name = 'about';
			$stmt2 = $db->prepare("SELECT * FROM pages WHERE name = ?");
			$stmt2->bindParam(1, $name);
			$stmt2->execute();
			$blackboard_data = $stmt2->fetch(PDO::FETCH_ASSOC);
			echo $blackboard_data['content'];
		?>
	</textarea>
	<div class="row" style="margin-top: 10px;">
		<div class="col-md-6"><a href="admin.php" class="btn btn-primary">Back to Dashboard</a></div>
		<div class="col-md-6 text-right"><input type="submit" class="btn btn-primary" value="Update"></div>

	</div>
</form>
<script>
	CKEDITOR.replace( 'content' ,{
		filebrowserUploadUrl: "upload.php",
		imageUploadUrl: '/upload.php?type=Images',
	} );
</script>
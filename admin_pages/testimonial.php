<?php
	if(!empty($_GET['delete'])){
		$stmt = $db->prepare("DELETE FROM testimonials WHERE id = ?");
		$stmt->bindParam(1, $id);
		$id = $_GET['id'];
		$stmt->execute();
		echo "<script>window.location = 'admin.php?page=testimonials'; </script>";
	}
	else{
		$content = ['name' => '', 'text' => '', 'job' => ''];
		if(!empty($_POST)){
			if(!empty($_GET['id'])) {
				$stmt = $db->prepare("UPDATE testimonials SET name = ?, text = ?, job = ? WHERE id = ?");
				$stmt->bindParam(1, $name);
				$stmt->bindParam(2, $text);
				$stmt->bindParam(3, $job);
				$stmt->bindParam(4, $id);
				$id = $_GET['id'];
				$name = $_POST['name'];
				$text = $_POST['text'];
				$job = $_POST['job'];
				$stmt->execute();
				echo "<script>window.location = 'admin.php?page=testimonials'; </script>";
			}
			else{
				$stmt = $db->prepare("INSERT INTO testimonials(name, text, job) VALUES (?,?,?)");
				$stmt->bindParam(1, $name);
				$stmt->bindParam(2, $text);
				$stmt->bindParam(3, $job);
				$name = $_POST['name'];
				$text = $_POST['text'];
				$job = $_POST['job'];
				$stmt->execute();
				echo "<script>window.location = 'admin.php?page=testimonials'; </script>";
			}

		}
		if(!empty($_GET['id'])){
			$stmt = $db->prepare("SELECT * FROM testimonials WHERE id = ?");
			$stmt->bindParam(1, $id);
			$id = $_GET['id'];
			$stmt->execute();
			$content = $stmt->fetch(PDO::FETCH_ASSOC);
		}
		?>
			<form method="post"  data-enable-shim="true">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label class="sr-only" for="text">Review</label>
							<textarea class="form-control" id="text" rows="3" placeholder="Review" name="text" required><?php echo $content['text'];?></textarea>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="sr-only" for="name">Name</label>
							<input type="text" class="form-control" id="name" name="name" placeholder="Name" value="<?php echo addcslashes($content['name'],'"');?>" required>
						</div>
					</div>
					<div class="col-md-6">
						<label class="sr-only" for="job">Job / Position</label>
						<input type="text" class="form-control" id="job" name="job" placeholder="Job / Position"  value="<?php echo addcslashes($content['job'], '"');?>" required>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12 text-right"><input type="submit" value="Save" class="btn btn-primary"></div>
				</div>
			</form>
		<?php


	}

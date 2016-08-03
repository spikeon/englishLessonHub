<?php
	if(login_type() == 'admin'){
		if(!empty($_POST['ban_teacher'])) $db->prepare("UPDATE teacher SET ban = 1 WHERE id = {$_POST['ban_teacher']}")->execute();
		if(!empty($_POST['ban_student'])) $db->prepare("UPDATE student SET ban = 1 WHERE id = {$_POST['ban_student']}")->execute();
		if(!empty($_POST['unban_teacher'])) $db->prepare("UPDATE teacher SET ban = 0 WHERE id = {$_POST['unban_teacher']}")->execute();
		if(!empty($_POST['unban_student'])) $db->prepare("UPDATE student SET ban = 0 WHERE id = {$_POST['unban_student']}")->execute();
		?>
		<div>

			<!-- Nav tabs -->
			<ul class="nav nav-tabs nav-justified" role="tablist">
				<li role="presentation" class="active"><a href="#students" aria-controls="students" role="tab" data-toggle="tab">Students</a></li>
				<li role="presentation"><a href="#teachers" aria-controls="teachers" role="tab" data-toggle="tab">Teachers</a></li>
			</ul>

			<!-- Tab panes -->
			<div class="tab-content">
				<div role="tabpanel" class="tab-pane active" id="students">
					<h3>Students</h3>
					<table class="table table-hover">
						<thead>
							<tr>
								<th>Name</th><th>Email</th><th>Username</th><td></td>
							</tr>
						</thead>
						<tbody>
							<?php
								foreach ($db->query('SELECT id FROM student WHERE ban = 0') as $row) {
									$s = new student($row['id']);
									echo "<tr><td>{$s->name}</td><td>{$s->email}</td><td>{$s->username}</td><td><form method='post'><input type='hidden' name='ban_student' value='{$s->id}'><input type='submit' class='btn btn-danger' value='ban'></form></td></tr>";
								}
							?>
						</tbody>
					</table>
					<h3>Banned Students</h3>
					<table class="table table-hover">
						<thead>
						<tr>
							<th>Name</th><th>Email</th><th>Username</th><td></td>
						</tr>
						</thead>
						<tbody>
						<?php
						foreach ($db->query('SELECT id FROM student WHERE ban = 1') as $row) {
							$s = new student($row['id']);
							echo "<tr><td>{$s->name}</td><td>{$s->email}</td><td>{$s->username}</td><td><form method='post'><input type='hidden' name='unban_student' value='{$s->id}'><input type='submit' class='btn btn-danger' value='unban'></form></td></tr>";
						}
						?>
						</tbody>
					</table>

				</div>
				<div role="tabpanel" class="tab-pane" id="teachers">
					<h3>Teachers</h3>
					<table class="table table-hover">
						<thead>
							<tr>
								<th>Name</th><th>Email</th><th>Username</th><td></td>
							</tr>
						</thead>
						<tbody>
							<?php
								foreach ($db->query('SELECT id FROM teacher WHERE ban = 0') as $row) {
									$t = new teacher($row['id']);
									echo "<tr><td>{$t->name}</td><td>{$t->email}</td><td>{$t->username}</td><td><form method='post'><input type='hidden' name='ban_teacher' value='{$t->id}'><input type='submit' class='btn btn-danger' value='ban'></form></td></tr>";
								}
							?>
						</tbody>
					</table>
					<h3>Banned Teachers</h3>
					<table class="table table-hover">
						<thead>
						<tr>
							<th>Name</th><th>Email</th><th>Username</th><td></td>
						</tr>
						</thead>
						<tbody>
						<?php
						foreach ($db->query('SELECT id FROM teacher WHERE ban = 1') as $row) {
							$t = new teacher($row['id']);
							echo "<tr><td>{$t->name}</td><td>{$t->email}</td><td>{$t->username}</td><td><form method='post'><input type='hidden' name='unban_teacher' value='{$t->id}'><input type='submit' class='btn btn-danger' value='unban'></form></td></tr>";
						}
						?>
						</tbody>
					</table>

				</div>
			</div>

		</div>
		<script>
			$('#nav-tabs a').click(function (e) {
				e.preventDefault()
				$(this).tab('show')
			})
		</script>
		<?

	}
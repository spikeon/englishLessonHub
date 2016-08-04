<?php include('header.php'); ?>
<?php

	$teacher = get_teacher($_GET['id']);

	if($teacher->ban){
		echo "This teacher has been banned";
	}
	else {
		$teacher->password = "";
		$teacher->email = "";
		$teacher->skype = "";
		$teacher->paypal = "";

		?>
		<script>
			<?php echo "var teacher = ". json_encode($teacher).";"; ?>

			function compare_start_time(a, b) {
				if (a.start_time < b.start_time)
					return -1;
				else if (a.start_time > b.start_time)
					return 1;
				else
					return 0;
			}

			var day_times = {};
			var events_list = [];

			$(document).ready(function () {
				var avail_start = new Date(new Date().getTime() + 48 * 60 * 60 * 1000);
				var avail_end = new Date(avail_start.getFullYear(), avail_start.getMonth() + 2, avail_start.getDate());
				events_list = [
					{
						start: '2000-01-01',
						end: avail_start.getFullYear() + '-' + (avail_start.getMonth() <= 8 ? "0" : "") + (avail_start.getMonth() + 1) + '-' + (avail_start.getDate() <= 8 ? "0" : "") + (avail_start.getDate() + 1),
						overlap: false,
						rendering: 'background',
						color: '#ff9f89',
						className: 'no-classes',
						description: ''
					},
					{
						start: avail_start.getFullYear() + '-' + ( (avail_start.getMonth() + 2) <= 8 ? "0" : "") + (avail_start.getMonth() + 3) + '-' + (avail_start.getDate() <= 8 ? "0" : "") + (avail_start.getDate() + 1),
						end: (avail_start.getFullYear() + 5) + '-01-01',
						overlap: false,
						rendering: 'background',
						color: '#ff9f89',
						className: 'no-classes',
						description: ''
					},
				];
				var day_msec = 24 * 60 * 60 * 1000;

				for (var i = avail_start.getTime(); i <= avail_end.getTime(); i += day_msec) {
					day_times[i] = [];
					var current_start = i;
					var current_end = (i + day_msec);
					var current_date = new Date(current_start);
					var chunks = [];
					var step = teacher.duration * 60 * 1000;


					$.each(teacher.availability, function (k, available) {
						if (available.day == current_date.getDay()) {
							// There are times available on this day
							var removals = [];
							var avail_start_date = new Date(current_date.getFullYear(), current_date.getMonth(), current_date.getDate(), available.start_hour, available.start_minute, 0);
							var avail_end_date = new Date(current_date.getFullYear(), current_date.getMonth(), current_date.getDate(), available.end_hour, available.end_minute, 0);
							$.each(teacher.classes, function (k2, course) {
								if (course.start_time >= avail_start_date.getTime() && course.start_time <= avail_end_date.getTime()) {
									// There are courses in this availablility chunk
									removals.push({start_time: course.start_time, end_time: course.end_time});
								}
							});

							var current_start = avail_start_date.getTime();
							var current_end = avail_end_date.getTime();
							if (removals.length > 0) {
								removals.sort(compare_start_time);
								$.each(removals, function (k3, removal) {
									if (removal.start_time - current_start >= step) chunks.push({
										start_time: current_start * 1,
										end_time: removal.start_time * 1
									});
									current_start = removal.end_time;
								});

								if (current_end - current_start >= step) chunks.push({
									start_time: current_start * 1,
									end_time: current_end * 1
								});
							}
							else {
								if (avail_end_date.getTime() - avail_start_date.getTime() > step) chunks.push({
									start_time: avail_start_date.getTime() * 1,
									end_time: avail_end_date.getTime() * 1
								});
							}
						}
					});
					chunks.sort(compare_start_time);

					$.each(chunks, function (k4, chunk) {

						for (var i2 = chunk.start_time; i2 <= chunk.end_time; i2 += step) {

							if (i2 + step <= chunk.end_time) day_times[i].push(i2);
						}
					});
				}

				$.each(day_times, function (day, times) {
					day = day * 1;
					var day_date = new Date(day);

					if (times.length > 0) {

						var temp = {
							start: 	day_date.getFullYear() + '-' + ( day_date.getMonth() <= 8 ? "0" : "") + (day_date.getMonth() + 1) + '-' + (day_date.getDate() <= 9 ? "0" : "") + (day_date.getDate()),
							end: 	day_date.getFullYear() + '-' + ( day_date.getMonth() <= 8 ? "0" : "") + (day_date.getMonth() + 1) + '-' + (day_date.getDate() <= 8 ? "0" : "") + (day_date.getDate() + 1),
							overlap: false,
							rendering: 'background',
							color: '#89E894',
							className: 'classes-here',
							description: day
						};

						events_list.push(temp);
					}
				});


				$('#calendar').fullCalendar({
					header: {
						left: 'title',
						center: '',
						right: 'today prev,next'
					},

					dayClick: function (date, jsEvent, view) {

					},
					eventAfterAllRender: function () {
						var $modal = $('#myModal');
						var $content = $('#myModal .modal-body .times');
						$('.fc-bgevent').click(function () {
							if ($(this).hasClass('classes-here')) {
								$modal.modal('show');
								$content.html("");
								var current_times = day_times[$(this).attr('day')];
								$.each(current_times, function (k, v) {
									var time = new Date(v * 1);
									var hours = time.getHours();
									var ampm = '';
									if (hours > 12) {
										hours -= 12;
										ampm = 'pm';
									} else if (hours === 0) {
										hours = 12;
										ampm = 'am';
									}
									else {
										ampm = 'am';
									}
									$content.append($("<div class='col-md-4'></div>").append($("<div class='btn btn-primary' style='margin-bottom: 10px;' date='" + time.getTime() + "'>" + hours + ":" + (time.getMinutes() < 9 ? "0" : "") + time.getMinutes() + " " + ampm + "</div>").click(function () {
										window.location = "book.php?teacher=<?php echo $teacher->id; ?>&time=" + $(this).attr('date');
									})));

								});
								$content.append($("<div class='col-md-12' style='font-style: italic;'></div>").text("All times shown on this site are adjusted to your time zone."));
							}

						});
					},

					eventRender: function (event, element) {
						$(element).attr('day', event.description);

					},
					events: events_list,
				});
			});
		</script>

		<div class="teacher">

			<div class="row">
				<div class="col-md-2 col-xs-6">
					<img src="<?php echo $teacher->thumb; ?>">
				</div>
				<div class="col-md-8 hidden-xs">
					<a href="teacher.php?id=<?php echo $teacher->id; ?>"
					   class="name"><?php echo $teacher->name; ?></a><br>
					<b>Method: </b> <?php echo $teacher->method; ?><br>
					<?php echo $teacher->country; ?><br>
					<?php echo $teacher->stars; ?><br>

					<p><?php echo $teacher->description; ?></p>

				</div>
				<div class="col-md-2 col-xs-6">
					<div class="hidden-s hidden-md hidden-lg hidden-xlg">
						<a href="teacher.php?id=<?php echo $teacher->id; ?>"
						   class="name"><?php echo $teacher->name; ?></a><br>
						<?php echo $teacher->stars; ?><br>
					</div>

					<div class="ball" style="position: relative;">
						<div class="background"></div>
						<div class="cost">
							<span class="unit">&euro;</span>
							<span class="price1"><?php echo $teacher->price1; ?></span>
							<span class="price2"><?php echo $teacher->price2; ?></span>
						</div>
						<div class="time"><?php echo $teacher->duration; ?> Min</div>
					</div>
				</div>
			</div>
			<div class="row hidden-sm hidden-md hidden-lg hidden-xlg">
				<div class="col-md-12">
					<b>Method: </b> <?php echo $teacher->method; ?><br>
					<?php echo $teacher->country; ?><br>

					<p><?php echo $teacher->description; ?></p>
				</div>
			</div>
		</div>


		<div id='calendar'></div>

		<!-- Modal -->
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
								aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel">Book Class</h4>
					</div>
					<div class="modal-body">

						<div class="row times"></div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>

		<?php
	}

?>
<?php include('footer.php'); ?>

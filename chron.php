<?php
	/**
	 * Chron Job - Will run on the hour every hour and clean up
	 * booked appointments that were started over an hour ago but
	 * have not been paid for
	 */

	// Get all the things
	include('config.php');
	$removed = 0;
	$errors = 0;
	// Get all rows
	foreach($db->query("SELECT * FROM class") as $c){
		// check to see if it's a row we want to delete
		if($c['status'] == "init" && $c['time'] < (time() - 3600) ){
			// Delete the row
			if( $db->query ("DELETE FROM class WHERE id = {$c['id']}")  !== TRUE) $errors++;
			else $removed++;
		}
	}
	$chron_log->lwrite("Removed {$removed} bad bookings with {$errors} errors.");

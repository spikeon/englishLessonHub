<?php
	use PayPal\Api\Amount;
	use PayPal\Api\Refund;
	use PayPal\Api\Sale;
	use PayPal\Rest\ApiContext;
	use PayPal\Auth\OAuthTokenCredential;

	$query = $_GET['query'];

	include('config.php');

	$r = [];


	switch($query){
		case "unique" :
			$type = $_GET['type'];
			$value = $_GET['value'];
			$r['unique'] = true;

			if($type == 'username') {
				$sql1 = "SELECT id FROM teacher WHERE username = ?";
				$sql2 = "SELECT id FROM student WHERE username = ?";
			}
			else if($type == 'email') {
				$sql1 = "SELECT id FROM teacher WHERE email = ?";
				$sql2 = "SELECT id FROM student WHERE email = ?";
			}

			$stmt = $db->prepare($sql1);
			$stmt->bindParam(1, $value);
			$stmt->execute();
			if($stmt->rowCount() != 0) $r['unique'] = false;

			$stmt = $db->prepare($sql2);
			$stmt->bindParam(1, $value);
			$stmt->execute();
			if($stmt->rowCount() != 0) $r['unique'] = false;

			break;
		case "refund":

			$teacher_id = $_GET['tid'];
			$student_id = $_GET['sid'];
			$course_id = $_GET['cid'];
			$refund_id = $_GET['txnid'];
			$msg = $_GET['msg'];
			$to = $_GET['to'];
			$course = new course($course_id);
			if($msg){
				$email = "";
				if($to == 'teacher') {
					$course->teacher->send_mail("Course Canceled", "<p>Dear Teacher,</p><p>Your class with {$course->student->name} on {$course->formatted_date} has been canceled.</p><p>Please try to keep cancellations to a minimum, as cancellations may reflect on your ratings. </p>");
					$course->student->send_mail("Course Canceled", "<p>Dear Student,</p><p>You have cancelled your lesson with {$course->teacher->name} for {$course->formatted_date}.  You will not be charged for this lesson because you cancelled 24 hours before.  And don't give up on your English studies.  The longer you wait between classes, the harder it is to begin again.  Book another lesson now, it's for your future!</p>");
				} else {
					$course->teacher->send_mail("Course Canceled", "<p>Dear Teacher,</p><p>Your class with {$course->student->name} on {$course->formatted_date} has been canceled.</p>");
					$course->student->send_mail("Course Canceled", "<p>Dear Student,</p><p>We're sorry to inform you that {$course->teacher->name} has had to cancel for the lesson on {$course->formatted_date}.  Sometimes these things happen.  Book another lesson soon.  Gaps between lessons cause gaps in your knowledge of English!</p><p>Reason for cancelation: {$msg}</p>");
				}
			}

			if(!$refund_id){
				$paypal_log->lwrite("Free 'Refund' Processed.  Course id: {$course_id}.");
				$c = $db->prepare("UPDATE class SET status = 'Refunded' WHERE id = {$course_id}");
				$c->execute();
			}
			else {

				$postfields = array(
					'USER' => urlencode($pp_config['acct1.UserName']),
					'PWD' => urlencode($pp_config['acct1.Password']),
					'SIGNATURE' => urlencode($pp_config['acct1.Signature']),
					'METHOD' => urlencode('RefundTransaction'),
					'VERSION' => urlencode(94),
					'TRANSACTIONID' => urlencode($refund_id),
					'REFUNDTYPE' => urlencode('Full')
				);


				try {
					$ch = curl_init();
					if (FALSE === $ch) throw new Exception('failed to initialize');

					curl_setopt($ch, CURLOPT_URL, 'https://api-3t.paypal.com/nvp');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
					curl_setopt($ch, CURLOPT_SSLVERSION, 6);
					$content = curl_exec($ch);
					if (FALSE === $content) throw new Exception(curl_error($ch), curl_errno($ch));

					else {
						$paypal_log->lwrite("Refund Processed.  Course id: {$course_id}.");
						$c = $db->prepare("UPDATE class SET status = 'Refunded' WHERE id = {$course_id}");
						$c->execute();
					}

				} catch (Exception $e) {
					$msg = sprintf('Curl failed with error #%d: %s', $e->getCode(), $e->getMessage());
					$paypal_log->lwrite("Error [{$course_id}]:{$msg}");
					trigger_error($msg, E_USER_ERROR);
				}
			}
			break;

		case "teacher_missed":
			$course_id = $_GET['cid'];
			$course->teacher->send_mail("You missed your class", "<p>Dear teacher,</p><p>You have missed your class with {$course->student->name} for {$course->formatted_date}.  We regret to inform you that the lesson wasn't cancelled within 24 hours of the lesson and you have received a rating of 0 and you have not received the payment for this course.  Please try and cancel a minimum of 24 hours ahead in order to avoid any further unnecessary charges in the future.</p><p><a href='".BASE_URL."/terms.php'>Terms and Conditions</a></p>");
			$course->student->send_mail("The teacher missed your class", "<p>Dear student,</p><p>We appologize for {$course->student->name} missing your class on {$course->formatted_date}.  We have processed your refund.  Have a great day.</p><p><a href='".BASE_URL."/terms.php'>Terms and Conditions</a></p>");
			break;

		case "student_missed":
			$course_id = $_GET['cid'];
			$course = new course($course_id);
			$course->student->send_mail("You missed your class", "<p>Dear student,</p><p>You have missed your class with {$course->teacher->name} for {$course->formatted_date}.  We regret to inform you that the lesson wasn't cancelled within 24 hours of the lesson and your account has been charged.  Please try and cancel a minimum of 24 hours ahead in order to avoid any further unnecessary charges in the future.</p><p><a href='".BASE_URL."/terms.php'>Terms and Conditions</a></p>");
			break;

		case "teacher_attended":
			$course_id = $_GET['cid'];
			$course->teacher->send_mail("Thanks for attending your class", "<p>Dear teacher,</p><pThank you for using ELH.  We hope your lesson has gone well and we expect to have more students in the future.  We are currently promoting the site and hope to see an influx of students in the near future.  Always remember to provide your best performance and maximum professionalism, as better ratings will attract more students.  </p>");
			break;

		case "student_attended":
			$course_id = $_GET['cid'];
			$course = new course($course_id);
			$course->student->send_mail("Thanks for attending your class", "<p>Dear student,</p><p>Thank you for using ELH!  We are glad to see that you have begun your learning experience with us and hope it has been a good one.</p>");
			break;


		case "payout":

			$teacher_id = $_GET['tid'];
			$course_id = $_GET['cid'];

			$teacher = new teacher($teacher_id);

			$payouts = new \PayPal\Api\Payout();

			$senderBatchHeader = new \PayPal\Api\PayoutSenderBatchHeader();
			$senderBatchHeader->setSenderBatchId(uniqid())->setEmailSubject("You have a Payment!");

			$senderItem = new \PayPal\Api\PayoutItem();
			$senderItem->setRecipientType('Email')
				->setNote('Thanks for your patronage!')
				->setReceiver($teacher->email)
				->setSenderItemId($course_id)
				->setAmount(new \PayPal\Api\Currency('{
                        "value":"'.$teacher->payment.'",
                        "currency":"EUR"
                    }'));

			$payouts->setSenderBatchHeader($senderBatchHeader)->addItem($senderItem);

			$request = clone $payouts;

			try {
				$output = $payouts->createSynchronous($apiContext);
				$paypal_log->lwrite("Teacher Paid.  Course id: {$course_id}.");
				$c = $db->prepare("UPDATE class SET status = 'PaidOut' WHERE id = {$course_id}");
				$c->execute();
			} catch (Exception $ex) {
				$paypal_log("Teacher Payout Error.  Course id: {$course_id}. {$ex->getMessage()}");
				exit(1);
			}
			break;

		case "check_in" :
			$user_id = $_GET['uid'];
			$course_id = $_GET['cid'];
			$type = $_GET['type'];

			$sth = $db->prepare("SELECT * FROM checkins WHERE order_id = ? AND teacher_id = ? AND is_teacher = ?");
			$sth->execute(array($course_id, $user_id,($type == "teacher" ? 1 : 0)));
			$result = $sth->fetchAll();

			if(empty($result)) {
				$sth2 = $db->prepare("INSERT INTO `checkins` (`order_id`, `teacher_id`, `is_teacher`) VALUES ( ? , ? , ? ) ");
				$sth2->bindParam(1, $course_id);
				$sth2->bindParam(2, $user_id);
				$sth2->bindParam(3, $type_i);

				$user_id = $_GET['uid'] * 1;
				$course_id = $_GET['cid'] * 1;
				$type_i = ($type == "teacher" ? 1 : 0);


				$sth2->execute();

			}

			break;
		case "alone" :
			$user_id = $_GET['uid'];
			$course_id = $_GET['cid'];
			$type = $_GET['type'];

			$partner = $db->query("SELECT * FROM checkins WHERE order_id = '{$course_id}' AND teacher_id = '{$user_id}' AND is_teacher = '".($type == "teacher" ? 0 : 1)."'")->rowCount();
			$r['alone'] = $partner == 0;
			break;
		case "get_blackboard" :
			$course_id = $_GET['cid'];
			$lastchange = $_GET['lastchange'];
			$blackboard = $db->query("SELECT * FROM blackboard WHERE order_id = '{$course_id}'")->fetch(PDO::FETCH_ASSOC);
			$blackboard['timestamp'] = strtotime($blackboard['timestamp']);
			if($blackboard['timestamp'] > $lastchange) $r = ['changed' => true, 'data' => $blackboard];
			else $r['changed'] = false;
			break;
		case "set_blackboard":

			if($db->query("SELECT * FROM blackboard WHERE order_id = '{$_POST['cid']}'")->rowCount() == 0){
				$stmt = $db->prepare("INSERT INTO blackboard (order_id, content) VALUES (?, ?)");
			}
			else{
				$stmt = $db->prepare("UPDATE blackboard SET order_id = ? , content = ? WHERE order_id = {$_POST['cid']}");
			}


			$stmt->bindParam(1, $course_id);
			$stmt->bindParam(2, $data);
			$course_id = $_POST['cid'];
			$data = $_POST['data'];
			$stmt->execute();
			break;
		case "rate":
			$stmt = $db->prepare("INSERT INTO rate (rating, teacher_id, order_id) VALUES (?, ?, ?)");
			$stmt->bindParam(1, $rating);
			$stmt->bindParam(2, $user_id);
			$stmt->bindParam(3, $course_id);
			$user_id = $_GET['uid'];
			$course_id = $_GET['cid'];
			$rating = $_GET['rating'];
			$stmt->execute();
			$r['rated'] = true;
			break;
		default:

			break;
	}

	echo json_encode($r);
	exit();

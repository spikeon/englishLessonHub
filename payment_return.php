<?php
use PayPal\CoreComponentTypes\BasicAmountType;
use PayPal\EBLBaseComponents\DoExpressCheckoutPaymentRequestDetailsType;
use PayPal\EBLBaseComponents\PaymentDetailsType;
use PayPal\EBLBaseComponents\PaymentDetailsItemType;
use PayPal\PayPalAPI\DoExpressCheckoutPaymentReq;
use PayPal\PayPalAPI\DoExpressCheckoutPaymentRequestType;
use PayPal\PayPalAPI\GetExpressCheckoutDetailsReq;
use PayPal\PayPalAPI\GetExpressCheckoutDetailsRequestType;
use PayPal\Service\PayPalAPIInterfaceServiceService;

	include('header.php');

	$paypalService = new PayPalAPIInterfaceServiceService($pp_config);
	$getExpressCheckoutDetailsRequest = new GetExpressCheckoutDetailsRequestType($_GET['token']);
	$getExpressCheckoutDetailsRequest->Version = '104.0';
	$getExpressCheckoutReq = new GetExpressCheckoutDetailsReq();
	$getExpressCheckoutReq->GetExpressCheckoutDetailsRequest = $getExpressCheckoutDetailsRequest;

	$getECResponse = $paypalService->GetExpressCheckoutDetails($getExpressCheckoutReq);

	$class_id = $getECResponse->GetExpressCheckoutDetailsResponseDetails->PaymentDetails[0]->PaymentDetailsItem[0]->Number;

	$c = $db->prepare("UPDATE class SET status = 'pending' WHERE id = {$class_id}");
	$c->execute();

	$st = $db->prepare('INSERT INTO paypal_log( response ) VALUES( :response )');
	$st->execute(array(':response' => serialize($getECResponse)));

	$paypalService = new PayPalAPIInterfaceServiceService($pp_config);
	$paymentDetails= $getECResponse->GetExpressCheckoutDetailsResponseDetails->PaymentDetails[0];

	$DoECRequestDetails = new DoExpressCheckoutPaymentRequestDetailsType();
	$DoECRequestDetails->PayerID = $_GET['PayerID'];
	$DoECRequestDetails->Token = $_GET['token'];
	$DoECRequestDetails->PaymentDetails[0] = $paymentDetails;

	$DoECRequest = new DoExpressCheckoutPaymentRequestType();
	$DoECRequest->DoExpressCheckoutPaymentRequestDetails = $DoECRequestDetails;
	$DoECRequest->Version = '104.0';

	$DoECReq = new DoExpressCheckoutPaymentReq();
	$DoECReq->DoExpressCheckoutPaymentRequest = $DoECRequest;

	$DoECResponse = $paypalService->DoExpressCheckoutPayment($DoECReq);

	$st = $db->prepare('INSERT INTO paypal_log( response ) VALUES( :response )');
	$st->execute(array(':response' => serialize($DoECResponse)));

	$course = new course($class_id);

	$course->student->send_mail("ELH Class Booked", "<p>Dear Student,</p><p>Thank you for booking with ELH.  Your class is scheduled with {$course->teacher->name} for {$course->formatted_date} at {$course->formatted_time}. (<a href='".BASE_URL."/login.php'>Login</a> to view class time in your time zone.)  Please be present in the <a href='".BASE_URL."/appointment.php'>Blackboard</a> at that time and date. If you must cancel, please cancel within 24 hours of your lesson or your account will be charged.</p><p><a href='".BASE_URL."/terms.php'>Terms of Use</a></p>");
	$course->teacher->send_mail("ELH Class Booked", "<p>Dear Teacher,</p><p>Thank you for listing with ELH.  Your class is scheduled with {$course->student->name} for {$course->formatted_date} at {$course->formatted_time}. (<a href='".BASE_URL."/login.php'>Login</a> to view class time in your time zone.)  Please be present in the <a href='".BASE_URL."/appointment.php'>Blackboard</a> at that time and date. If you must cancel, please cancel within 24 hours of your lesson or your account will be charged.</p><p><a href='".BASE_URL."/terms.php'>Terms of Use</a></p>");

	?>
		<div class="alert alert-success">You are now signed up for the class</div>
	<?php

	include('footer.php');

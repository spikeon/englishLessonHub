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


	$c = $db->prepare("UPDATE class SET status = 'pending' WHERE id = {$getECResponse->GetExpressCheckoutDetailsResponseDetails->PaymentDetails[0]->PaymentDetailsItem[0]->Number}");
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


	?>

		<div class="alert alert-success">You are now signed up for the class</div>
	<?php

	include('footer.php');



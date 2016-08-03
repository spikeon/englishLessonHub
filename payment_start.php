<?php
use PayPal\CoreComponentTypes\BasicAmountType;
use PayPal\EBLBaseComponents\AddressType;
use PayPal\EBLBaseComponents\BillingAgreementDetailsType;
use PayPal\EBLBaseComponents\PaymentDetailsItemType;
use PayPal\EBLBaseComponents\PaymentDetailsType;
use PayPal\EBLBaseComponents\SetExpressCheckoutRequestDetailsType;
use PayPal\PayPalAPI\SetExpressCheckoutReq;
use PayPal\PayPalAPI\SetExpressCheckoutRequestType;
use PayPal\Service\PayPalAPIInterfaceServiceService;

	include('config.php');

	$_SESSION['purchase_id'] = $_GET['id'];
	$_SESSION['purchase_price'] = $_GET['price'];

	$paypalService = new PayPalAPIInterfaceServiceService($pp_config);
	$paymentDetails= new PaymentDetailsType();

	$itemDetails = new PaymentDetailsItemType();
	$itemDetails->Name = 'Course';
	$itemDetails->Number = $_GET['id'];
	$itemAmount = $_GET['price'];
	$itemDetails->Amount = $itemAmount;
	$itemQuantity = '1';
	$itemDetails->Quantity = $itemQuantity;

	$paymentDetails->PaymentDetailsItem[0] = $itemDetails;

	$orderTotal = new BasicAmountType();
	$orderTotal->currencyID = 'EUR';
	$orderTotal->value = $itemAmount * $itemQuantity;

	$paymentDetails->OrderTotal = $orderTotal;
	$paymentDetails->PaymentAction = 'Sale';
	$paymentDetails->NotifyURL = BASE_URL . '/IPN.php';

	$setECReqDetails = new SetExpressCheckoutRequestDetailsType();
	$setECReqDetails->PaymentDetails[0] = $paymentDetails;
	$setECReqDetails->CancelURL = BASE_URL . '/payment_cancel.php';
	$setECReqDetails->ReturnURL = BASE_URL . '/payment_return.php';

	$setECReqType = new SetExpressCheckoutRequestType();
	$setECReqType->Version = '104.0';
	$setECReqType->SetExpressCheckoutRequestDetails = $setECReqDetails;

	$setECReq = new SetExpressCheckoutReq();
	$setECReq->SetExpressCheckoutRequest = $setECReqType;

	$setECResponse = $paypalService->SetExpressCheckout($setECReq);

	$st = $db->prepare('INSERT INTO paypal_log( response ) VALUES( :response )');
	$st->execute( array( ':response' => serialize($setECResponse) ) );

	if($setECResponse->Ack == "Success") echo "<script> window.location = \"https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token={$setECResponse->Token}\"; </script>";
	else {
		var_dump($setECResponse);
		die();
	}
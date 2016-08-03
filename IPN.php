<?php
use PayPal\IPN\PPIPNMessage;

require_once('config.php');

$ipnMessage = new PPIPNMessage(null, $pp_config);
$r = "";
$order = false;
$status = false;
$id = false;

foreach($ipnMessage->getRawData() as $key => $value) {

	$r .= "IPN: $key => $value\r\n";

	if($key == 'item_number1') $order = $value;

	if($key == 'payment_status') $status = $value;

	if($key == 'txn_id') $id = $value;

}


if($ipnMessage->validate()) {

	$r .= "Success: Got valid IPN data";

	$c = $db->prepare("UPDATE class SET status = '{$status}', paypal = '{$id}' WHERE id = {$order}");

	$c->execute();

} else { $r .= "Error: Got invalid IPN data"; }

file_put_contents('ipn_log.txt', $r);
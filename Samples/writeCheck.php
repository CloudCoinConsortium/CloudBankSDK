<?php

require __DIR__ . "CloudBank/vendor/autoload.php";

use CloudBank\CloudBank;
use CloudBank\CloudBankException;

try {
	$cBank = new CloudBank([
		"url" => 'https://bank.cloudcoin.global/service',
		"privateKey" => "1DECE3AF-43EC-435B-8C39-E2A5D0EA8677"
	]);

	$amount = 250;
	$checkId = "8345AD";
	$emailTo = "buyer@user.com";
	$payerTo = "John Smit";
	$emailFrom = "shop@domain.com";
	$payeeFrom = "Shop LLC";
	$memo = "Description#1";
	
	$writeCheckResponse = $cBank->writeCheck($amount, $checkId, $emailTo, $payerTo, $emailFrom, $payeeFrom, $memo);
	if ($writeCheckResponse->isError()) {
		die("Failed to send a check");
	}

	$url = $cBank->getCashCheckURL($checkId, "json", "buyer@domain.com");
	
	echo "Click here to see the check <a href=\"$url\">$url</a>\n";

} catch (CloudBankException $e) {
	echo "Error[" . $e->getCode() . "]: " . $e->getMessage() . "\n";
	exit(1);

}

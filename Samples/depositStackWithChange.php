<?php

require __DIR__ . "/CloudBank/vendor/autoload.php";

use CloudBank\CloudBank;
use CloudBank\CloudBankException;

// Update receipt status in the Database
try {
	$cBank = new CloudBank([
		"url" => 'https://bank.cloudcoin.global/service',
		"privateKey" => "00000000000000000000000000000000",
                "account" => "CloudCoin@Protonmail.com",
                "debug" => "true"
	]);

	echo "CloudBank Version: " . $cBank->getVersion() . "\n";
	// Check RAIDA
	$echoResponse = $cBank->echoRAIDA();
	if ($echoResponse->status == "ready") {

		$stack = file_get_contents("/tmp/stack-1530937884.json");
		if (!$stack)
			die("Can not get stack file");

		$depositResponse = $cBank->depositStack($stack, 251);
		if ($depositResponse->isError()) 
			die("Failed to import stack");
		
		echo $depositResponse->message . "\n";

		$change = $depositResponse->change;
		file_put_contents("/tmp/change.stack", $change);

	} else {
		echo "RAIDA is not ready. Plese try again later\n";
		exit(0);
	}


} catch (CloudBankException $e) {
	echo "Error[" . $e->getCode() . "]: " . $e->getMessage() . "\n";
	exit(1);

}

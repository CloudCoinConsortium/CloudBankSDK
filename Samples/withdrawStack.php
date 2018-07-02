<?php

require __DIR__ . "/CloudBank/vendor/autoload.php";

use CloudBank\CloudBank;
use CloudBank\CloudBankException;

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

		$amountToWithdraw = 250;

		$withdrawResponse = $cBank->withdrawStack($amountToWithdraw);
		if ($withdrawResponse->isError()) {
			die("Failed withdraw: " . $withdrawResponse->message);
		}

		$stack = $withdrawResponse->getStack();
		file_put_contents("/path/to/mystack.json", $stack);
	} else {
		echo "RAIDA is not ready. Plese try again later\n";
		exit(0);
	}


} catch (CloudBankException $e) {
	echo "Error[" . $e->getCode() . "]: " . $e->getMessage() . "\n";
	exit(1);

}

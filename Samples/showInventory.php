<?php

require __DIR__ . "/CloudBank/vendor/autoload.php";

use CloudBank\CloudBank;
use CloudBank\CloudBankException;

try {
	$cBank = new CloudBank([
		"url" => 'https://bank.CloudCoin.global/service',
		"privateKey" => "00000000000000000000000000000000",
		"account" => "CloudCoin@Protonmail.com",
		"debug" => "true"
	]);

	echo "CloudBank Version: " . $cBank->getVersion() . "\n";

	// Check CloudBank Service
	$welcomeResponse = $cBank->printWelcome();
	echo $welcomeResponse->message . "\n";

	// Check RAIDA
	$echoResponse = $cBank->echoRAIDA();
	if ($echoResponse->status == "ready") {
		echo $echoResponse->message . "\n\n";

		$showCoinsResponse = $cBank->showCoins();
		if (!$showCoinsResponse->isError()) {
			echo "Total: " . $showCoinsResponse->getTotal() . "\n";
			$coins = $showCoinsResponse->getBulk();
			foreach ($coins as $denomination => $value) {
				echo "$denomination: $value\n";
			}
		}
	} else {
		echo "RAIDA is not ready. Plese try again later\n";
		exit(0);
	}


} catch (CloudBankException $e) {
	echo "Error[" . $e->getCode() . "]: " . $e->getMessage() . "\n";
	exit(1);

}


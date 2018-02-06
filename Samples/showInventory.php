<?php

require "CloudBank/autoload.php";

use CloudBank\CloudBank;
use CloudBank\CloudBankException;

try {
	$cBank = new CloudBank([
		"url" => 'https://bank.cloudcoin.global/service',
		"privateKey" => "1DECE3AF-43EC-435B-8C39-E2A5D0EA8677"
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


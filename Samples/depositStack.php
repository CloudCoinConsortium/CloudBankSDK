<?php

require "CloudBank/autoload.php";

use CloudBank\CloudBank;
use CloudBank\CloudBankException;

// Update receipt status in the Database
function updateReceipt($receiptNumber, $status) {
	return true;
}

try {
	$cBank = new CloudBank([
		"url" => 'https://bank.cloudcoin.global/service',
		"privateKey" => "1DECE3AF-43EC-435B-8C39-E2A5D0EA8677"
	]);

	echo "CloudBank Version: " . $cBank->getVersion() . "\n";
	// Check RAIDA
	$echoResponse = $cBank->echoRAIDA();
	if ($echoResponse->status == "ready") {

		$stack = file_get_contents("/path/to/stackfile.json");
		if (!$stack)
			die("Can not get stack file");

		$receiptNumber = "ea22cbae0394f6c6918691f2e2f2e267";
		$depositResponse = $cBank->depositStack($stack, $receiptNumber);

		if ($depositResponse->isError()) {
			updateReceipt($receiptNumber, "error");
			die("Failed to import stack");
		}

		echo $depositResponse->message . "\n";

		updateReceipt($reseiptNumber, $depositResponse->status);

		// Wait for IMPORT. Better do it in a separate thread
		echo "Waiting for import\n";
		sleep(10);

		$receiptResonse = $ccb->getReceipt($receiptNumber);
		if (!$receiptResonse->isValid()) {
			updateReceipt($receiptNumber, "counterfeit");
			die("Counterfeit stack" . print_r($receiptResonse->receipt, true));
		}

		updateReceipt($receiptNumber, "authentic");
	} else {
		echo "RAIDA is not ready. Plese try again later\n";
		exit(0);
	}


} catch (CloudBankException $e) {
	echo "Error[" . $e->getCode() . "]: " . $e->getMessage() . "\n";
	exit(1);

}

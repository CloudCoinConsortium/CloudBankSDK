<?php

require __DIR__ . "/CloudBank/vendor/autoload.php";

use CloudBank\CloudBank;
use CloudBank\CloudBankException;

// Update receipt status in the Database
function updateReceipt($localReceiptID, $receiptNumber, $status) {
	return true;
}

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

		$stack = file_get_contents("e.stack");
		if (!$stack)
			die("Can not get stack file");

		$localReceiptID = "r125";

//		$receiptNumber = "ea22cbae0394f6c6918691f2e2f2e267";
//		$depositResponse = $cBank->depositStack($stack, $receiptNumber);
		$depositResponse = $cBank->depositStack($stack);

		if ($depositResponse->isError()) {
			updateReceipt($receiptNumber, "error");
			die("Failed to import stack");
		}

		echo $depositResponse->message . "\n";

		$receiptNumber = $depositResponse->receipt;

		updateReceipt($localReceiptID, $receiptNumber, $depositResponse->status);

		// Wait for IMPORT. Better do it in a separate thread
		echo "Waiting for import\n";
		sleep(10);

		$receiptResonse = $cBank->getReceipt($receiptNumber);
		if (!$receiptResonse->isValid()) {
			updateReceipt($localReceiptID, $receiptNumber, "counterfeit");
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

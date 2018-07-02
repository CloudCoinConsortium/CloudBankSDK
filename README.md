*CloudBank PHP SDK*

SDK helps to accept CloudCoins on any website. It uses CloudBank protocol to talk to a remote CloudBank.

https://github.com/worthingtonse/Bank


SDK implements the following services:

```php
getVersion()
printWelcome()
echoRAIDA()
depositStack()
withdrawStack()
getReceipt()
writeCheck()
getCashCheckURL()
```


*Basic usage:*

```php
	require __DIR__ . "CloudBank/vendor/autoload.php";

	use CloudBank\CloudBank;

	$cBank = new CloudBank([
		"url" => 'https://bank.cloudcoin.global/service',
		"privateKey" => "1DECE3AF-43EC-435B-8C39-E2A5D0EA8677",
		"account" => "myaccount@protonmail.com",
		"debug" => false
	]);

        echo "CloudBank Version: " . $cBank->getVersion() . "\n";

	$welcome = $cBank->printWelcome();
	echo $welcome->message;
```

Other examples can be found in 'Samples' folder


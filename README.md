*CloudBank PHP SDK*

SDK helps to accept CloudCoins on any website. It uses CloudBank protocol to talk to a remote CloudBank.

https://github.com/worthingtonse/Bank

SDK implements the following services:

*Basic usage:*

```php
	$cBank = new CloudBank([
		"url" => 'https://bank.cloudcoin.global/service',
		"privateKey" => "1DECE3AF-43EC-435B-8C39-E2A5D0EA8677",
		"debug" => false
	]);

        echo "CloudBank Version: " . $cBank->getVersion() 
```

Other examples can be found in 'Samples' folder


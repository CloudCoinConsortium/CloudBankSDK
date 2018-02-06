<?php
/**
 * Copyright 2018 CloudCoin 
 *
 * You are hereby granted a non-exclusive, worldwide, royalty-free license to
 * use, copy, modify, and distribute this software in source code or binary
 * form for use in connection with the web services and APIs provided by
 * Facebook.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 */

namespace CloudBank;

use CloudBank\HTTPClient\HTTPClientFactory;
use CloudBank\CloudBankException;
use CloudBank\Logger;

class CloudBank {
	const VERSION = '0.1.15';

	private $client;

	private $config;

	private $rmappings;

	private $privateKey;

	private $validator;

	public function __construct($config = []) {

		$this->config = array_merge([
			"url" => "",
			"debug" => false,
			"privateKey" => ""
		], $config);

		if ($this->config['debug'])
			Logger::init(Logger::MSGTYPE_DEBUG);

		Logger::debug("SDK initialized: " . print_r($this->config, true));

		if (!$this->config['url']) {
			throw new CloudBankException("Required 'url' parameter not supplied");
		}

		$this->privateKey = $this->config['privateKey'];

		$this->setResponseMappings();
		$rmappings = $this->rmappings;

		$this->client = HTTPClientFactory::createClient();
		$this->client->setBaseURL($this->config['url']);
		$this->client->setProcessResponseFunc(function($data, $url) use ($rmappings) {
			$payload = @json_decode($data);
			$jsonLastError = json_last_error();
			if ($jsonLastError !== JSON_ERROR_NONE) {
				throw new CloudBankException("Failed to parse JSON: " . $jsonLastError);
			}

			$url = preg_replace("/\/?(.+)\.(aspx|php)?(\?.*)?$/", "$1", $url);
			if (!isset($rmappings[$url])) {
				Logger::debug(print_r($rmappings, true));
				throw new CloudBankException("Invalid response format for $url");
			}

			$className = "\CloudBank\\" . $rmappings[$url];
			$rObject = new $className();
			foreach ($payload as $k => $v)
				$rObject->$k = $v;

			return $rObject;
		});


		$this->validator = new Validator();
	//	$rv = $this->client->send("/print_welcome.aspx");
	}

	private function setResponseMappings() {
		$this->rmappings = [
			"print_welcome" => "WelcomeResponse",
			"echo"		=> "EchoRAIDAResponse",
			"import_one_stack" => "DepositStackResponse",
			"deposit" 	=> "DepositStackResponse",
			"get_receipt"	=> "GetReceiptResponse",
			"show_coins"	=> "ShowCoinsResponse",
			"withdraw_one_stack" => "WithdrawStackResponse",
			"write_check"	=> "WriteCheckResponse"
		];
	}

	public function printWelcome() {
		$welcomeResponse = $this->client->send("print_welcome.aspx");

		return $welcomeResponse;
	}

	public function echoRAIDA() {
		$echoRAIDAResponse = $this->client->send("echo.aspx");

		return $echoRAIDAResponse;
	}

	public function depositStack($stack, $rn = null) {
		Logger::debug("Deposit Stack");

		//$url = "import_one_stack.aspx";
		$url = "deposit.aspx";
		if ($rn)
			$url .= "?rn=$rn";

		$stack="stack=$stack";
		
		$depositStackResponse = $this->client->send($url, $stack);

		return $depositStackResponse;
	}

	public function withdrawStack($amount, $format = "json", $tag = null) {
		Logger::debug("Withdraw $amount CC");

		$amount = intval($amount);
		if (!$this->validator->amount($amount))
			throw new CloudBankException("Invalid amount");

		if (!$this->validator->sendType($format))
			throw new CloudBankException("Invalid format. Must be one of 'json','email','url'");


		$url = "export_one_stack.aspx";
		$params = "amount=$amount&sendby=$format";
		if ($tag)
			$params .= "&tag=$tag";

		$params .= "&" . $this->getPK();
		
		$withdrawStackResponse = $this->client->send($url, $params);

		return $withdrawStackResponse;
	}

	public function writeCheck($amount, $checkId, $email, $payto, $fromemail, $by, $memo = "") {
		Logger::debug("WriteCheck $checkId: $amount CC to $email");

		$amount = intval($amount);
		if (!$this->validator->amount($amount))
			throw new CloudBankException("Invalid amount");
	
		if (!$this->validator->email($email))
			throw new CloudBankException("Invalid email");

		if (!$this->validator->email($fromemail))
			throw new CloudBankException("Invalid email from");

		if (!$this->validator->checkID($checkId))
			throw new CloudBankException("Invalid checkID. Must contain alphanum chars. Min length is 4");

		if (!$payto)	
			throw new CloudBankException("Payee is not specifed");

		if (!$by) 
			throw new CloudBankException("Payer is not specifed");

		$url = "write_check.aspx";

		$params = $this->getPK();
		$params .= "&action=email&amount=$amount&checkid=$checkId";
		$params .= "&emailto=$email&payto=$payto&from=$fromemail&signby=$by&memo=$memo";

		$writeCheckResponse = $this->client->send($url, $params);

		return $writeCheckResponse;
	}

	public function getCashCheckURL($checkId, $format = "json", $data = "") {
		if (!$this->validator->checkID($checkId))
			throw new CloudBankException("Invalid checkID. Must contain alphanum chars. Min length is 4");

		if (!$this->validator->sendType($format))
			throw new CloudBankException("Invalid format. Must be one of 'json','email','url'");

		$url = $this->config['url'] . "/checks.aspx?id=$checkId";
		$url .= "&receive=$format";

		if ($format == "email" && !$this->validator->email($data))
			throw new CloudBankException("Invalid email");

		if ($format == "sms" && !$this->validator->phoneNumber($data))
			throw new CloudBankException("Invalid phone number");
			
		if ($data)
			$url .= "&contact=$data";

		return $url;
	}


	public function getReceipt($rn) {
		Logger::debug("Get Receipt $rn");

		$url = "get_receipt.aspx?rn=$rn";

		$getReceiptResponse = $this->client->send($url, $this->getPK());

		return $getReceiptResponse;
	}

	public function showCoins() {
		Logger::debug("Show coins");

		$showCoinsResponse = $this->client->send("show_coins.aspx", $this->getPK());

		return $showCoinsResponse;
	}

	public function getVersion() {
		return self::VERSION;
	}

	private function getPK() {
		return "pk=" . $this->privateKey;
	}


}


?>

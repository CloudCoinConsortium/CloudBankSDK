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

namespace CloudCoinBank;

use CloudCoinBank\HTTPClient\HTTPClientFactory;
use CloudCoinBank\CloudBankException;
use CloudCoinBank\Logger;

class CloudCoinBank {
	const VERSION = '0.1.15';

	private $client;

	private $config;

	public function __construct($config = []) {

		$this->config = array_merge([
			"id" => "",
			"url" => "",
			"debug" => false
		], $config);

		if ($this->config['debug'])
			Logger::init(Logger::MSGTYPE_DEBUG);

		Logger::debug("SDK initialized: " . print_r($this->config, true));

		if (!$this->config['url']) {
			throw new CloudBankException("Required 'url' parameter not supplied");
		}

		$this->client = HTTPClientFactory::createClient();
		$this->client->setBaseURL($this->config['url']);
		$this->client->setProcessResponseFunc(function($data) {
			$payload = @json_decode($data);
			$jsonLastError = json_last_error();
			if ($jsonLastError !== JSON_ERROR_NONE) {
				throw new CloudBankException("Failed to parse JSON: " . $jsonLastError);
			}

			return $payload;
		});

		$rv = $this->client->send("/print_welcome.aspx");
	}

	public function getVersion() {
		return self::VERSION;
	}

}


?>

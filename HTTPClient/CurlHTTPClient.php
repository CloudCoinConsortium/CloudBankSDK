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


namespace CloudBank\HTTPClient;

use CloudBank\CloudBankException;
use CloudBank\Logger;

class CurlHTTPClient implements HTTPClientInterface {

	const CURL_DEFAULT_TIMEOUT = 10;
	const CURL_DEFAULT_CONTENT = "application/x-www-form-urlencoded";

	private $timeout;

	private $method;

	private $curl;

	private $verifyCert = true;

	private $url, $baseURL;

	private $response;
	private $responseCode;
	private $responseBody, $rawBody;
	private $responseHeaders;

	private $prFunc;

        public function send($url, $body = "", array $headers = []) {
		if (!$this->timeout)
			$this->setTimeout(self::CURL_DEFAULT_TIMEOUT);

		$this->url = $url;
		$this->method = $body ? "POST" : "GET";
			
		if ($this->baseURL)
			$url = $this->baseURL . "/$url";

		
		Logger::debug("Connecting to $url [{$this->method}]");
		if ($body)
			Logger::debug(print_r($body, true));

		$this->curl = @curl_init();
		if (!$this->curl) 
			throw new CloudBankException("Failed to initialize CURL");

		$hArray = [];
		foreach ($headers as $header => $value) 
			$hArray[] = "$header: $value";

		if ($this->method == "POST") {
			if (!isset($headers['Content-Type'])) 
				$hArray[] = "Content-Type: " . self::CURL_DEFAULT_CONTENT;

			if (!isset($headers['Content-Length']))
				$hArray[] = "Content-Length: " . strlen($body);

		}

		curl_setopt($this->curl, CURLOPT_HTTPHEADER, $hArray);

		curl_setopt($this->curl, CURLOPT_URL, $url);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->curl, CURLOPT_TIMEOUT, $this->timeout);

		if (!$this->verifyCert) {
			curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
		}

		curl_setopt($this->curl, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, $this->timeout);
		curl_setopt($this->curl, CURLOPT_TIMEOUT, $this->timeout);

		//curl_setopt($this->curl, CURLOPT_VERBOSE, true);

		if ($body)
			curl_setopt($this->curl, CURLOPT_POSTFIELDS, $body);


		curl_setopt($this->curl, CURLOPT_POST, true);
		curl_setopt($this->curl, CURLOPT_HEADER, true);

		$this->response = curl_exec($this->curl);
		if (curl_errno($this->curl)) {
			$error = "Failed to exec URL. #" . curl_errno($this->curl) . ": " . curl_error($this->curl);
			throw new CloudBankException($error);
		}

		$info = curl_getinfo($this->curl);
		if (!$info) {
			throw new CloudBankException("Failed to obtain CURL info");
		}

		$this->responseCode = $info['http_code'];
		curl_close($this->curl);

		Logger::debug("ResponseCode: {$this->responseCode}");

		$this->setResponse();

		return $this->responseBody;
	}

	public function setProcessResponseFunc($function) {
		$this->prFunc = $function;
	}

	public function setBaseURL($url) {
		$this->baseURL = $url;
	}

        public function setTimeout($timeout) {
		$this->timeout = $timeout;
	}

	public function getTimeout() {
		return $this->timeout;
	}

	public function enableCertVerify() {
		$this->verifyCert = true;
	}

	public function disableCertVerify() {
		$this->verifyCert = false;
	}

	private function setResponse() {

		$parts = explode("\r\n\r\n", $this->response);

		$this->rawBody = trim(array_pop($parts));

		Logger::debug("RawBody: {$this->rawBody}");

		$headers = explode("\r\n", $parts[0]);
		array_shift($headers);

		foreach ($headers as $header) {
			list ($k, $v) = preg_split("/:/", $header, 2);

			$k = trim($k);
			$v = trim($v);

			$this->headers[$k] = $v;
		}

		if ($this->prFunc) {
			$prFunc = $this->prFunc;
			$this->responseBody = $prFunc($this->rawBody, $this->url);
		}

	}

	public function __toString() {
		return $this->rawBody;
	}

}


?>

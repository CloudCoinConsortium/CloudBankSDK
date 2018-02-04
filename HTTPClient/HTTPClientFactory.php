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

namespace CloudCoinBank\HTTPClient;

use CloudCoinBank\CloudBankException;
use CloudCoinBank\Logger;

class HTTPClientFactory {

	const HTTPCLIENT_TYPE_CURL = 0x1;
	const HTTPCLIENT_TYPE_RAW  = 0x2;

	private function __construct() {
		return null;
	}

	public static function createClient($type = 0) {

		Logger::debug("Creating HTTP Client");

		if (!$type) {
			$type = self::detectDefaultClient();
		}

		if ($type instanceof HTTPClientInterface) {
			return $type;
		}

		if ($type == self::HTTPCLIENT_TYPE_CURL)
			return new CurlHTTPClient();


		throw new CloudBankException("Unable to set HTTP Client. No matching libraries found");
	
		return null;
	}

	private static function detectDefaultClient() {
		if (extension_loaded('curl'))
			return self::HTTPCLIENT_TYPE_CURL;

		// Only curl is supported now

		return null;
	}


}



?>

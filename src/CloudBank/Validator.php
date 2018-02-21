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

use CloudBank\CloudBankException;
use CloudBank\Logger;


class Validator {

	function __construct() {
	}


	public function digit($data, $min, $max) {
		return $data >= $min && $data <= $max;
	}

	public function amount($data) {
		return $this->digit($data, 0, PHP_INT_MAX);
	}

	public function email($data) {
		return filter_var($data, FILTER_VALIDATE_EMAIL);
	}

	public function checkID($data) {
		return preg_match("/^[A-Za-z0-9]{4,32}$/", $data);
	}

	public function sendType($data) {
		return in_array($data, ["json", "email", "url", "download", "sms"]);
	}

	public function phoneNumber($data) {
		return preg_match("/^\d{5,18}$/", $data);
	}

}


?>

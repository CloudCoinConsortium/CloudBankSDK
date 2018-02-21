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

class GetReceiptResponse extends CloudBankResponse {

	public $bank_server;

	public $status;

	public $message;

	public $time;

	public $version;

	public $receipt_id;

	public $total_authentic, $total_fracked, $total_lost, $total_counterfeit;

	public $receipt;

	public function __construct() {
		$this->total_authentic = $this->total_fracked = $this->total_lost = $this->total_counterfeit = 0;
	}

	public function getTotal() {
		return $this->total_authentic + $this->total_fracked + $this->total_lost + $this->total_counterfeit;
	}

	public function getTotalValid() {
		return $this->total_authentic + $this->total_fracked;
	}

	public function isValid() {
		if ($this->total_counterfeit > 0 || $this->total_lost > 0)
			return false;

		if ($this->total_authentic > 0 || $this->total_fracked > 0)
			return true;

		return false;
	}

}


?>

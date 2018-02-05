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

class ShowCoinsResponse extends CloudBankResponse {

	public $bank_server;

	public $status;

	public $message;

	public $time;

	public $ones, $fives, $twentyfives, $hundreds, $twohundredfifties;

	public function __construct() {
		$this->ones = $this->fives = $this->twentyfives = $this->hundreds = $this->twohundredfifties = 0;
	}
	
	public function getTotalCoins() {
		return $this->ones + $this->fives + $this->twentyfives + $this->hundreds + $this->twohundredfifties;
	}

	public function getTotal() {
		return $this->ones + $this->fives * 5 
			+ $this->twentyfives * 25
			+ $this->hundreds *100
			+ $this->twohundredfifties * 250;

	}

}


?>

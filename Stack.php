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

class Stack {

	private $cloudcoin;
	private $totalCoins;
	private $total;

	public function __construct($stack) {
		$stack = @json_decode($stack);
                $jsonLastError = json_last_error();
		if ($jsonLastError !== JSON_ERROR_NONE) {
			throw new CloudBankException("Failed to parse stack: " . $jsonLastError);
		}

		if (!isset($stack->cloudcoin)) {
			throw new CloudBankException("Failed to parse stack: " . $jsonLastError);
		}

		$this->total = 0;
		$this->totalCoins = count($stack->cloudcoin);
		$this->cloudcoin = $stack->cloudcoin;
		foreach ($this->cloudcoin as $idx => $cc) {
			$this->cloudcoin[$idx]->denomination = self::getDenomination($cc->sn);
			$this->total += $this->cloudcoin[$idx]->denomination;
		}
	}

	public function getTotal() {
		return $this->total;
	}

	public function getStack() {
		$data = @json_encode($this->cloudcoin);
		$data = "{\"cloudcoin\":$data}";

		return $data;
	}

	static function getDenomination($sn) {
		if ($sn < 1)
			return 0;
			
		if ($sn < 2097153)
			return 1;

		if ($sn < 4194305)
			return 5;

		if ($sn < 6291457)
			return 25;

		if ($sn < 14680065)
			return 100;

		if ($sn < 16777217)
			return 250;

		return 0;
	}
}



?>

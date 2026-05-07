<?php

declare(strict_types=1);

namespace OCA\TwoFactorKannel\Tests\Unit;

use OCA\TwoFactorKannel\Provider\Channel\SMS\Provider\Drivers\Kannel;
use PHPUnit\Framework\TestCase;

class KannelResponseTest extends TestCase {
	public function testAcceptsZeroPrefix(): void {
		$this->assertTrue(Kannel::isSuccessBody('0: Accepted for delivery'));
	}

	public function testAcceptsThreePrefix(): void {
		// Kannel response 3 (queued for later delivery) is also a success.
		$this->assertTrue(Kannel::isSuccessBody('3: Queued for later delivery'));
	}

	public function testRejectsErrorPrefixes(): void {
		$this->assertFalse(Kannel::isSuccessBody('1: Internal error'));
		$this->assertFalse(Kannel::isSuccessBody('2: Recipient does not exist'));
		$this->assertFalse(Kannel::isSuccessBody('6: Delivery rejected'));
	}

	public function testRejectsEmptyAndUnknown(): void {
		$this->assertFalse(Kannel::isSuccessBody(''));
		$this->assertFalse(Kannel::isSuccessBody('OK'));
		$this->assertFalse(Kannel::isSuccessBody('Accepted'));
	}
}

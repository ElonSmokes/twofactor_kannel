<?php

declare(strict_types=1);

namespace OCA\TwoFactorKannel\Tests\Unit;

use OCA\TwoFactorKannel\PhoneNumberMask;
use PHPUnit\Framework\TestCase;

class PhoneNumberMaskTest extends TestCase {
	public function testMasksAllButLastThree(): void {
		$this->assertSame('**********890', PhoneNumberMask::maskNumber('+441234567890'));
	}

	public function testHandlesShortInputsWithoutCrashing(): void {
		// Regression: prior to the fix, length<=3 inputs caused str_repeat('*', -n)
		// to throw a ValueError on PHP 8.
		$this->assertSame('***', PhoneNumberMask::maskNumber('123'));
		$this->assertSame('**', PhoneNumberMask::maskNumber('12'));
		$this->assertSame('*', PhoneNumberMask::maskNumber('1'));
		$this->assertSame('', PhoneNumberMask::maskNumber(''));
	}

	public function testMasksFourCharacterInput(): void {
		$this->assertSame('*234', PhoneNumberMask::maskNumber('1234'));
	}
}

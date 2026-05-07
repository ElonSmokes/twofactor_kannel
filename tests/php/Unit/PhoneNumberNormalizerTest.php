<?php

declare(strict_types=1);

namespace OCA\TwoFactorKannel\Tests\Unit;

use OCA\TwoFactorKannel\PhoneNumberNormalizer;
use PHPUnit\Framework\TestCase;

class PhoneNumberNormalizerTest extends TestCase {
	/** @return array<string,array{string,string}> */
	public static function cases(): array {
		return [
			'plain e164' => ['+441234567890', '+441234567890'],
			'with spaces' => ['+44 1234 567 890', '+441234567890'],
			'with dashes' => ['+44-1234-567-890', '+441234567890'],
			'with parens' => ['+44 (0) 1234 567890', '+4401234567890'], // illustrative; depends on input shape
			'leading 00 form' => ['00441234567890', '+441234567890'],
			'too short' => ['+441', ''],
			'too long' => ['+1234567890123456789', ''],
			'no plus' => ['441234567890', ''],
			'starts with zero after plus' => ['+0441234567890', ''],
			'empty' => ['', ''],
			'only symbols' => ['++++', ''],
			'whitespace padding' => ['  +441234567890  ', '+441234567890'],
		];
	}

	/**
	 * @dataProvider cases
	 */
	public function testNormalize(string $input, string $expected): void {
		// Note: the parens case is an illustration only — the normalizer strips
		// non-digit/non-plus chars first, so '+44 (0) 1234 567890' becomes
		// '+44012345678900' which fits the pattern. We verify the contract
		// rather than the exact behaviour for that specific input.
		$result = PhoneNumberNormalizer::normalize($input);
		$this->assertSame($expected, $result);
	}

	public function testIdempotent(): void {
		$once = PhoneNumberNormalizer::normalize('+44 1234 567 890');
		$twice = PhoneNumberNormalizer::normalize($once);
		$this->assertSame($once, $twice);
	}
}

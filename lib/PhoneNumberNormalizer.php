<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 ElonSmokes and contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorKannel;

class PhoneNumberNormalizer {
	private const INTERNATIONAL_PHONE_PATTERN = '/^\+[1-9]\d{7,14}$/';

	public static function normalize(string $phone): string {
		$normalized = preg_replace('/[^\d+]/', '', trim($phone)) ?? '';
		if ($normalized === '') {
			return '';
		}

		if (str_starts_with($normalized, '00')) {
			$normalized = '+' . substr($normalized, 2);
		}

		if (preg_match(self::INTERNATIONAL_PHONE_PATTERN, $normalized) !== 1) {
			return '';
		}

		return $normalized;
	}
}

<?php

declare(strict_types=1);

namespace OCA\TwoFactorKannel\Service;

use OCA\TwoFactorKannel\PhoneNumberMask;
use OCP\Accounts\IAccountManager;
use OCP\IUser;

class UserPhoneService {
	private const INTERNATIONAL_PHONE_PATTERN = '/^\+[1-9]\d{7,14}$/';

	public function __construct(
		private IAccountManager $accountManager,
	) {
	}

	public function getPhoneNumber(IUser $user): string {
		try {
			return $this->normalizeInternationalPhone((string)$this->accountManager->getAccount($user)->getProperty(IAccountManager::PROPERTY_PHONE)->getValue());
		} catch (\Throwable) {
			return '';
		}
	}

	public function getMaskedPhoneNumber(IUser $user): string {
		$phone = $this->getPhoneNumber($user);
		return $phone === '' ? '' : PhoneNumberMask::maskNumber($phone);
	}

	public function normalizeInternationalPhone(string $phone): string {
		$normalized = preg_replace('/[^\d+]/', '', trim($phone)) ?? '';
		if ($normalized === '') {
			return '';
		}

		if (str_starts_with($normalized, '00')) {
			$normalized = '+' . substr($normalized, 2);
		}

		if (!preg_match(self::INTERNATIONAL_PHONE_PATTERN, $normalized)) {
			return '';
		}

		return $normalized;
	}
}

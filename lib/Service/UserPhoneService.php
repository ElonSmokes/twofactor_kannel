<?php

declare(strict_types=1);

namespace OCA\TwoFactorKannel\Service;

use OCA\TwoFactorKannel\PhoneNumberMask;
use OCP\Accounts\IAccountManager;
use OCP\IUser;

class UserPhoneService {
	public function __construct(
		private IAccountManager $accountManager,
	) {
	}

	public function getPhoneNumber(IUser $user): string {
		try {
			return trim($this->accountManager->getAccount($user)->getProperty(IAccountManager::PROPERTY_PHONE)->getValue());
		} catch (\Throwable) {
			return '';
		}
	}

	public function getMaskedPhoneNumber(IUser $user): string {
		$phone = $this->getPhoneNumber($user);
		return $phone === '' ? '' : PhoneNumberMask::maskNumber($phone);
	}
}

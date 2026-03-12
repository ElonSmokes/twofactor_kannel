<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2024 Christoph Wurst <christoph@winzerhof-wurst.at>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorKannel\Service;

use Exception;
use OCA\TwoFactorKannel\AppInfo\Application;
use OCA\TwoFactorKannel\Provider\State;
use OCP\IConfig;
use OCP\IUser;

class StateStorage {
	public const STATE_DISABLED = 0;
	public const STATE_START_VERIFICATION = 1;
	public const STATE_VERIFYING = 2;
	public const STATE_ENABLED = 3;

	public function __construct(
		private IConfig $config,
	) {
	}

	private function buildConfigKey(string $gatewayName, string $key): string {
		return $gatewayName . "_$key";
	}

	private function getUserValue(IUser $user, string $gatewayName, string $key, string $default = ''): string {
		$gatewayKey = $this->buildConfigKey($gatewayName, $key);
		return $this->config->getUserValue($user->getUID(), Application::APP_ID, $gatewayKey, $default);
	}

	private function setUserValue(IUser $user, string $gatewayName, string $key, ?string $value): void {
		$gatewayKey = $this->buildConfigKey($gatewayName, $key);
		$this->config->setUserValue($user->getUID(), Application::APP_ID, $gatewayKey, $value);
	}

	private function deleteUserValue(IUser $user, string $gatewayName, string $key): void {
		$gatewayKey = $this->buildConfigKey($gatewayName, $key);
		$this->config->deleteUserValue($user->getUID(), Application::APP_ID, $gatewayKey);
	}

	public function get(IUser $user, string $gatewayName): State {
		$isVerified = $this->getUserValue($user, $gatewayName, 'verified', 'false') === 'true';
		$identifier = $this->getUserValue($user, $gatewayName, 'identifier');
		$verificationCode = $this->getUserValue($user, $gatewayName, 'verification_code');
		$expiresAt = $this->getUserValue($user, $gatewayName, 'expires_at');
		$resendAvailableAt = $this->getUserValue($user, $gatewayName, 'resend_available_at');
		$failedAttempts = (int)$this->getUserValue($user, $gatewayName, 'failed_attempts', '0');

		if ($isVerified) {
			$state = StateStorage::STATE_ENABLED;
		} elseif ($identifier !== '' && $verificationCode !== '') {
			$state = StateStorage::STATE_VERIFYING;
		} else {
			$state = StateStorage::STATE_DISABLED;
		}

		return new State(
			$user,
			$state,
			$gatewayName,
			$identifier,
			$verificationCode,
			$expiresAt !== '' ? (int)$expiresAt : null,
			$resendAvailableAt !== '' ? (int)$resendAvailableAt : null,
			$failedAttempts
		);
	}

	public function persist(State $state): State {
		switch ($state->getState()) {
			case StateStorage::STATE_DISABLED:
				$this->deleteUserValue(
					$state->getUser(),
					$state->getGatewayName(),
					'identifier'
				);
				$this->deleteUserValue(
					$state->getUser(),
					$state->getGatewayName(),
					'verified'
				);
				$this->deleteUserValue(
					$state->getUser(),
					$state->getGatewayName(),
					'verification_code'
				);
				$this->deleteUserValue($state->getUser(), $state->getGatewayName(), 'expires_at');
				$this->deleteUserValue($state->getUser(), $state->getGatewayName(), 'resend_available_at');
				$this->deleteUserValue($state->getUser(), $state->getGatewayName(), 'failed_attempts');

				break;
			case StateStorage::STATE_VERIFYING:
				$this->setUserValue(
					$state->getUser(),
					$state->getGatewayName(),
					'identifier',
					$state->getIdentifier()
				);
				$this->setUserValue(
					$state->getUser(),
					$state->getGatewayName(),
					'verification_code',
					$state->getVerificationCode()
				);
				$this->setUserValue($state->getUser(), $state->getGatewayName(), 'expires_at', (string)$state->getExpiresAt());
				$this->setUserValue($state->getUser(), $state->getGatewayName(), 'resend_available_at', (string)$state->getResendAvailableAt());
				$this->setUserValue($state->getUser(), $state->getGatewayName(), 'failed_attempts', (string)$state->getFailedAttempts());
				$this->setUserValue(
					$state->getUser(),
					$state->getGatewayName(),
					'verified',
					'false'
				);

				break;
			case StateStorage::STATE_ENABLED:
				$this->setUserValue(
					$state->getUser(),
					$state->getGatewayName(),
					'identifier',
					$state->getIdentifier()
				);
				$this->setUserValue(
					$state->getUser(),
					$state->getGatewayName(),
					'verification_code',
					$state->getVerificationCode()
				);
				$this->setUserValue($state->getUser(), $state->getGatewayName(), 'expires_at', (string)$state->getExpiresAt());
				$this->setUserValue($state->getUser(), $state->getGatewayName(), 'resend_available_at', (string)$state->getResendAvailableAt());
				$this->setUserValue($state->getUser(), $state->getGatewayName(), 'failed_attempts', '0');
				$this->setUserValue(
					$state->getUser(),
					$state->getGatewayName(),
					'verified',
					'true'
				);

				break;
			default:
				throw new Exception('invalid provider state');
		}

		return $state;
	}
}

<?php

/**
 * SPDX-FileCopyrightText: 2018 Christoph Wurst <christoph@winzerhof-wurst.at>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorKannel\Provider;

use JsonSerializable;
use OCA\TwoFactorKannel\Service\StateStorage;
use OCP\IUser;

/**
 * @psalm-type TwoFactorKannelState = array{
 *     gatewayName: string,
 *     state: int,
 *     phoneNumber: ?string,
 *     expiresAt: ?int,
 *     resendAvailableAt: ?int,
 *     failedAttempts: int,
 * }
 *
 * @psalm-assert-if-true TwoFactorKannelState $this
 */
class State implements JsonSerializable {

	public function __construct(
		private IUser $user,
		private int $state,
		private string $gatewayName,
		private ?string $identifier = null,
		private ?string $verificationCode = null,
		private ?int $expiresAt = null,
		private ?int $resendAvailableAt = null,
		private int $failedAttempts = 0,
	) {
	}

	public static function verifying(IUser $user,
		string $gatewayName,
		string $identifier,
		string $verificationCode,
		int $expiresAt,
		int $resendAvailableAt,
		int $failedAttempts = 0): State {
		return new State(
			$user,
			StateStorage::STATE_VERIFYING,
			$gatewayName,
			$identifier,
			$verificationCode,
			$expiresAt,
			$resendAvailableAt,
			$failedAttempts
		);
	}

	public static function disabled(IUser $user, string $gatewayName): State {
		return new State(
			$user,
			StateStorage::STATE_DISABLED,
			$gatewayName
		);
	}

	public function verify(): State {
		return new State(
			$this->user,
			StateStorage::STATE_ENABLED,
			$this->gatewayName,
			$this->identifier,
			$this->verificationCode,
			$this->expiresAt,
			$this->resendAvailableAt,
			0
		);
	}

	public function withFailedAttempt(): State {
		return new State(
			$this->user,
			$this->state,
			$this->gatewayName,
			$this->identifier,
			$this->verificationCode,
			$this->expiresAt,
			$this->resendAvailableAt,
			$this->failedAttempts + 1
		);
	}

	public function getUser(): IUser {
		return $this->user;
	}

	public function getState(): int {
		return $this->state;
	}

	public function getGatewayName(): string {
		return $this->gatewayName;
	}

	public function getIdentifier(): ?string {
		return $this->identifier;
	}

	public function getVerificationCode(): ?string {
		return $this->verificationCode;
	}

	public function getExpiresAt(): ?int {
		return $this->expiresAt;
	}

	public function getResendAvailableAt(): ?int {
		return $this->resendAvailableAt;
	}

	public function getFailedAttempts(): int {
		return $this->failedAttempts;
	}

	#[\Override]
	public function jsonSerialize(): mixed {
		return [
			'gatewayName' => $this->gatewayName,
			'state' => $this->state,
			'phoneNumber' => $this->identifier,
			'expiresAt' => $this->expiresAt,
			'resendAvailableAt' => $this->resendAvailableAt,
			'failedAttempts' => $this->failedAttempts,
		];
	}
}

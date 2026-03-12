<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2024 Christoph Wurst <christoph@winzerhof-wurst.at>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorKannel\Service;

use Exception;
use OCA\TwoFactorKannel\Exception\IdentifierMissingException;
use OCA\TwoFactorKannel\Exception\InvalidProviderException;
use OCA\TwoFactorKannel\Exception\MessageTransmissionException;
use OCA\TwoFactorKannel\Exception\VerificationException;
use OCA\TwoFactorKannel\Provider\Factory as ProviderFactory;
use OCA\TwoFactorKannel\Provider\Gateway\Factory as GatewayFactory;
use OCA\TwoFactorKannel\Provider\State;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\Authentication\TwoFactorAuth\IRegistry;
use OCP\IL10N;
use OCP\IUser;
use OCP\Security\ISecureRandom;

class SetupService {
	private const RESEND_COOLDOWN = 90;
	private const CODE_TTL = 120;
	private const MAX_FAILED_ATTEMPTS = 5;

	public function __construct(
		private StateStorage $stateStorage,
		private GatewayFactory $gatewayFactory,
		private ProviderFactory $providerFactory,
		private ISecureRandom $random,
		private IRegistry $providerRegistry,
		private IL10N $l10n,
		private ITimeFactory $timeFactory,
		private UserPhoneService $userPhoneService,
	) {
	}

	public function getState(IUser $user, string $gatewayName): State {
		return $this->stateStorage->get($user, $gatewayName);
	}

	/**
	 * @throws IdentifierMissingException
	 */
	public function getChallengePhoneNumber(IUser $user, string $gatewayName): string {
		$state = $this->stateStorage->get($user, $gatewayName);
		$identifier = $state->getIdentifier();
		if (is_null($identifier)) {
			throw new IdentifierMissingException("verified identifier for $gatewayName is missing");
		}

		return $identifier;
	}

	/**
	 * Send out confirmation message and save current identifier in user settings
	 *
	 * @throws VerificationException
	 */
	public function startSetup(IUser $user, string $gatewayName, string $identifier): State {
		$identifier = trim($identifier);
		if ($identifier === '') {
			$identifier = $this->userPhoneService->getPhoneNumber($user);
		}
		if ($identifier === '') {
			throw new VerificationException($this->l10n->t('No phone number is configured for your account.'));
		}

		$now = $this->timeFactory->getTime();
		$currentState = $this->stateStorage->get($user, $gatewayName);
		if ($currentState->getState() === StateStorage::STATE_VERIFYING
			&& ($currentState->getResendAvailableAt() ?? 0) > $now) {
			$wait = ($currentState->getResendAvailableAt() ?? $now) - $now;
			throw new VerificationException($this->l10n->t('Please wait %s seconds before requesting a new code.', [(string)$wait]));
		}

		$verificationNumber = $this->random->generate(6, ISecureRandom::CHAR_DIGITS);
		$gateway = $this->gatewayFactory->get($gatewayName);
		try {
			$message = match ($gateway->getSettings()->allow_markdown ?? false) {
				true => $this->l10n->t('`%s` is your verification code.', [$verificationNumber]),
				default => $this->l10n->t('%s is your verification code.', [$verificationNumber]),
			};
			$gateway->send(
				$identifier,
				$message,
				['code' => $verificationNumber],
			);
		} catch (MessageTransmissionException $ex) {
			throw new VerificationException($ex->getMessage(), $ex->getCode(), $ex);
		}

		return $this->stateStorage->persist(
			State::verifying(
				$user,
				$gatewayName,
				$identifier,
				$verificationNumber,
				$now + self::CODE_TTL,
				$now + self::RESEND_COOLDOWN
			)
		);
	}

	public function finishSetup(IUser $user, string $gatewayName, string $token): State {
		$state = $this->stateStorage->get($user, $gatewayName);
		$now = $this->timeFactory->getTime();
		if (is_null($state->getVerificationCode())) {
			throw new VerificationException($this->l10n->t('no verification code set'));
		}
		if (($state->getExpiresAt() ?? 0) < $now) {
			$this->stateStorage->persist(State::disabled($user, $gatewayName));
			throw new VerificationException($this->l10n->t('The verification code has expired. Request a new one.'));
		}

		if ($state->getVerificationCode() !== $token) {
			$failedState = $state->withFailedAttempt();
			if ($failedState->getFailedAttempts() >= self::MAX_FAILED_ATTEMPTS) {
				$this->stateStorage->persist(State::disabled($user, $gatewayName));
				throw new VerificationException($this->l10n->t('Too many invalid attempts. Request a new code.'));
			}
			$this->stateStorage->persist($failedState);
			throw new VerificationException($this->l10n->t('verification token mismatch'));
		}

		try {
			$provider = $this->providerFactory->get($gatewayName);
		} catch (InvalidProviderException) {
			throw new VerificationException('Invalid provider');
		}
		$this->providerRegistry->enableProviderFor($provider, $user);

		try {
			return $this->stateStorage->persist(
				$state->verify()
			);
		} catch (Exception $e) {
			throw new VerificationException($e->getMessage());
		}
	}

	public function disable(IUser $user, string $gatewayName): State {
		try {
			$provider = $this->providerFactory->get($gatewayName);
		} catch (InvalidProviderException) {
			throw new VerificationException('Invalid provider');
		}
		$this->providerRegistry->enableProviderFor($provider, $user);
		$this->providerRegistry->disableProviderFor($provider, $user);

		try {
			return $this->stateStorage->persist(
				State::disabled($user, $gatewayName)
			);
		} catch (Exception $e) {
			throw new VerificationException($e->getMessage());
		}
	}
}

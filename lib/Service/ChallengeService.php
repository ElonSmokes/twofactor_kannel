<?php

declare(strict_types=1);

namespace OCA\TwoFactorKannel\Service;

use OCA\TwoFactorKannel\Exception\MessageTransmissionException;
use OCA\TwoFactorKannel\Provider\Gateway\IGateway;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\IL10N;
use OCP\ISession;
use OCP\Security\ISecureRandom;

class ChallengeService {
	private const RESEND_COOLDOWN = 90;
	private const CODE_TTL = 120;
	private const MAX_FAILED_ATTEMPTS = 5;

	public function __construct(
		private ISession $session,
		private ISecureRandom $secureRandom,
		private ITimeFactory $timeFactory,
		private IL10N $l10n,
	) {
	}

	private function getSessionKey(string $gatewayName): string {
		return 'twofactor_kannel_' . $gatewayName . '_secret';
	}

	private function getStateKey(string $gatewayName): string {
		return 'twofactor_kannel_' . $gatewayName . '_challenge_state';
	}

	public function getState(string $gatewayName): array {
		if (!$this->session->exists($this->getStateKey($gatewayName))) {
			return [];
		}

		$state = $this->session->get($this->getStateKey($gatewayName));
		return is_array($state) ? $state : [];
	}

	private function setState(string $gatewayName, array $state): void {
		$this->session->set($this->getStateKey($gatewayName), $state);
	}

	private function hashCode(string $code): string {
		return hash('sha256', $code);
	}

	private function verifyStoredCode(array $state, string $challenge): bool {
		$storedHash = $state['secret_hash'] ?? null;
		if (is_string($storedHash) && $storedHash !== '') {
			return hash_equals($storedHash, $this->hashCode($challenge));
		}

		$legacySecret = $state['secret'] ?? null;
		return is_string($legacySecret) && hash_equals($legacySecret, $challenge);
	}

	public function clear(string $gatewayName): void {
		$this->session->remove($this->getStateKey($gatewayName));
		$this->session->remove($this->getSessionKey($gatewayName));
	}

	public function getOrCreate(string $gatewayName, IGateway $gateway, string $identifier, bool $shouldResend): array {
		$state = $this->getState($gatewayName);
		$now = $this->timeFactory->getTime();

		if ($state === [] || (($state['expires_at'] ?? 0) < $now)) {
			return $this->issue($gatewayName, $gateway, $identifier);
		}

		if ($shouldResend && (($state['resend_available_at'] ?? 0) <= $now)) {
			return $this->issue($gatewayName, $gateway, $identifier);
		}

		return $state;
	}

	public function verify(string $gatewayName, string $challenge): bool {
		$state = $this->getState($gatewayName);
		$now = $this->timeFactory->getTime();
		if ($state === [] || (($state['expires_at'] ?? 0) < $now)) {
			$this->clear($gatewayName);
			return false;
		}

		$valid = $this->verifyStoredCode($state, $challenge);
		if ($valid) {
			$this->clear($gatewayName);
			return true;
		}

		$state['failed_attempts'] = (int)($state['failed_attempts'] ?? 0) + 1;
		if ($state['failed_attempts'] >= self::MAX_FAILED_ATTEMPTS) {
			$this->clear($gatewayName);
		} else {
			$this->setState($gatewayName, $state);
		}

		return false;
	}

	private function issue(string $gatewayName, IGateway $gateway, string $identifier): array {
		$secret = $this->secureRandom->generate(6, ISecureRandom::CHAR_DIGITS);
		$now = $this->timeFactory->getTime();

		$message = $gateway->getSettings()->allow_markdown ?? false
			? $this->l10n->t('`%s` is your Nextcloud authentication code', [$secret])
			: $this->l10n->t('%s is your Nextcloud authentication code', [$secret]);

		$gateway->send($identifier, $message, ['code' => $secret]);

		$state = [
			'secret_hash' => $this->hashCode($secret),
			'identifier' => $identifier,
			'expires_at' => $now + self::CODE_TTL,
			'resend_available_at' => $now + self::RESEND_COOLDOWN,
			'failed_attempts' => 0,
		];
		$this->setState($gatewayName, $state);
		$this->session->set($this->getSessionKey($gatewayName), $this->hashCode($secret));
		return $state;
	}
}

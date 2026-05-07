<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 OpenAI
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorKannel\Provider\Channel\SMS\Provider\Drivers;

use Exception;
use OCA\TwoFactorKannel\Exception\MessageTransmissionException;
use OCA\TwoFactorKannel\Provider\Channel\SMS\Provider\AProvider;
use OCA\TwoFactorKannel\Provider\FieldDefinition;
use OCA\TwoFactorKannel\Provider\Settings;
use OCP\Http\Client\IClient;
use OCP\Http\Client\IClientService;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @method string getUrl()
 * @method static setUrl(string $url)
 * @method string getUsername()
 * @method static setUsername(string $username)
 * @method string getPassword()
 * @method static setPassword(string $password)
 * @method string getSender()
 * @method static setSender(string $sender)
 */
class Kannel extends AProvider {
	private const HTTP_CONNECT_TIMEOUT = 5;
	private const HTTP_TIMEOUT = 15;

	/**
	 * Kannel `sendsms` response codes that indicate the message was accepted
	 * for delivery (success). See Kannel User's Guide §7.5 (HTTP Interface).
	 *  - 0: Message accepted for delivery
	 *  - 3: Queued for later delivery
	 */
	private const SUCCESS_PREFIXES = ['0:', '3:'];

	private IClient $client;
	private LoggerInterface $logger;

	public function __construct(
		IClientService $clientService,
		?LoggerInterface $logger = null,
	) {
		$this->client = $clientService->newClient();
		$this->logger = $logger ?? new NullLogger();
	}

	public function createSettings(): Settings {
		return new Settings(
			id: 'kannel',
			name: 'Kannel',
			instructions: 'Configure a direct Kannel sendsms HTTP endpoint.',
			fields: [
				new FieldDefinition(
					field: 'url',
					prompt: 'Please enter your Kannel sendsms URL:',
					default: 'http://10.10.248.62:13013/cgi-bin/sendsms',
				),
				new FieldDefinition(
					field: 'username',
					prompt: 'Please enter your Kannel username:',
				),
				new FieldDefinition(
					field: 'password',
					prompt: 'Please enter your Kannel password:',
				),
				new FieldDefinition(
					field: 'sender',
					prompt: 'Please enter your Kannel sender ID (optional):',
					optional: true,
				),
			]
		);
	}

	#[\Override]
	public function send(string $identifier, string $message) {
		$normalizedIdentifier = preg_replace('/\D+/', '', $identifier) ?? '';
		if ($normalizedIdentifier === '') {
			throw new MessageTransmissionException('Phone number is empty after normalization');
		}

		$url = $this->getUrl();
		$this->assertUrlSafe($url);

		// Credentials and OTP-bearing message body MUST be sent in the request body
		// (POST), not the URL query string, so they don't leak into Kannel access
		// logs or upstream proxy logs.
		$body = [
			'username' => $this->getUsername(),
			'password' => $this->getPassword(),
			'to' => '++' . $normalizedIdentifier,
			'text' => $message,
		];

		try {
			$sender = $this->getSender();
			if ($sender !== '') {
				$body['from'] = $sender;
			}
		} catch (\Throwable) {
			// Optional field not configured.
		}

		try {
			$response = $this->client->post($url, [
				'body' => $body,
				'connect_timeout' => self::HTTP_CONNECT_TIMEOUT,
				'timeout' => self::HTTP_TIMEOUT,
				'verify' => true,
			]);
		} catch (Exception $ex) {
			$this->logger->warning('Kannel sendsms request failed', [
				'app' => 'twofactor_kannel',
				'exception' => $ex,
			]);
			throw new MessageTransmissionException('Kannel request failed', $ex->getCode(), $ex);
		}

		$responseBody = trim((string)$response->getBody());
		$status = $response->getStatusCode();
		if ($status < 200 || $status >= 300 || !$this->isSuccessBody($responseBody)) {
			$this->logger->warning('Kannel sendsms rejected', [
				'app' => 'twofactor_kannel',
				'http_status' => $status,
				'body' => $responseBody,
			]);
			throw new MessageTransmissionException($responseBody !== '' ? $responseBody : 'Kannel rejected the SMS');
		}
	}

	private function assertUrlSafe(string $url): void {
		if ($url === '') {
			throw new MessageTransmissionException('Kannel URL is not configured');
		}
		$parts = parse_url($url);
		if (!is_array($parts) || !isset($parts['scheme'], $parts['host'])) {
			throw new MessageTransmissionException('Kannel URL is malformed');
		}
		$scheme = strtolower($parts['scheme']);
		if ($scheme !== 'http' && $scheme !== 'https') {
			throw new MessageTransmissionException('Kannel URL must use http or https');
		}
	}

	public static function isSuccessBody(string $body): bool {
		foreach (self::SUCCESS_PREFIXES as $prefix) {
			if (str_starts_with($body, $prefix)) {
				return true;
			}
		}
		return false;
	}
}

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
	private IClient $client;

	public function __construct(
		IClientService $clientService,
	) {
		$this->client = $clientService->newClient();
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
		$query = [
			'username' => $this->getUsername(),
			'password' => $this->getPassword(),
			'to' => $identifier,
			'text' => $message,
		];

		try {
			$sender = $this->getSender();
			if ($sender !== '') {
				$query['from'] = $sender;
			}
		} catch (\Throwable) {
			// Optional field not configured.
		}

		try {
			$response = $this->client->get($this->getUrl(), [
				'query' => $query,
			]);
		} catch (Exception $ex) {
			throw new MessageTransmissionException('Kannel request failed', $ex->getCode(), $ex);
		}

		$body = trim((string)$response->getBody());
		$status = $response->getStatusCode();
		if ($status < 200 || $status >= 300 || !str_starts_with($body, '0:')) {
			throw new MessageTransmissionException($body !== '' ? $body : 'Kannel rejected the SMS');
		}
	}
}

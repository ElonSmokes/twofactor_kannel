<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2024 André Matthies <a.matthies@sms77.io>
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
 * @method string getApiKey()
 * @method static setApiKey(string $apiKey)
 */
class Sms77Io extends AProvider {
	private IClient $client;

	public function __construct(
		IClientService $clientService,
	) {
		$this->client = $clientService->newClient();
	}

	public function createSettings(): Settings {
		return new Settings(
			id: 'sms77io',
			name: 'sms77.io',
			fields: [
				new FieldDefinition(
					field: 'api_key',
					prompt: 'Please enter your sms77.io API key:',
				),
			]
		);
	}

	#[\Override]
	public function send(string $identifier, string $message) {
		$apiKey = $this->getApiKey();
		try {
			$this->client->get('https://gateway.sms77.io/api/sms', [
				'query' => [
					'p' => $apiKey,
					'to' => $identifier,
					'text' => $message,
					'sendWith' => 'nextcloud'
				],
			]);
		} catch (Exception $ex) {
			throw new MessageTransmissionException();
		}
	}
}

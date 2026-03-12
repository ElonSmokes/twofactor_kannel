<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2024 Paweł Kuffel <pawel@kuffel.io>
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
 * @method string getLogin()
 * @method static setLogin(string $login)
 * @method string getPassword()
 * @method static setPassword(string $password)
 * @method string getSender()
 * @method static setSender(string $sender)
 */
class SerwerSMS extends AProvider {
	private IClient $client;

	public function __construct(
		IClientService $clientService,
	) {
		$this->client = $clientService->newClient();
	}

	public function createSettings(): Settings {
		return new Settings(
			id: 'serwersms',
			name: 'SerwerSMS',
			fields: [
				new FieldDefinition(
					field: 'login',
					prompt: 'Please enter your SerwerSMS.pl API login:',
				),
				new FieldDefinition(
					field: 'password',
					prompt: 'Please enter your SerwerSMS.pl API password:',
				),
				new FieldDefinition(
					field: 'sender',
					prompt: 'Please enter your SerwerSMS.pl sender name:',
				),
			],
		);
	}

	#[\Override]
	public function send(string $identifier, string $message) {
		$login = $this->getLogin();
		$password = $this->getPassword();
		$sender = $this->getSender();
		try {
			$response = $this->client->post('https://api2.serwersms.pl/messages/send_sms', [
				'headers' => [
					'Content-Type' => 'application/json',
				],
				'json' => [
					'username' => $login,
					'password' => $password,
					'phone' => $identifier,
					'text' => $message,
					'sender' => $sender,
				],
			]);

			$responseData = json_decode((string)$response->getBody(), true);

			if ($responseData['success'] !== true) {
				throw new MessageTransmissionException();
			}
		} catch (Exception $ex) {
			throw new MessageTransmissionException();
		}
	}
}

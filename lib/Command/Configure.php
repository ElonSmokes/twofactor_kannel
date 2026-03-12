<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2024 Christoph Wurst <christoph@winzerhof-wurst.at>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorKannel\Command;

use OCA\TwoFactorKannel\Exception\InvalidProviderException;
use OCA\TwoFactorKannel\Provider\Gateway\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Configure extends Command {
	public function __construct(
		private Factory $gatewayFactory,
	) {
		parent::__construct('twofactorauth:kannel:configure');
	}

	#[\Override]
	protected function execute(InputInterface $input, OutputInterface $output) {
		try {
			$gateway = $this->gatewayFactory->get('sms');
			return $gateway->cliConfigure($input, $output);
		} catch (InvalidProviderException $e) {
			$output->writeln('<error>Kannel SMS gateway is not available</error>');
			return 1;
		}
	}
}

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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Test extends Command {
	public function __construct(
		private Factory $gatewayFactory,
	) {
		parent::__construct('twofactorauth:kannel:test');

		$this->addArgument(
			'identifier',
			InputArgument::REQUIRED,
			'The SMS recipient phone number'
		);
	}

	#[\Override]
	protected function execute(InputInterface $input, OutputInterface $output) {
		$identifier = $input->getArgument('identifier');
		$gatewayName = 'sms';

		try {
			$gateway = $this->gatewayFactory->get($gatewayName);
			if (!$gateway->isComplete()) {
				$output->writeln('<error>Kannel SMS gateway is not configured</error>');
				return 1;
			}
		} catch (InvalidProviderException $e) {
			$output->writeln("<error>{$e->getMessage()}</error>");
			return 1;
		}

		$message = 'Test';

		$output->writeln('');
		$output->writeln('<info>════════════════════════════════════════════════════════════════</info>');
		$output->writeln('<info>  Two-Factor Kannel Test</info>');
		$output->writeln('<info>════════════════════════════════════════════════════════════════</info>');
		$output->writeln('');
		$output->writeln('  <comment>Gateway:</comment>     kannel');
		$output->writeln('  <comment>Recipient:</comment>   ' . $identifier);
		$output->writeln('  <comment>Message:</comment>     ' . $message);
		$output->writeln('');
		$output->writeln('<info>Sending message...</info>');

		$gateway->send($identifier, $message);

		$output->writeln('');
		$output->writeln('<info>✓ Message successfully sent!</info>');
		$output->writeln('');

		return 0;
	}
}

<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2024 Christoph Wurst <christoph@winzerhof-wurst.at>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorKannel\Command;

use OCA\TwoFactorKannel\Provider\Gateway\AGateway;
use OCA\TwoFactorKannel\Provider\Gateway\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Status extends Command {
	public function __construct(
		private Factory $gatewayFactory,
	) {
		parent::__construct('twofactorauth:kannel:status');
	}

	#[\Override]
	protected function execute(InputInterface $input, OutputInterface $output) {
		/** @var AGateway */
		$gateway = $this->gatewayFactory->get('sms');
		$isConfigured = $gateway->isComplete();
		$settings = $gateway->getSettings();
		$output->writeln($settings->name . ': ' . ($isConfigured ? 'configured' : 'not configured'));
		$output->write(json_encode($gateway->getConfiguration(), JSON_PRETTY_PRINT), true, OutputInterface::VERBOSITY_VERBOSE);
		return 0;
	}
}

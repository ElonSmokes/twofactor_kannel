<?php

declare(strict_types=1);

/**
 * Test bootstrap. The upstream vendor/ uses a frozen classmap that does not
 * include new files added in this branch, so we register a small PSR-4
 * autoloader on top of it for the OCA\TwoFactorKannel\ namespace and tests.
 */

$loader = require __DIR__ . '/../vendor/autoload.php';
spl_autoload_register(static function (string $class): void {
	$prefixes = [
		'OCA\\TwoFactorKannel\\Tests\\Unit\\' => __DIR__ . '/php/Unit/',
		'OCA\\TwoFactorKannel\\' => __DIR__ . '/../lib/',
	];
	foreach ($prefixes as $prefix => $base) {
		if (str_starts_with($class, $prefix)) {
			$path = $base . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
			if (is_file($path)) {
				require $path;
				return;
			}
		}
	}
});

#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Webmozart\Assert\Assert;

(function (): void {
	$container = require __DIR__ . '/container.php';
	Assert::isInstanceOf($container, ContainerInterface::class);

	$application = new Application();
	$application->setAutoExit(false);

	$config = $container->get('config');
	Assert::isArray($config);

	$laminasCli = $config['laminas-cli'] ?? [];
	Assert::isArray($laminasCli);

	$commands = $laminasCli['commands'] ?? [];
	Assert::isArray($commands);
	Assert::allString($commands);

	foreach ($commands as $alias => $serviceId) {
		$application->addCommand($container->get($serviceId));
	}

	$application->run();
})();

<?php

declare(strict_types=1);

use Laminas\ConfigAggregator\ConfigAggregator;

$aggregator = new ConfigAggregator([
	// your app config provider (where you define factories / commands)
	\VasilDakov\Doctrine\ConfigProvider::class,

	new Laminas\ConfigAggregator\PhpFileProvider(__DIR__ . '/doctrine.global.php'),
]);

return $aggregator->getMergedConfig();

<?php

declare(strict_types=1);

use Laminas\ConfigAggregator\ConfigAggregator;

$aggregator = new ConfigAggregator([
	\VasilDakov\Doctrine\ConfigProvider::class,
	new Laminas\ConfigAggregator\PhpFileProvider(__DIR__ . '/autoload/{{,*.}global,{,*.}local}.php'),
]);

return $aggregator->getMergedConfig();

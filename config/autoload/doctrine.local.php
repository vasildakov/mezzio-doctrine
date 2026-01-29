<?php

declare(strict_types=1);

return [
	'doctrine' => [
		'dev_mode' => true,
		'fixtures' => [
			'paths' => [
				__DIR__ . '/../../data/fixtures',
			],
		],
	],
];

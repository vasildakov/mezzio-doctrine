<?php

// doctrine.global.php (production)

declare(strict_types=1);

use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\ORM\Proxy\ProxyFactory;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;

return [
	'doctrine' => [
		'dev_mode'   => false,
		'connection' => [
			'orm_default' => [
				'params' => [
					'driver'   => 'pdo_mysql',
					'host'     => 'db',
					'port'     => 3306,
					'user'     => 'app_user',
					'password' => 'app_password',
					'dbname'   => 'app_db',
					'charset'  => 'utf8mb4',
				],
			],
		],
		'driver'     => [
			'orm_default' => [
				'class'   => MappingDriverChain::class,
				'drivers' => [
					'Entity' => 'app_entity',
				],
			],
			'app_entity' => [
				'class' => AttributeDriver::class,
				'paths' => ['Entity'],
			],
		],
		'proxy'      => [
			'dir'           => __DIR__ . '/../../data/cache/doctrine/proxies',
			'namespace'     => 'DoctrineProxies',
			'auto_generate' => ProxyFactory::AUTOGENERATE_NEVER,
		],
		'cache'      => [
			'type' => 'filesystem',
			'path' => __DIR__ . '/../../data/cache/doctrine',
		],
		'migrations' => [
			'orm_default' => [
				'directory'     => __DIR__ . '/../../data/migrations',
				'namespace'     => 'App\Migrations',
				'table_name'    => 'doctrine_migration_versions',
				'column_length' => 14,
			],
		],
		'fixtures'  => [
			'paths' => [
				__DIR__ . '/../../data/fixtures',
			],
		],
	],
];

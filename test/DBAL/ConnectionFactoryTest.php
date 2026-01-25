<?php

declare(strict_types=1);

namespace VasilDakov\DoctrineTest\DBAL;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use VasilDakov\Doctrine\DBAL\ConnectionFactory;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

class ConnectionFactoryTest extends TestCase
{
	public function testInvokeReturnsConnection(): void {
		$config = [
			'doctrine' => [
				'connection' => [
					'orm_default' => [
						'params' => [
							'url' => 'sqlite:///:memory:',
							'driver' => 'pdo_sqlite'
						]
					]
				]
			]
		];

		$container = $this->createMock(ContainerInterface::class);
		$container->method('get')
			->with('config')
			->willReturn($config);

		$factory = new ConnectionFactory();
		$connection = $factory($container);

		$this->assertInstanceOf(Connection::class, $connection);
	}

	public function testInvokeThrowsWhenConfigMissing(): void {
		$this->expectException(\RuntimeException::class);

		$config = [
			'doctrine' => [
				'connection' => [
					'orm_default' => [
						// 'params' missing
					]
				]
			]
		];

		$container = $this->createMock(ContainerInterface::class);
		$container->method('get')
			->with('config')
			->willReturn($config);

		$factory = new ConnectionFactory();
		$factory($container);
	}
}

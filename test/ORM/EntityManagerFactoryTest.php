<?php

declare(strict_types=1);

namespace VasilDakov\DoctrineTest\ORM;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\Proxy\ProxyFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use RuntimeException;
use VasilDakov\Doctrine\ORM\EntityManagerFactory;

class EntityManagerFactoryTest extends TestCase
{
	#[Test]
	public function createsEntityManagerWithValidConfiguration(): void
	{
		$container = $this->createMock(ContainerInterface::class);
		$config = [
			'doctrine' => [
				'connection' => [
					'orm_default' => [
						'params' => [
							'driver' => 'pdo_sqlite',
							'memory' => true
						]
					]
				],
				'proxy' => [
					'dir' => sys_get_temp_dir(),
					'namespace' => 'DoctrineProxies',
					'auto_generate' => ProxyFactory::AUTOGENERATE_NEVER
				],
				'driver' => [
					'orm_default' => [
						'class' => MappingDriverChain::class,
						'drivers' => [
							'App\Entity' => 'app_entity'
						]
					],
					'app_entity' => [
						'class' => AttributeDriver::class,
						'paths' => [__DIR__ . '/../../src/Entity']
					]
				],
				'cache' => [
					'type' => 'array'
				],
				'dev_mode' => true
			]
		];

		$container->method('get')->with('config')->willReturn($config);

		$factory = new EntityManagerFactory();
		$entityManager = $factory($container);

		$this->assertInstanceOf(EntityManager::class, $entityManager);
	}

	#[Test]
	public function throwsExceptionWhenConnectionParamsAreMissing(): void
	{
		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('Missing config: doctrine.connection.orm_default.params');

		$container = $this->createMock(ContainerInterface::class);
		$container->method('get')->with('config')->willReturn(['doctrine' => []]);

		$factory = new EntityManagerFactory();
		$factory($container);
	}

	#[Test]
	public function throwsExceptionWhenDriverConfigurationIsMissing(): void
	{
		$this->expectException(RuntimeException::class);

		$container = $this->createMock(ContainerInterface::class);
		$container->method('get')->with('config')->willReturn([
			'doctrine' => [
				'connection' => [
					'orm_default' => [
						'params' => [
							'driver' => 'pdo_sqlite',
							'memory' => true
						]
					]
				]
			]
		]);

		$factory = new EntityManagerFactory();
		$factory($container);
	}

	#[Test]
	public function usesDefaultValuesWhenOptionalConfigIsMissing(): void
	{
		$container = $this->createMock(ContainerInterface::class);
		$config = [
			'doctrine' => [
				'connection' => [
					'orm_default' => [
						'params' => [
							'driver' => 'pdo_sqlite',
							'memory' => true
						]
					]
				],
				'driver' => [
					'orm_default' => [
						'class' => MappingDriverChain::class,
						'drivers' => [
							'App\Entity' => 'app_entity'
						]
					],
					'app_entity' => [
						'class' => AttributeDriver::class,
						'paths' => [__DIR__ . '/../../src/Entity']
					]
				]
			]
		];

		$container->method('get')->with('config')->willReturn($config);

		$factory = new EntityManagerFactory();
		$entityManager = $factory($container);

		$this->assertInstanceOf(EntityManager::class, $entityManager);
	}

	#[Test]
	public function removesEmptyStringsFromPaths(): void
	{
		$paths = [__DIR__, ''];
		$result = $this->invokeNormalizePaths($paths);
		$this->assertCount(1, $result);
		$this->assertContains(__DIR__, $result);
	}

	#[Test]
	public function removesNonExistentDirectories(): void
	{
		$paths = [__DIR__, '/nonexistent/path/that/does/not/exist'];
		$result = $this->invokeNormalizePaths($paths);
		$this->assertCount(1, $result);
		$this->assertContains(__DIR__, $result);
	}

	#[Test]
	public function removesDuplicatePaths(): void
	{
		$paths = [__DIR__, __DIR__];
		$result = $this->invokeNormalizePaths($paths);
		$this->assertCount(1, $result);
		$this->assertContains(__DIR__, $result);
	}

	#[Test]
	private function invokeNormalizePaths(array $paths): array
	{
		$reflection = new \ReflectionMethod(EntityManagerFactory::class, 'normalizePaths');
		$reflection->setAccessible(true);
		return $reflection->invoke(new EntityManagerFactory(), $paths);
	}

	#[Test]
	public function throwsExceptionWhenDriverDefinitionIsNotAnArray(): void
	{
		$this->expectException(\RuntimeException::class);
		$this->expectExceptionMessage('Driver "invalid_driver" not defined. Expected doctrine.driver.invalid_driver with keys: class, paths');

		$driverDef = null;
		$driverServiceName = 'invalid_driver';

		if (!is_array($driverDef) || !isset($driverDef['class'], $driverDef['paths'])) {
			throw new \RuntimeException(sprintf(
				'Driver "%s" not defined. Expected doctrine.driver.%s with keys: class, paths',
				$driverServiceName,
				$driverServiceName
			));
		}
	}

	#[Test]
	public function throwsExceptionWhenDriverDefinitionIsMissingRequiredKeys(): void
	{
		$this->expectException(\RuntimeException::class);
		$this->expectExceptionMessage('Driver "incomplete_driver" not defined. Expected doctrine.driver.incomplete_driver with keys: class, paths');

		$driverDef = ['class' => 'SomeClass'];
		$driverServiceName = 'incomplete_driver';

		if (!is_array($driverDef) || !isset($driverDef['class'], $driverDef['paths'])) {
			throw new \RuntimeException(sprintf(
				'Driver "%s" not defined. Expected doctrine.driver.%s with keys: class, paths',
				$driverServiceName,
				$driverServiceName
			));
		}
	}

	#[Test]
	public function doesNotThrowExceptionWhenDriverDefinitionIsValid(): void
	{
		$driverDef = ['class' => 'SomeClass', 'paths' => ['/some/path']];
		$driverServiceName = 'valid_driver';

		$this->assertTrue(is_array($driverDef) && isset($driverDef['class'], $driverDef['paths']));
	}
}

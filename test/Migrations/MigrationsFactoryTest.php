<?php

declare(strict_types=1);

namespace VasilDakov\DoctrineTest\Migrations;

use Doctrine\Migrations\DependencyFactory;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use VasilDakov\Doctrine\Migrations\MigrationsFactory;

class MigrationsFactoryTest extends TestCase
{
	#[Test]
	public function invokeReturnsDependencyFactory(): void
	{
		$entityManager = $this->createMock(EntityManagerInterface::class);
		$container = $this->createMock(ContainerInterface::class);
		$container->method('get')
			->with(EntityManagerInterface::class)
			->willReturn($entityManager);

		$factory = new MigrationsFactory();
		$dependencyFactory = $factory($container);

		$this->assertInstanceOf(DependencyFactory::class, $dependencyFactory);
	}

	#[Test]
	public function invokeThrowsWhenEntityManagerNotFound(): void
	{
		$this->expectException(NotFoundExceptionInterface::class);

		$container = $this->createMock(ContainerInterface::class);
		$container->method('get')
			->with(EntityManagerInterface::class)
			->willThrowException($this->createMock(NotFoundExceptionInterface::class));

		$factory = new MigrationsFactory();
		$factory($container);
	}

	#[Test]
	public function invokeThrowsWhenContainerExceptionOccurs(): void
	{
		$this->expectException(ContainerExceptionInterface::class);

		$container = $this->createMock(ContainerInterface::class);
		$container->method('get')
			->with(EntityManagerInterface::class)
			->willThrowException($this->createMock(ContainerExceptionInterface::class));

		$factory = new MigrationsFactory();
		$factory($container);
	}
}

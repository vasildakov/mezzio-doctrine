<?php

declare(strict_types=1);

namespace VasilDakov\Doctrine\Migrations;

use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Webmozart\Assert\Assert;

final class MigrationsFactory
{
	public function __invoke(ContainerInterface $container): DependencyFactory
	{
		$entityManager = $container->get(EntityManagerInterface::class);
		Assert::isInstanceOf($entityManager, EntityManagerInterface::class);

		$configPath = $this->resolveMigrationsPhpPath();

		return DependencyFactory::fromEntityManager(
			new PhpFile($configPath),
			new ExistingEntityManager($entityManager),
		);
	}

	private function resolveMigrationsPhpPath(): string
	{
		/**
		 * Doctrine CLI rule:
		 * - migrations.php must live in project root
		 */
		$path = 'migrations.php';

		Assert::true(
			is_file($path) && is_readable($path),
			sprintf(
				'Doctrine Migrations requires a readable "%s" in the project root.',
				$path
			)
		);

		return $path;
	}
}

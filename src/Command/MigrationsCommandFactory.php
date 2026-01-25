<?php

declare(strict_types=1);

namespace VasilDakov\Doctrine\Command;

use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\ConfigurationArray;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Tools\Console\Command\DoctrineCommand;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class MigrationsCommandFactory implements FactoryInterface
{
	/**
	 * @inheritDoc
	 * @param ContainerInterface $container
	 * @param string $requestedName
	 * @param array|null $options
	 * @return DoctrineCommand
	 * @throws ContainerExceptionInterface
	 * @throws NotFoundExceptionInterface
	 */
	public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): DoctrineCommand
	{
		/** @var array $config */
		$config = $container->get('config')['doctrine']['migrations']['orm_default'];

		/** @var EntityManager $entityManager */
		$entityManager = $container->get(EntityManagerInterface::class);

		return new $requestedName(
			DependencyFactory::fromEntityManager(
				new ConfigurationArray($config),
				new ExistingEntityManager($entityManager)
			)
		);
	}
}

<?php

declare(strict_types=1);

namespace VasilDakov\Doctrine\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Command\Command;
use VasilDakov\Doctrine\Exception\ServiceNotFoundException;

/**
 * Factory for Doctrine ORM Console Commands
 */
final class DoctrineOrmCommandFactory
{
	/**
	 * @throws ContainerExceptionInterface
	 * @throws NotFoundExceptionInterface
	 */
	public function __invoke(ContainerInterface $container, string $requestedName): Command
	{
		if (! $container->has(EntityManagerInterface::class)) {
			throw new ServiceNotFoundException('EntityManagerInterface service is not available');
		}

		$em = $container->get(EntityManagerInterface::class);

		// ORM 3: commands require an EntityManagerProvider
		$provider = new SingleManagerProvider($em);

		// Most ORM commands accept the provider as 1st ctor arg
		return new $requestedName($provider);
	}
}

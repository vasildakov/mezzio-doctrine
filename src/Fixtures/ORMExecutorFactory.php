<?php

declare(strict_types=1);

namespace VasilDakov\Doctrine\Fixtures;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class ORMExecutorFactory
{
	/**
	 * @param ContainerInterface $container
	 * @return ORMExecutor
	 * @throws ContainerExceptionInterface
	 * @throws NotFoundExceptionInterface
	 */
	public function __invoke(ContainerInterface $container): ORMExecutor
	{
		$entityManager = $container->get(EntityManagerInterface::class);
		$purger = $container->get(ORMPurger::class);

		return new ORMExecutor($entityManager, $purger);
	}
}

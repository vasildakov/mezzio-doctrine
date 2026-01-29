<?php

declare(strict_types=1);

namespace VasilDakov\Doctrine\Command;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function array_values;

final class LoadFixturesCommandFactory
{
	/**
	 * @throws ContainerExceptionInterface
	 * @throws NotFoundExceptionInterface
	 */
	public function __invoke(ContainerInterface $container): LoadFixturesCommand
	{
		$config = $container->get('config');

		$paths = $config['doctrine']['fixtures']['paths'] ?? null;

		return new LoadFixturesCommand(
			$container->get(ORMExecutor::class),
			array_values($paths),
		);
	}
}

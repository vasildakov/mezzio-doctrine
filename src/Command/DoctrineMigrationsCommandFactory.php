<?php

declare(strict_types=1);

namespace VasilDakov\Doctrine\Command;

use Doctrine\Migrations\DependencyFactory;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Command\Command;
use Webmozart\Assert\Assert;

final class DoctrineMigrationsCommandFactory
{
	/**
	 * @throws ContainerExceptionInterface
	 * @throws NotFoundExceptionInterface
	 */
	public function __invoke(ContainerInterface $container, string $requestedName): Command
	{
		Assert::classExists($requestedName);
		Assert::subclassOf($requestedName, Command::class);

		$df = $container->get(DependencyFactory::class);
		Assert::isInstanceOf($df, DependencyFactory::class);

		/** @var Command $cmd */
		$cmd = new $requestedName($df);

		return $cmd;
	}
}

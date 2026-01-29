<?php

declare(strict_types=1);

namespace VasilDakov\Doctrine\ORM;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Webmozart\Assert\Assert;

final class SingleManagerProviderFactory
{
	/**
	 * @throws ContainerExceptionInterface
	 * @throws NotFoundExceptionInterface
	 */
	public function __invoke(ContainerInterface $container): SingleManagerProvider
	{
		$em = $container->get(EntityManagerInterface::class);
		Assert::isInstanceOf($em, EntityManagerInterface::class);

		return new SingleManagerProvider($em);
	}
}

<?php

declare(strict_types=1);

namespace VasilDakov\Doctrine\Fixtures;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Psr\Container\ContainerInterface;

final class ORMPurgerFactory
{
	/**
	 * @param ContainerInterface $container
	 * @return ORMPurger
	 */
	public function __invoke(ContainerInterface $container): ORMPurger
	{
		return new ORMPurger();
	}
}

<?php

declare(strict_types=1);

namespace VasilDakov\Doctrine\DBAL;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

/**
 * Factory for Doctrine DBAL Connection
 */
final class ConnectionFactory
{
	/**
	 * @throws ContainerExceptionInterface
	 * @throws NotFoundExceptionInterface
	 */
	public function __invoke(ContainerInterface $container): Connection {
		$config = $container->get('config');

		$connectionParams = $config['doctrine']['connection']['orm_default']['params']
			?? throw new RuntimeException('Doctrine connection config missing');

		return DriverManager::getConnection($connectionParams);
	}
}

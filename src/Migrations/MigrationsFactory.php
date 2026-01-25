<?php

declare(strict_types=1);

namespace VasilDakov\Doctrine\Migrations;

use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Factory for Doctrine Migrations DependencyFactory
 */
final class MigrationsFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): DependencyFactory
    {
        /** @todo from the doctrine config */
        $config = new PhpFile('config/migrations.php');

        return DependencyFactory::fromEntityManager(
            $config,
            new ExistingEntityManager($container->get(EntityManagerInterface::class))
        );
    }
}

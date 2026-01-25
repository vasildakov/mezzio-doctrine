<?php

declare(strict_types=1);

namespace VasilDakov\DoctrineTest\ORM;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\Proxy\ProxyFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use RuntimeException;
use VasilDakov\Doctrine\ORM\EntityManagerFactory;

class EntityManagerFactoryTest extends TestCase
{
    #[Test]
    public function createsEntityManagerWithValidConfiguration(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $config = [
            'doctrine' => [
                'connection' => [
                    'orm_default' => [
                        'params' => [
                            'driver' => 'pdo_sqlite',
                            'memory' => true
                        ]
                    ]
                ],
                'proxy' => [
                    'dir' => sys_get_temp_dir(),
                    'namespace' => 'DoctrineProxies',
                    'auto_generate' => ProxyFactory::AUTOGENERATE_NEVER
                ],
                'driver' => [
                    'orm_default' => [
                        'class' => MappingDriverChain::class,
                        'drivers' => [
                            'App\Entity' => 'app_entity'
                        ]
                    ],
                    'app_entity' => [
                        'class' => AttributeDriver::class,
                        'paths' => [__DIR__ . '/../../src/Entity']
                    ]
                ],
                'cache' => [
                    'type' => 'array'
                ],
                'dev_mode' => true
            ]
        ];

        $container->method('get')->with('config')->willReturn($config);

        $factory = new EntityManagerFactory();
        $entityManager = $factory($container);

        $this->assertInstanceOf(EntityManager::class, $entityManager);
    }

    #[Test]
    public function throwsExceptionWhenConnectionParamsAreMissing(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing config: doctrine.connection.orm_default.params');

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->with('config')->willReturn(['doctrine' => []]);

        $factory = new EntityManagerFactory();
        $factory($container);
    }

    #[Test]
    public function throwsExceptionWhenDriverConfigurationIsMissing(): void
    {
        $this->expectException(RuntimeException::class);

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->with('config')->willReturn([
            'doctrine' => [
                'connection' => [
                    'orm_default' => [
                        'params' => [
                            'driver' => 'pdo_sqlite',
                            'memory' => true
                        ]
                    ]
                ]
            ]
        ]);

        $factory = new EntityManagerFactory();
        $factory($container);
    }

    #[Test]
    public function usesDefaultValuesWhenOptionalConfigIsMissing(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $config = [
            'doctrine' => [
                'connection' => [
                    'orm_default' => [
                        'params' => [
                            'driver' => 'pdo_sqlite',
                            'memory' => true
                        ]
                    ]
                ],
                'driver' => [
                    'orm_default' => [
                        'class' => MappingDriverChain::class,
                        'drivers' => [
                            'App\Entity' => 'app_entity'
                        ]
                    ],
                    'app_entity' => [
                        'class' => AttributeDriver::class,
                        'paths' => [__DIR__ . '/../../src/Entity']
                    ]
                ]
            ]
        ];

        $container->method('get')->with('config')->willReturn($config);

        $factory = new EntityManagerFactory();
        $entityManager = $factory($container);

        $this->assertInstanceOf(EntityManager::class, $entityManager);
    }
}

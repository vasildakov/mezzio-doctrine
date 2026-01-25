<?php

declare(strict_types=1);

namespace VasilDakov\DoctrineTest\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use VasilDakov\Doctrine\Command\DoctrineOrmCommandFactory;
use VasilDakov\Doctrine\Exception\ServiceNotFoundException;

#[CoversClass(DoctrineOrmCommandFactory::class)]
final class DoctrineOrmCommandFactoryTest extends TestCase
{
    #[Test]
    public function canCreateOrmCommandWhenEntityManagerIsAvailable(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);

        $container = $this->createMock(ContainerInterface::class);
        $container
            ->method('has')
            ->with(EntityManagerInterface::class)
            ->willReturn(true);

        $container
            ->method('get')
            ->with(EntityManagerInterface::class)
            ->willReturn($em);

        $factory = new DoctrineOrmCommandFactory();

        $command = $factory($container, DummyOrmCommand::class);

        self::assertInstanceOf(DummyOrmCommand::class, $command);
        self::assertInstanceOf(SingleManagerProvider::class, $command->provider);
    }

    #[Test]
    public function itThrowsExceptionWhenEntityManagerIsMissing(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->method('has')
            ->with(EntityManagerInterface::class)
            ->willReturn(false);

        $factory = new DoctrineOrmCommandFactory();

        $this->expectException(ServiceNotFoundException::class);
        $this->expectExceptionMessage('EntityManagerInterface service is not available');

        $factory($container, DummyOrmCommand::class);
    }
}

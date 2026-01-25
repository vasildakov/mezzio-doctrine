<?php

declare(strict_types=1);

namespace VasilDakov\DoctrineTest\Command;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use VasilDakov\Doctrine\Command\LoadFixturesCommand;
use VasilDakov\Doctrine\Command\LoadFixturesCommandFactory;

class LoadFixturesCommandFactoryTest extends TestCase
{
    #[Test]
    public function invokesCommandWithValidConfiguration(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $container->expects($this->exactly(2))
            ->method('get')
            ->willReturnCallback(function ($id) use ($entityManager) {
                return match ($id) {
                    'config' => ['doctrine' => ['fixtures' => ['paths' => ['./data/fixtures']]]],
                    EntityManagerInterface::class => $entityManager,
                };
            });

        $factory = new LoadFixturesCommandFactory();
        $command = $factory($container);

        $this->assertInstanceOf(LoadFixturesCommand::class, $command);
    }

    #[Test]
    public function throwsExceptionWhenConfigIsMissing(): void
    {
        $this->expectException(NotFoundExceptionInterface::class);

        $container = $this->createMock(ContainerInterface::class);

        $container->expects($this->once())
            ->method('get')
            ->with('config')
            ->willThrowException($this->createMock(NotFoundExceptionInterface::class));

        $factory = new LoadFixturesCommandFactory();
        $factory($container);
    }

    #[Test]
    public function handlesEmptyFixturePathsGracefully(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $container->expects($this->exactly(2))
            ->method('get')
            ->willReturnCallback(function ($id) use ($entityManager) {
                return match ($id) {
                    'config' => ['doctrine' => ['fixtures' => ['paths' => []]]],
                    EntityManagerInterface::class => $entityManager,
                };
            });

        $factory = new LoadFixturesCommandFactory();
        $command = $factory($container);

        $this->assertInstanceOf(LoadFixturesCommand::class, $command);
    }
}

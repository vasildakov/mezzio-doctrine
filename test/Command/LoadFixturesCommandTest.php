<?php

declare(strict_types=1);

namespace VasilDakov\DoctrineTest\Command;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use VasilDakov\Doctrine\Command\LoadFixturesCommand;

class LoadFixturesCommandTest extends TestCase
{
	#[Test]
	public function executeLoadsFixturesSuccessfully(): void
	{
		$entityManager = $this->createMock(EntityManagerInterface::class);
		$fixturesPath = __DIR__ . '/../../data/fixtures';

		if (!is_dir($fixturesPath)) {
			mkdir($fixturesPath, 0777, true);
		}

		$command = new LoadFixturesCommand($entityManager, [$fixturesPath]);
		$commandTester = new CommandTester($command);

		$exitCode = $commandTester->execute([]);

		$this->assertEquals(Command::SUCCESS, $exitCode);
		$this->assertStringContainsString('Books fixtures loaded successfully!', $commandTester->getDisplay());

		if (is_dir($fixturesPath)) {
			rmdir($fixturesPath);
		}
	}

	#[Test]
	public function executeHandlesEmptyFixturePaths(): void
	{
		$entityManager = $this->createMock(EntityManagerInterface::class);

		$command = new LoadFixturesCommand($entityManager, []);
		$commandTester = new CommandTester($command);

		$exitCode = $commandTester->execute([]);

		$this->assertEquals(Command::SUCCESS, $exitCode);
		$this->assertStringContainsString('Books fixtures loaded successfully!', $commandTester->getDisplay());
	}

	#[Test]
	public function executeThrowsExceptionForInvalidFixturePath(): void
	{
		$this->expectException(\InvalidArgumentException::class);

		$entityManager = $this->createMock(EntityManagerInterface::class);

		$command = new LoadFixturesCommand($entityManager, ['invalid/path']);
		$commandTester = new CommandTester($command);

		$commandTester->execute([]);
	}
}

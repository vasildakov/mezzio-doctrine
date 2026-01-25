<?php

declare(strict_types=1);

namespace VasilDakov\DoctrineTest;

use Doctrine\DBAL\Connection;
use Doctrine\Migrations\Tools\Console\Command;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\Command as OrmCommand;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use VasilDakov\Doctrine\ConfigProvider;
use VasilDakov\Doctrine\DBAL\ConnectionFactory;
use VasilDakov\Doctrine\ORM\EntityManagerFactory;

class ConfigProviderTest extends TestCase
{
	#[Test]
	public function invokeReturnsExpectedConfiguration(): void
	{
		$configProvider = new ConfigProvider();
		$config = $configProvider();

		$this->assertIsArray($config);
		$this->assertArrayHasKey('dependencies', $config);
		$this->assertArrayHasKey('laminas-cli', $config);
		$this->assertArrayHasKey('commands', $config['laminas-cli']);
		$this->assertNotEmpty($config['dependencies']);
		$this->assertNotEmpty($config['laminas-cli']['commands']);
	}

	#[Test]
	public function dependenciesContainExpectedFactories(): void
	{
		$configProvider = new ConfigProvider();
		$config = $configProvider();
		$dependencies = $config['dependencies'];

		$this->assertArrayHasKey('factories', $dependencies);
		$this->assertArrayHasKey(Connection::class, $dependencies['factories']);
		$this->assertArrayHasKey(EntityManagerInterface::class, $dependencies['factories']);
		$this->assertArrayHasKey(OrmCommand\ValidateSchemaCommand::class, $dependencies['factories']);
		$this->assertSame(ConnectionFactory::class, $dependencies['factories'][Connection::class]);
		$this->assertSame(EntityManagerFactory::class, $dependencies['factories'][EntityManagerInterface::class]);
	}

	#[Test]
	public function commandsContainExpectedMappings(): void
	{
		$configProvider = new ConfigProvider();
		$config = $configProvider();
		$commands = $config['laminas-cli']['commands'];

		$this->assertArrayHasKey('doctrine:migrations:diff', $commands);
		$this->assertArrayHasKey('orm:schema-tool:create', $commands);
		$this->assertSame(Command\DiffCommand::class, $commands['doctrine:migrations:diff']);
		$this->assertSame(OrmCommand\SchemaTool\CreateCommand::class, $commands['orm:schema-tool:create']);
	}
}

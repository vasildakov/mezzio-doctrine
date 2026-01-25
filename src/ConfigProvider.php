<?php

declare(strict_types=1);

namespace VasilDakov\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\Migrations\Tools\Console\Command;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\Command as OrmCommand;
use VasilDakov\Doctrine\Command\DoctrineOrmCommandFactory;
use VasilDakov\Doctrine\Command\LoadFixturesCommand;
use VasilDakov\Doctrine\Command\LoadFixturesCommandFactory;
use VasilDakov\Doctrine\DBAL\ConnectionFactory;
use VasilDakov\Doctrine\ORM\EntityManagerFactory;

/**
 * The configuration provider for the Doctrine module
 */
final class ConfigProvider
{
	/**
	 * @return array<string, mixed>
	 */
	public function __invoke(): array
	{
		return [
			'dependencies' => $this->getDependencies(),
			'laminas-cli'  => [
				'commands' => [
					// Doctrine Migrations commands
					'doctrine:migrations:diff'                  => Command\DiffCommand::class,
					'doctrine:migrations:migrate'               => Command\MigrateCommand::class,
					'doctrine:migrations:status'                => Command\StatusCommand::class,
					'doctrine:migrations:list'                  => Command\ListCommand::class,
					'doctrine:migrations:execute'               => Command\ExecuteCommand::class,
					'doctrine:migrations:generate'              => Command\GenerateCommand::class,
					'doctrine:migrations:sync-metadata-storage' => Command\SyncMetadataCommand::class,
					'doctrine:migrations:version'               => Command\VersionCommand::class,
					'doctrine:migrations:dump-schema'           => Command\DumpSchemaCommand::class,

					// ORM commands (names match Doctrine’s CLI)
					'orm:clear-cache:metadata' => OrmCommand\ClearCache\MetadataCommand::class,
					'orm:clear-cache:query'    => OrmCommand\ClearCache\QueryCommand::class,
					'orm:clear-cache:result'   => OrmCommand\ClearCache\ResultCommand::class,
					'orm:generate-proxies'     => OrmCommand\GenerateProxiesCommand::class,
					'orm:run-dql'              => OrmCommand\RunDqlCommand::class,
					'orm:schema-tool:create'   => OrmCommand\SchemaTool\CreateCommand::class,
					'orm:schema-tool:drop'     => OrmCommand\SchemaTool\DropCommand::class,
					'orm:schema-tool:update'   => OrmCommand\SchemaTool\UpdateCommand::class,
					'orm:validate-schema'      => OrmCommand\ValidateSchemaCommand::class,

					// Doctrine Fixtures command
					'doctrine:fixtures:load' => LoadFixturesCommand::class,
				],
			],
		];
	}

	/**
	 * Returns the container dependencies
	 *
	 * @return array<string,mixed>
	 */
	public function getDependencies(): array
	{
		return [
			'invokables' => [],
			'factories'  => [
				// Doctrine DBAL Connection
				Connection::class => ConnectionFactory::class,

				// Doctrine ORM EntityManager
				EntityManagerInterface::class => EntityManagerFactory::class,

				// Fixtures
				LoadFixturesCommand::class => LoadFixturesCommandFactory::class,

				// Cache
				OrmCommand\ClearCache\MetadataCommand::class => DoctrineOrmCommandFactory::class,
				OrmCommand\ClearCache\QueryCommand::class    => DoctrineOrmCommandFactory::class,
				OrmCommand\ClearCache\ResultCommand::class   => DoctrineOrmCommandFactory::class,

				// Proxies / DQL
				OrmCommand\GenerateProxiesCommand::class => DoctrineOrmCommandFactory::class,
				OrmCommand\RunDqlCommand::class          => DoctrineOrmCommandFactory::class,

				// Schema tool
				OrmCommand\SchemaTool\CreateCommand::class => DoctrineOrmCommandFactory::class,
				OrmCommand\SchemaTool\DropCommand::class   => DoctrineOrmCommandFactory::class,
				OrmCommand\SchemaTool\UpdateCommand::class => DoctrineOrmCommandFactory::class,

				// Validate
				OrmCommand\ValidateSchemaCommand::class => DoctrineOrmCommandFactory::class,
			],
		];
	}
}

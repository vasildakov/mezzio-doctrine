<?php

declare(strict_types=1);

namespace VasilDakov\Doctrine;

use VasilDakov\Doctrine\Migrations\MigrationsFactory;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\DBAL\Connection;
use Doctrine\Migrations\Tools\Console\Command as MigrationsCommand;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\Command as OrmCommand;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;
use VasilDakov\Doctrine\Command\DoctrineOrmCommandFactory;
use VasilDakov\Doctrine\Command\LoadFixturesCommand;
use VasilDakov\Doctrine\Command\LoadFixturesCommandFactory;
use VasilDakov\Doctrine\DBAL\ConnectionFactory;
use VasilDakov\Doctrine\Fixtures\ORMExecutorFactory;
use VasilDakov\Doctrine\Fixtures\ORMPurgerFactory;
use VasilDakov\Doctrine\ORM\EntityManagerFactory;
use VasilDakov\Doctrine\ORM\SingleManagerProviderFactory;
use Doctrine\Migrations\DependencyFactory;
use VasilDakov\Doctrine\Command\DoctrineMigrationsCommandFactory;

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
					'doctrine:migrations:diff'                  => MigrationsCommand\DiffCommand::class,
					'doctrine:migrations:migrate'               => MigrationsCommand\MigrateCommand::class,
					'doctrine:migrations:status'                => MigrationsCommand\StatusCommand::class,
					'doctrine:migrations:list'                  => MigrationsCommand\ListCommand::class,
					'doctrine:migrations:execute'               => MigrationsCommand\ExecuteCommand::class,
					'doctrine:migrations:generate'              => MigrationsCommand\GenerateCommand::class,
					'doctrine:migrations:sync-metadata-storage' => MigrationsCommand\SyncMetadataCommand::class,
					'doctrine:migrations:version'               => MigrationsCommand\VersionCommand::class,
					'doctrine:migrations:dump-schema'           => MigrationsCommand\DumpSchemaCommand::class,

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
			'aliases'    => [
				'doctrine.entity_manager' => EntityManagerInterface::class,
			],
			'invokables' => [],
			'factories'  => [
				DependencyFactory::class => MigrationsFactory::class,
				// Doctrine DBAL Connection
				Connection::class => ConnectionFactory::class,

				// Doctrine ORM EntityManager
				EntityManagerInterface::class => EntityManagerFactory::class,
				SingleManagerProvider::class => SingleManagerProviderFactory::class,

				// Fixtures
				ORMPurger::class => ORMPurgerFactory::class,
				ORMExecutor::class => ORMExecutorFactory::class,
				LoadFixturesCommand::class => LoadFixturesCommandFactory::class,

				// ORM Commands
				OrmCommand\ClearCache\MetadataCommand::class => DoctrineOrmCommandFactory::class,
				OrmCommand\ClearCache\QueryCommand::class    => DoctrineOrmCommandFactory::class,
				OrmCommand\ClearCache\ResultCommand::class   => DoctrineOrmCommandFactory::class,
				OrmCommand\GenerateProxiesCommand::class     => DoctrineOrmCommandFactory::class,
				OrmCommand\RunDqlCommand::class              => DoctrineOrmCommandFactory::class,
				OrmCommand\SchemaTool\CreateCommand::class   => DoctrineOrmCommandFactory::class,
				OrmCommand\SchemaTool\DropCommand::class     => DoctrineOrmCommandFactory::class,
				OrmCommand\SchemaTool\UpdateCommand::class   => DoctrineOrmCommandFactory::class,
				OrmCommand\ValidateSchemaCommand::class      => DoctrineOrmCommandFactory::class,


				// Migrations commands
				MigrationsCommand\DiffCommand::class         => DoctrineMigrationsCommandFactory::class,
				MigrationsCommand\MigrateCommand::class      => DoctrineMigrationsCommandFactory::class,
				MigrationsCommand\StatusCommand::class       => DoctrineMigrationsCommandFactory::class,
				MigrationsCommand\ListCommand::class         => DoctrineMigrationsCommandFactory::class,
				MigrationsCommand\ExecuteCommand::class      => DoctrineMigrationsCommandFactory::class,
				MigrationsCommand\GenerateCommand::class     => DoctrineMigrationsCommandFactory::class,
				MigrationsCommand\SyncMetadataCommand::class => DoctrineMigrationsCommandFactory::class,
				MigrationsCommand\VersionCommand::class      => DoctrineMigrationsCommandFactory::class,
				MigrationsCommand\DumpSchemaCommand::class   => DoctrineMigrationsCommandFactory::class,
			],
		];
	}
}

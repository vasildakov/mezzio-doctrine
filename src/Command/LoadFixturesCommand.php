<?php

declare(strict_types=1);

namespace VasilDakov\Doctrine\Command;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class LoadFixturesCommand extends Command
{
	/**
	 * @param array<string> $fixturePaths
	 */
	public function __construct(
		private readonly EntityManagerInterface $entityManager,
		private readonly array $fixturePaths,
	) {
		parent::__construct();
	}

	protected function configure(): void
	{
		$this->setName('fixtures:load')
			->setDescription('Load data fixtures');
	}

	/** @SuppressWarnings("PHPMD.UnusedFormalParameter") */
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$loader = new Loader();
		foreach ($this->fixturePaths as $path) {
			$loader->loadFromDirectory($path);
		}

		//$loader->loadFromDirectory('src/App/src/Fixtures');

		$purger   = new ORMPurger();
		$executor = new ORMExecutor($this->entityManager, $purger);
		$executor->execute($loader->getFixtures());

		$output->writeln('<info>Books fixtures loaded successfully!</info>');
		return Command::SUCCESS;
	}
}

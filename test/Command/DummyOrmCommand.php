<?php

declare(strict_types=1);

namespace VasilDakov\DoctrineTest\Command;

use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use Symfony\Component\Console\Command\Command;

final class DummyOrmCommand extends Command
{
    /** @var string The name of the command */
    protected static $defaultName = 'test:dummy-orm';

    public EntityManagerProvider $provider;

    public function __construct(EntityManagerProvider $provider)
    {
        parent::__construct(self::$defaultName);

        $this->provider = $provider;
    }
}

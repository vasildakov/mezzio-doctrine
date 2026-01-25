# Doctrine ORM integration for Mezzio.

[![CI](https://github.com/vasildakov/mezzio-doctrine/actions/workflows/ci.yml/badge.svg?branch=main)](https://github.com/vasildakov/mezzio-doctrine/actions/workflows/ci.yml)
[![Coverage Status](https://coveralls.io/repos/github/vasildakov/mezzio-doctrine/badge.svg?branch=main)](https://coveralls.io/github/vasildakov/mezzio-doctrine?branch=develop)
[![Latest Stable Version](http://poser.pugx.org/vasildakov/mezzio-doctrine/v)](https://packagist.org/packages/vasildakov/mezzio-doctrine) 
[![Total Downloads](http://poser.pugx.org/vasildakov/mezzio-doctrine/downloads)](https://packagist.org/packages/vasildakov/mezzio-doctrine) 
[![Latest Unstable Version](http://poser.pugx.org/vasildakov/mezzio-doctrine/v/unstable)](https://packagist.org/packages/vasildakov/mezzio-doctrine) 
[![License](http://poser.pugx.org/vasildakov/mezzio-doctrine/license)](https://packagist.org/packages/vasildakov/mezzio-doctrine) 
[![PHP Version Require](http://poser.pugx.org/vasildakov/mezzio-doctrine/require/php)](https://packagist.org/packages/vasildakov/mezzio-doctrine)

## Synopsis

Doctrine ORM integration for Mezzio. This package provides integration with
Doctrine ORM 3.x, Doctrine Migrations, and Doctrine Fixtures.


## Installation

Install package via composer

```bash
composer require vasildakov/mezzio-doctrine
```

## Configuration

Include the ConfigProvider in your config aggregator:

```php
<?php
// In your config/config.php

$aggregator = new ConfigAggregator([
    // ...
    // Default App module config
    App\ConfigProvider::class,
    VasilDakov\Doctrine\ConfigProvider::class,
    // ...
]);
```


### Example Configuration

```php
<?php
declare(strict_types=1);

use Doctrine\DBAL\Driver\PDO\MySQL\Driver;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'driver_class' => Driver::class,
                'params' => [
                    'host'     => 'localhost',
                    'port'     => '3306',
                    'user'     => 'root',
                    'password' => 'root',
                    'dbname'   => 'database',
                ],
            ],
        ],
        
        'driver' => [
            'orm_default' => [
                'drivers' => [
                    'App\Entity' => [
                        'class' => AnnotationDriver::class,
                        'paths' => [
                            'src/Entity',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
```

## Doctrine ORM Commands

```shell
orm:clear-cache:metadata                   Clear all metadata cache of the various cache drivers
orm:clear-cache:query                      Clear all query cache of the various cache drivers
orm:clear-cache:result                     Clear all result cache of the various cache drivers
orm:generate-proxies                       [orm:generate:proxies] Generates proxy classes for entity classes
orm:run-dql                                Executes arbitrary DQL directly from the command line
orm:schema-tool:create                     Processes the schema and either create it directly on EntityManager Storage Connection or generate the SQL output
orm:schema-tool:drop                       Drop the complete database schema of EntityManager Storage Connection or generate the corresponding SQL output
orm:schema-tool:update                     Executes (or dumps) the SQL needed to update the database schema to match the current mapping metadata
orm:validate-schema                        Validate the mapping files
```

## Migrations Commands

Commands uses Laminas CLI and `vendor/bin/laminas` entry point for it

### Possible commands
```
doctrine:migrations:diff         [diff] Generate a migration by comparing your current database to your mapping information.
doctrine:migrations:dump-schema  [dump-schema] Dump the schema for your database to a migration.
doctrine:migrations:execute      [execute] Execute a single migration version up or down manually.
doctrine:migrations:generate     [generate] Generate a blank migration class.
doctrine:migrations:latest       [latest] Outputs the latest version number
doctrine:migrations:migrate      [migrate] Execute a migration to a specified version or the latest available version.
doctrine:migrations:rollup       [rollup] Rollup migrations by deleting all tracked versions and insert the one version that exists.
doctrine:migrations:status       [status] View the status of a set of migrations.
doctrine:migrations:up-to-date   [up-to-date] Tells you if your schema is up-to-date.
doctrine:migrations:version      [version] Manually add and delete migration versions from the version table.
```

### Example
```
$ vendor/bin/laminas doctrine:migrations:diff
$ vendor/bin/laminas doctrine:migrations:migrate
```

## Doctrine Fixtures Commands

```shell
doctrine:fixtures:load                     Load data fixtures
```

## Tests

Run the tests with code coverage
```bash
XDEBUG_MODE=coverage vendor/bin/phpunit 
```

## Author

Vasil Dakov - <vasildakov@gmail.com> - <https://vasildakov.com><br />

## License

Package is licensed under the MIT License - see the [LICENSE](LICENSE) file for details

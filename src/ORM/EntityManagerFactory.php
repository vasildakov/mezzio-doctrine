<?php

declare(strict_types=1);

namespace VasilDakov\Doctrine\ORM;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration as OrmConfiguration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Proxy\ProxyFactory;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

use function array_unique;
use function array_values;
use function is_array;
use function is_dir;
use function is_string;
use function sprintf;
use function sys_get_temp_dir;

/**
 * Factory for Doctrine ORM EntityManager
 */
final class EntityManagerFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): EntityManager
    {
        $config   = $container->get('config');
        $doctrine = $config['doctrine'] ?? [];

        // 1) DBAL Connection (from your config)
        $connParams = $doctrine['connection']['orm_default']['params']
            ?? throw new RuntimeException('Missing config: doctrine.connection.orm_default.params');

        $connection = DriverManager::getConnection($connParams);

        // 2) ORM Configuration
        $ormConfig = new OrmConfiguration();

        // Proxy config (from your config)
        $proxy = $doctrine['proxy'] ?? [];
        $ormConfig->setProxyDir($proxy['dir'] ?? sys_get_temp_dir());
        $ormConfig->setProxyNamespace($proxy['namespace'] ?? 'DoctrineProxies');
        $ormConfig->setAutoGenerateProxyClasses(
            $proxy['auto_generate'] ?? ProxyFactory::AUTOGENERATE_NEVER
        );

        // 3) Metadata driver chain (DoctrineModule-style)
        $ormDriverConfig = $doctrine['driver']['orm_default'] ?? null;
        if (! is_array($ormDriverConfig) || ($ormDriverConfig['class'] ?? null) !== MappingDriverChain::class) {
            throw new RuntimeException(
                'Missing/invalid config: doctrine.driver.orm_default (MappingDriverChain expected)'
            );
        }

        /** @var array<string,string> $namespaceMap */
        $namespaceMap = $ormDriverConfig['drivers'] ?? [];

        $driverChain = new MappingDriverChain();

        foreach ($namespaceMap as $namespace => $driverServiceName) {
            // driverServiceName is e.g. "app_entity"
            $driverDef = $doctrine['driver'][$driverServiceName] ?? null;

            if (! is_array($driverDef) || ! isset($driverDef['class'], $driverDef['paths'])) {
                throw new RuntimeException(sprintf(
                    'Driver "%s" not defined. Expected doctrine.driver.%s with keys: class, paths',
                    $driverServiceName,
                    $driverServiceName
                ));
            }

            $driverClass = $driverDef['class'];
            $paths       = (array) $driverDef['paths'];

            $driver = new $driverClass($paths);
            $driverChain->addDriver($driver, $namespace);
        }

        $ormConfig->setMetadataDriverImpl($driverChain);

        // 4) Cache (from your config)
        $devMode = (bool) ($doctrine['dev_mode'] ?? false);
        $cache   = $doctrine['cache'] ?? [];

        $cachePool = null;

        if ($devMode) {
            $cachePool = new ArrayAdapter();
        } else {
            $type = $cache['type'] ?? 'filesystem';

            if ($type === 'filesystem') {
                $path      = $cache['path'] ?? (sys_get_temp_dir() . '/doctrine-cache');
                $cachePool = new FilesystemAdapter(namespace: '', defaultLifetime: 0, directory: $path);
            } else {
                throw new RuntimeException(sprintf('Unsupported doctrine.cache.type "%s"', $type));
            }
        }

        $ormConfig->setMetadataCache($cachePool);
        $ormConfig->setQueryCache($cachePool);
        $ormConfig->setResultCache($cachePool);

        return new EntityManager($connection, $ormConfig);
    }

    /**
     * Resolve attribute metadata paths for:
     * - simple config: doctrine.driver.orm_default.paths
     * - driver chain: doctrine.driver.orm_default.drivers + doctrine.driver.<name>.paths
     *
     * @return string[]
     */
    private function resolveAttributePaths(array $doctrineConfig): array
    {
        $drivers    = $doctrineConfig['driver'] ?? [];
        $ormDefault = $drivers['orm_default'] ?? [];

        // Simple config: orm_default.paths
        $paths = $ormDefault['paths'] ?? null;
        if (is_array($paths) && $paths !== []) {
            return $this->normalizePaths($paths);
        }

        // Driver chain config: orm_default.drivers -> driverName -> paths
        $map = $ormDefault['drivers'] ?? null;
        if (! is_array($map) || $map === []) {
            throw new RuntimeException(
                'Doctrine mapping paths not found. Expected doctrine.driver.orm_default.paths.'
            );
        }

        $collected = [];
        foreach ($map as $namespace => $driverName) {
            if (! is_string($driverName) || $driverName === '') {
                continue;
            }

            $driverCfg   = $drivers[$driverName] ?? null;
            $driverPaths = is_array($driverCfg) ? ($driverCfg['paths'] ?? null) : null;

            if (is_array($driverPaths)) {
                foreach ($driverPaths as $p) {
                    $collected[] = $p;
                }
            }
        }

        $collected = $this->normalizePaths($collected);

        if ($collected === []) {
            throw new RuntimeException(
                'Doctrine mapping paths resolved to an empty list. Check doctrine.driver.*.paths.'
            );
        }

        return $collected;
    }

    /**
     * @param mixed[] $paths
     * @return string[]
     */
    private function normalizePaths(array $paths): array
    {
        $out = [];

        foreach ($paths as $p) {
            if (! is_string($p) || $p === '') {
                continue;
            }

            // keep existing real dirs; ignore missing to avoid fatals
            if (is_dir($p)) {
                $out[] = $p;
            }
        }

        return array_values(array_unique($out));
    }
}

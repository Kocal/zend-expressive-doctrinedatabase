<?php

namespace Kocal\Expressive\Database\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Kocal\Expressive\Database\DatabaseFactoryInterface;
use Psr\Container\ContainerInterface;

/**
 * Class EloquentDatabaseFactory
 */
class EntityManagerFactory implements DatabaseFactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @return EntityManager
     */
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config');
        $dbParams = isset($config['doctrine']) ? $config['doctrine'] : [];
        $doctrineConfig = Setup::createAnnotationMetadataConfiguration($config['entities_path'], $config['debug'], null, null, false);

        return EntityManager::create($dbParams, $doctrineConfig);
    }
}
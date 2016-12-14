<?php   // bootstrap.php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/config/config.php';

/**
 * Genera el gestor de entidades
 *
 * @return Doctrine\ORM\EntityManager
 */
function getEntityManager()
{
    // Cargar configuración de la conexión
    $dbParams = array(
        'host'      => DATABASE_HOST,
        'port'      => DATABASE_PORT,
        'dbname'    => DATABASE_DBNAME,
        'user'      => DATABASE_USER,
        'password'  => DATABASE_PASSWD,
        'driver'    => DATABASE_DRIVER,
        'charset'   => DATABASE_CHARSET
    );

    $config = Setup::createAnnotationMetadataConfiguration(
        array(ENTITY_DIR),  // paths to mapped entities
        DEBUG,              // developper mode
        PROXY_DIR,          // Proxy dir
        null,               // Cache implementation
        false               // use Simple Annotation Reader
    );
    $config->setAutoGenerateProxyClasses(DEBUG);
    if (DEBUG) {
        $config->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());
    }

    return EntityManager::create($dbParams, $config);
}

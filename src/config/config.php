<?php   // config/config.php

/*
 * configuración SGBD
 */
define('DATABASE_HOST', '127.0.0.1');
define('DATABASE_PORT', 3306);
define('DATABASE_DBNAME', 'doctrine');
define('DATABASE_USER', 'root');
define('DATABASE_PASSWD', '');
define('DATABASE_DRIVER', 'pdo_mysql');
define('DATABASE_CHARSET', 'UTF8');

/*
 * configuración Doctrine
 */
define('PROXY_DIR', '/xampp/tmp');
define('ENTITY_DIR', __DIR__ . '/../models');
define('DEBUG', false);  // muestra consulta SQL por la salida estándar

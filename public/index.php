<?php   // public/index.php

if (PHP_SAPI == 'cli-server') {
    /**
     * To help the built-in PHP dev server, check if the request was actually for
     * something which should probably be served as a static file.
     */
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];

    if (is_file($file)) {
        return false;
    }
}

require_once __DIR__ . '/../bootstrap.php';

session_start();

/**
 * Instantiate the app
 */
$settings = require __DIR__ . '/../src/settings.php';

/**
 * Slim app
 * @var \Slim\App $app
 */
$app = new \Slim\App($settings);

/**
 * Set up dependencies
 */
require __DIR__ . '/../src/dependencies.php';

/**
 * Register middleware
 */
require __DIR__ . '/../src/middleware.php';

/**
 * Register routes
 */
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../src/routes/MainRoutesProvider.php';
require_once __DIR__ . '/../src/routes/UserRoutesProvider.php';
require_once __DIR__ . '/../src/routes/ResultRoutesProvider.php';

use \MiW16\Results\Routes\MainRoutesProvider;
use \MiW16\Results\Routes\UserRoutesProvider;
use \MiW16\Results\Routes\ResultRoutesProvider;

$mainRoutesProvider = new MainRoutesProvider($app);
$userRoutesProvider = new UserRoutesProvider($app);
$resultRoutesProvider = new ResultRoutesProvider($app);

$mainRoutesProvider->defineRoutes();
$userRoutesProvider->defineRoutes();
$resultRoutesProvider->defineRoutes();

/**
 * Run app
 */
$app->run();
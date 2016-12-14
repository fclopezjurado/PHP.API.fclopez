<?php // src/dependencies.php

// DIC configuration

$container = $app->getContainer();

// view renderer
/** @var Interop\Container\ContainerInterface $c */
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
/** @var Interop\Container\ContainerInterface $c */
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(
        new Monolog\Processor\UidProcessor()
    );
    $logger->pushHandler(
        new Monolog\Handler\StreamHandler(
            $settings['path'],
            $settings['level']
        )
    );
    return $logger;
};

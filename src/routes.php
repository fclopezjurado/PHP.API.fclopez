<?php // apiResultsDoctrine - src/routes.php

require_once __DIR__ . '/../bootstrap.php';

require 'routes_user.php';
require 'routes_results.php';

/**  @var \Slim\App $app */
$app->get(
    '/',
    function ($request, $response, $args) {

        return $response
            ->withStatus(302)
            ->withHeader('Location', '/api-docs/index.html');
    }
);
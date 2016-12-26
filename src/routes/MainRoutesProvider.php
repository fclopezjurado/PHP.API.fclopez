<?php

/**
 * Created by PhpStorm.
 * User: fran lopez
 * Date: 25/12/2016
 * Time: 17:38
 */

namespace MiW16\Results\Routes;

class MainRoutesProvider
{
    private $app;

    const ROOT_PATH = '/';
    const SWAGGER_CLIENT_PATH = '../public/api-docs/index.html';
    const FOUND_RESPONSE_CODE = 302;
    const ROOT_PATH_FUNCTION_NAME = __CLASS__ . ':rootPath';

    /**
     * MainRoutesProvider constructor.
     * @param \Slim\App $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args
     * @return \Slim\Http\Response
     */
    public static function rootPath($request, $response, $args)
    {
        return $response->withStatus(MainRoutesProvider::FOUND_RESPONSE_CODE)
            ->withHeader('Location', MainRoutesProvider::SWAGGER_CLIENT_PATH);
    }

    /**
     *
     */
    public function defineRoutes()
    {
        $this->app->get(
            MainRoutesProvider::ROOT_PATH,
            MainRoutesProvider::ROOT_PATH_FUNCTION_NAME
        );
    }
}
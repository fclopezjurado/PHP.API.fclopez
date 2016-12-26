<?php

/**
 * Created by PhpStorm.
 * User: fran lopez
 * Date: 25/12/2016
 * Time: 17:41
 */

namespace MiW16\Results\Routes;

use MiW16\Results\Controllers\ResultController;
use MiW16\Results\Controllers\UserController;
use MiW16\Results\Models\Result;
use MiW16\Results\Models\User;
use Monolog\Logger;
use Slim\Views\PhpRenderer;
use Swagger\Annotations as SWG;
use Monolog\Handler\StreamHandler;

class ResultRoutesProvider
{
    const GET_RESULTS_PATH = '/results';
    const GET_RESULT_BY_ID_PATH = '/results/{id:[0-9]+}';
    const DELETE_RESULT_BY_ID_PATH = '/results/{id:[0-9]+}';
    const CREATE_RESULT_PATH = '/results';
    const UPDATE_RESULT_PATH = '/results/{id:[0-9]+}';

    const GET_RESULTS_LOGGER = 'GET \'/results\'';
    const GET_RESULT_BY_ID_LOGGER = 'GET \'/results/{id}\'';
    const DELETE_RESULT_BY_ID_LOGGER = 'DELETE \'/results/{id}\'';
    const OPTIONS_LOGGER = 'OPTIONS \'/results\'';
    const CREATE_RESULT_LOGGER = 'POST \'/results\'';
    const UPDATE_RESULT_LOGGER = 'PUT \'/results\'';

    const GET_RESULTS_PATH_TITLE = 'miw_get_all_results';
    const GET_RESULT_BY_ID_PATH_TITLE = 'miw_get_result_by_id';
    const DELETE_RESULT_BY_ID_PATH_TITLE = 'miw_delete_result_by_id';
    const GET_OPTIONS_PATH_TITLE = 'miw_options_results';
    const CREATE_RESULT_PATH_TITLE = 'miw_create_result';
    const UPDATE_RESULT_PATH_TITLE = 'miw_update_result';

    const GET_ALL_RESULTS_FUNCTION_NAME =  __CLASS__ . ':getAll';
    const GET_RESULT_BY_ID_FUNCTION_NAME =  __CLASS__ . ':getByID';
    const DELETE_RESULT_BY_ID_FUNCTION_NAME =  __CLASS__ . ':deleteByID';
    const GET_OPTIONS_FUNCTION_NAME =  __CLASS__ . ':options';
    const CREATE_RESULT_FUNCTION_NAME =  __CLASS__ . ':create';
    const UPDATE_RESULT_FUNCTION_NAME =  __CLASS__ . ':update';

    const NOT_FOUND_RESPONSE_CODE = 404;
    const NO_CONTENT_RESPONSE_CODE = 204;
    const CREATED_RESPONSE_CODE = 201;
    const BAD_REQUEST_RESPONSE_CODE = 400;
    const UNPROCESSABLE_ENTITY_RESPONSE_CODE = 422;
    const OK_RESPONSE_CODE = 200;

    const USER_NOT_FOUND = 'User not found';
    const RESULT_NOT_FOUND = 'Result not found';
    const RESULTS_NOT_FOUND = 'Results not found';
    const USER_ID_OR_RESULT_IS_LEFT_OUT = 'User ID or result is left out';
    const RESULT_IS_LEFT_OUT = "Result field is left out";
    const USER_ID_IS_LEFT_OUT = 'User ID is left out';

    const CODE_RESPONSE_ATTRIBUTE = 'code';
    const MESSAGE_RESPONSE_ATTRIBUTE = 'message';
    const RESULTS_RESPONSE_ATTRIBUTE = 'results';

    const TEMPLATES_PATH = __DIR__ . '/../../templates';
    const LOG_PATH = '../logs/app.log';
    const TEMPLATE = 'message.phtml';

    const LOGGER_ID = 'MiW16';

    private $app;
    private $logger;
    private $resultController;
    private $userController;
    private $view;

    /**
     * ResultRoutesProvider constructor.
     * @param \Slim\App $app
     */
    public function __construct($app)
    {
        $this->app = $app;
        $this->logger = new Logger(ResultRoutesProvider::LOGGER_ID);
        $this->resultController = new ResultController();
        $this->userController = new UserController();
        $this->view = new PhpRenderer(ResultRoutesProvider::TEMPLATES_PATH);

        $this->logger->pushHandler(new StreamHandler(ResultRoutesProvider::LOG_PATH, Logger::INFO));
    }

    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args
     * @return \Slim\Http\Response|\Psr\Http\Message\ResponseInterface
     */
    public function getAll($request, $response, $args)
    {
        $this->logger->info(ResultRoutesProvider::GET_RESULTS_LOGGER);
        $results = $this->resultController->getAll();

        if (empty($results)) {
            $data = [ResultRoutesProvider::CODE_RESPONSE_ATTRIBUTE => ResultRoutesProvider::NOT_FOUND_RESPONSE_CODE,
                ResultRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE => ResultRoutesProvider::RESULTS_NOT_FOUND];

            return $this->view->render($response->withStatus(ResultRoutesProvider::NOT_FOUND_RESPONSE_CODE),
                ResultRoutesProvider::TEMPLATE, $data);
        }

        return $response->withJson(array(ResultRoutesProvider::RESULTS_RESPONSE_ATTRIBUTE => $results));
    }

    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args
     *
     * @return \Slim\Http\Response|\Psr\Http\Message\ResponseInterface
     */
    public function getByID($request, $response, $args)
    {
        $this->logger->info(str_replace('{' . Result::ID_ATTRIBUTE . '}', $args[Result::ID_ATTRIBUTE],
            ResultRoutesProvider::GET_RESULT_BY_ID_LOGGER));

        $result = $this->resultController->getByID($args);

        if (empty($result)) {
            $data = [ResultRoutesProvider::CODE_RESPONSE_ATTRIBUTE => ResultRoutesProvider::NOT_FOUND_RESPONSE_CODE,
                ResultRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE => ResultRoutesProvider::RESULT_NOT_FOUND];

            return $this->view->render($response->withStatus(ResultRoutesProvider::NOT_FOUND_RESPONSE_CODE),
                ResultRoutesProvider::TEMPLATE, $data);
        }

        return $response->withJson($result);
    }

    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args
     *
     * @return \Slim\Http\Response|\Psr\Http\Message\ResponseInterface
     */
    public function deleteByID($request, $response, $args)
    {
        $this->logger->info(str_replace('{' . Result::ID_ATTRIBUTE . '}', $args[Result::ID_ATTRIBUTE],
            ResultRoutesProvider::DELETE_RESULT_BY_ID_LOGGER));

        $result = $this->resultController->getByID($args);

        if (empty($result)) {
            $data = [ResultRoutesProvider::CODE_RESPONSE_ATTRIBUTE => ResultRoutesProvider::NOT_FOUND_RESPONSE_CODE,
                ResultRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE => ResultRoutesProvider::RESULT_NOT_FOUND];

            return $this->view->render($response->withStatus(ResultRoutesProvider::NOT_FOUND_RESPONSE_CODE),
                ResultRoutesProvider::TEMPLATE, $data);
        } else
            $this->resultController->deleteByID($args);

        return $response->withStatus(ResultRoutesProvider::NO_CONTENT_RESPONSE_CODE);
    }

    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args
     *
     * @return \Slim\Http\Response
     */
    public function options($request, $response, $args)
    {
        $this->logger->info(ResultRoutesProvider::OPTIONS_LOGGER);

        return $response
            ->withHeader(
                'Allow',
                'OPTIONS, GET, POST, PUT, DELETE'
            );
    }

    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args
     *
     * @return \Slim\Http\Response|\Psr\Http\Message\ResponseInterface
     */
    public function create($request, $response, $args)
    {
        $this->logger->info(ResultRoutesProvider::CREATE_RESULT_LOGGER);
        $data = $request->getParsedBody();

        if ((!array_key_exists(ResultController::USER_ID_REQUEST_PARAMETER, $data)
                || !array_key_exists(ResultController::RESULT_REQUEST_PARAMETER, $data)
            ) || (intval($data[ResultController::USER_ID_REQUEST_PARAMETER]) === 0)
        ) {
            $data = [ResultRoutesProvider::CODE_RESPONSE_ATTRIBUTE =>
                ResultRoutesProvider::UNPROCESSABLE_ENTITY_RESPONSE_CODE,
                ResultRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE =>
                    ResultRoutesProvider::USER_ID_OR_RESULT_IS_LEFT_OUT];

            return $this->view->render($response->withStatus(ResultRoutesProvider::UNPROCESSABLE_ENTITY_RESPONSE_CODE),
                ResultRoutesProvider::TEMPLATE, $data);
        }

        $userInfo = array();
        $userInfo[User::ID_ATTRIBUTE] = $data[ResultController::USER_ID_REQUEST_PARAMETER];

        $user = $this->userController->getByID($userInfo);

        if (is_null($user)) {
            $data = [ResultRoutesProvider::CODE_RESPONSE_ATTRIBUTE => ResultRoutesProvider::BAD_REQUEST_RESPONSE_CODE,
                ResultRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE => ResultRoutesProvider::USER_NOT_FOUND];

            return $this->view->render($response->withStatus(ResultRoutesProvider::BAD_REQUEST_RESPONSE_CODE),
                ResultRoutesProvider::TEMPLATE, $data);
        }

        $result = $this->resultController->create($data);

        return $response->withStatus(ResultRoutesProvider::CREATED_RESPONSE_CODE)->withJson($result);
    }

    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args
     *
     * @return \Slim\Http\Response|\Psr\Http\Message\ResponseInterface
     */
    public function update($request, $response, $args)
    {
        $this->logger->info(ResultRoutesProvider::UPDATE_RESULT_LOGGER);

        $resultID = $args[Result::ID_ATTRIBUTE];
        $data = $request->getParsedBody();
        $data[Result::ID_ATTRIBUTE] = $resultID;
        $result = $this->resultController->getByID($args);

        if (!array_key_exists(ResultController::RESULT_REQUEST_PARAMETER, $data)) {
            $data = [ResultRoutesProvider::CODE_RESPONSE_ATTRIBUTE =>
                ResultRoutesProvider::UNPROCESSABLE_ENTITY_RESPONSE_CODE,
                ResultRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE =>
                    ResultRoutesProvider::RESULT_IS_LEFT_OUT];

            return $this->view->render($response->withStatus(ResultRoutesProvider::UNPROCESSABLE_ENTITY_RESPONSE_CODE),
                ResultRoutesProvider::TEMPLATE, $data);
        }

        if (empty($result)) {
            $data = [ResultRoutesProvider::CODE_RESPONSE_ATTRIBUTE => ResultRoutesProvider::NOT_FOUND_RESPONSE_CODE,
                ResultRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE => ResultRoutesProvider::RESULT_NOT_FOUND];

            return $this->view->render($response->withStatus(ResultRoutesProvider::NOT_FOUND_RESPONSE_CODE),
                ResultRoutesProvider::TEMPLATE, $data);
        }

        if (array_key_exists(ResultController::USER_ID_REQUEST_PARAMETER, $data)) {
            $userInfo = array();
            $userInfo[User::ID_ATTRIBUTE] = $data[ResultController::USER_ID_REQUEST_PARAMETER];

            $user = $this->userController->getByID($userInfo);

            if (is_null($user)) {
                $data = [ResultRoutesProvider::CODE_RESPONSE_ATTRIBUTE =>
                    ResultRoutesProvider::BAD_REQUEST_RESPONSE_CODE,
                    ResultRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE => ResultRoutesProvider::USER_NOT_FOUND];

                return $this->view->render($response->withStatus(ResultRoutesProvider::BAD_REQUEST_RESPONSE_CODE),
                    ResultRoutesProvider::TEMPLATE, $data);
            }
        }

        $result = $this->resultController->update($data);

        return $response->withStatus(ResultRoutesProvider::OK_RESPONSE_CODE)->withJson($result);
    }

    /**
     *
     */
    public function defineRoutes()
    {
        /**
         * Summary: Returns all results
         * Notes: Returns all results from the system that the user has access to.
         *
         * @SWG\Get(
         *     method      = "GET",
         *     path        = "/results",
         *     tags        = { "Results" },
         *     summary     = "Returns all results",
         *     description = "Returns all results from the system that the user has access to.",
         *     operationId = "miw_get_all_results",
         *     @SWG\Response(
         *          response    = 200,
         *          description = "Result array response",
         *          schema      = { "$ref": "#/definitions/ResultsArray" }
         *     ),
         *     @SWG\Response(
         *          response    = 404,
         *          description = "Result object not found",
         *          schema      = { "$ref": "#/definitions/Message" }
         *     )
         * )
         */
        $this->app->get(
            ResultRoutesProvider::GET_RESULTS_PATH,
            ResultRoutesProvider::GET_ALL_RESULTS_FUNCTION_NAME
        )->setName(ResultRoutesProvider::GET_RESULTS_PATH_TITLE);

        /**
         * Summary: Returns a result based on a single ID
         * Notes: Returns the result identified by &#x60;resultId&#x60;.
         *
         * @SWG\Get(
         *     method      = "GET",
         *     path        = "/results/{resultId}",
         *     tags        = { "Results" },
         *     summary     = "Returns a result based on a single ID",
         *     description = "Returns the result identified by `resultId`.",
         *     operationId = "miw_get_result_by_id",
         *     parameters  = {
         *          { "$ref" = "#/parameters/resultId" }
         *     },
         *     @SWG\Response(
         *          response    = 200,
         *          description = "Result",
         *          schema      = { "$ref": "#/definitions/Result" }
         *     ),
         *     @SWG\Response(
         *          response    = 404,
         *          description = "Result id. not found",
         *          schema      = { "$ref": "#/definitions/Message" }
         *     )
         * )
         */
        $this->app->get(
            ResultRoutesProvider::GET_RESULT_BY_ID_PATH,
            ResultRoutesProvider::GET_RESULT_BY_ID_FUNCTION_NAME
        )->setName(ResultRoutesProvider::GET_RESULT_BY_ID_PATH_TITLE);

        /**
         * Summary: Deletes a result
         * Notes: Deletes the result identified by &#x60;resultId&#x60;.
         *
         * @SWG\Delete(
         *     method      = "DELETE",
         *     path        = "/results/{resultId}",
         *     tags        = { "Results" },
         *     summary     = "Deletes a result",
         *     description = "Deletes the result identified by `resultId`.",
         *     operationId = "miw_delete_result_by_id",
         *     parameters={
         *          { "$ref" = "#/parameters/resultId" }
         *     },
         *     @SWG\Response(
         *          response    = 204,
         *          description = "Result deleted &lt;Response body is empty&gt;"
         *     ),
         *     @SWG\Response(
         *          response    = 404,
         *          description = "Result not found",
         *          schema      = { "$ref": "#/definitions/Message" }
         *     )
         * )
         */
        $this->app->delete(
            ResultRoutesProvider::DELETE_RESULT_BY_ID_PATH,
            ResultRoutesProvider::DELETE_RESULT_BY_ID_FUNCTION_NAME
        )->setName(ResultRoutesProvider::DELETE_RESULT_BY_ID_PATH_TITLE);

        /**
         * Summary: Provides the list of HTTP supported methods
         * Notes: Return a &#x60;Allow&#x60; header with a list of HTTP supported methods.
         *
         * @SWG\Options(
         *     method      = "OPTIONS",
         *     path        = "/results",
         *     tags        = { "Results" },
         *     summary     = "Provides the list of HTTP supported methods",
         *     description = "Return a `Allow` header with a list of HTTP supported methods.",
         *     operationId = "miw_options_results",
         *     @SWG\Response(
         *          response    = 200,
         *          description = "`Allow` header &lt;Response body is empty&gt;",
         *     )
         * )
         */
        $this->app->options(
            ResultRoutesProvider::GET_RESULTS_PATH,
            ResultRoutesProvider::GET_OPTIONS_FUNCTION_NAME
        )->setName(ResultRoutesProvider::GET_OPTIONS_PATH_TITLE);

        /**
         * Summary: Creates a new result
         * Notes: Creates a new result
         *
         * @SWG\Post(
         *     method      = "POST",
         *     path        = "/results",
         *     tags        = { "Results" },
         *     summary     = "Creates a new result",
         *     description = "Creates a new result",
         *     operationId = "miw_create_result",
         *     parameters  = {
         *          {
         *          "name":        "data",
         *          "in":          "body",
         *          "description": "`Result` properties to add to the system",
         *          "required":    true,
         *          "schema":      { "$ref": "#/definitions/ResultData" }
         *          }
         *     },
         *     @SWG\Response(
         *          response    = 201,
         *          description = "`Created` Result created",
         *          schema      = { "$ref": "#/definitions/Result" }
         *     ),
         *     @SWG\Response(
         *          response    = 400,
         *          description = "`Bad Request` User not found.",
         *          schema      = { "$ref": "#/definitions/Message" }
         *     ),
         *     @SWG\Response(
         *          response    = 422,
         *          description = "`Unprocessable entity` User ID or result is left out",
         *          schema      = { "$ref": "#/definitions/Message" }
         *     )
         * )
         */
        $this->app->post(
            ResultRoutesProvider::CREATE_RESULT_PATH,
            ResultRoutesProvider::CREATE_RESULT_FUNCTION_NAME
        )->setName(ResultRoutesProvider::CREATE_RESULT_PATH_TITLE);

        /**
         * Summary: Updates a result
         * Notes: Updates the result identified by &#x60;resultId&#x60;.
         *
         * @SWG\Put(
         *     method      = "PUT",
         *     path        = "/results/{resultId}",
         *     tags        = { "Results" },
         *     summary     = "Updates a result",
         *     description = "Updates the result identified by `resultId`.",
         *     operationId = "miw_update_result",
         *     parameters={
         *          { "$ref" = "#/parameters/resultId" },
         *          {
         *          "name":        "data",
         *          "in":          "body",
         *          "description": "`Result` data to update",
         *          "required":    true,
         *          "schema":      { "$ref": "#/definitions/ResultData" }
         *          }
         *     },
         *     @SWG\Response(
         *          response    = 200,
         *          description = "`Ok` Result previously existed and is now updated",
         *          schema      = { "$ref": "#/definitions/Result" }
         *     ),
         *     @SWG\Response(
         *          response    = 400,
         *          description = "`Bad request` User not found",
         *          schema      = { "$ref": "#/definitions/Message" }
         *     ),
         *     @SWG\Response(
         *          response    = 404,
         *          description = "`Not Found` Result not found",
         *          schema      = { "$ref": "#/definitions/Message" }
         *     ),
         *     @SWG\Response(
         *          response    = 422,
         *          description = "`Unprocessable entity` Result field is left out",
         *          schema      = { "$ref": "#/definitions/Message" }
         *     )
         * )
         */
        $this->app->put(
            ResultRoutesProvider::UPDATE_RESULT_PATH,
            ResultRoutesProvider::UPDATE_RESULT_FUNCTION_NAME
        )->setName(ResultRoutesProvider::UPDATE_RESULT_PATH_TITLE);
    }
}
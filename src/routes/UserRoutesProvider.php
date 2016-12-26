<?php

/**
 * Created by PhpStorm.
 * User: fran lopez
 * Date: 25/12/2016
 * Time: 17:40
 */

namespace MiW16\Results\Routes;

use MiW16\Results\Controllers\UserController;
use MiW16\Results\Models\User;
use Slim\Views\PhpRenderer;
use Swagger\Annotations as SWG;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class UserRoutesProvider
{
    const GET_USERS_PATH = '/users';
    const GET_USER_BY_ID_PATH = '/users/{id:[0-9]+}';
    const DELETE_USER_BY_ID_PATH = '/users/{id:[0-9]+}';
    const CREATE_USER_PATH = '/users';
    const UPDATE_USER_PATH = '/users/{id:[0-9]+}';

    const GET_USERS_LOGGER = 'GET \'/users\'';
    const GET_USER_BY_ID_LOGGER = 'GET \'/users/{id}\'';
    const DELETE_USER_BY_ID_LOGGER = 'DELETE \'/users/{id}\'';
    const OPTIONS_LOGGER = 'OPTIONS \'/users\'';
    const CREATE_USER_LOGGER = 'POST \'/users\'';
    const UPDATE_USER_LOGGER = 'PUT \'/users\'';

    const GET_USERS_PATH_TITLE = 'miw_get_all_users';
    const GET_USER_BY_ID_PATH_TITLE = 'miw_get_user_by_id';
    const DELETE_USER_BY_ID_PATH_TITLE = 'miw_delete_user_by_id';
    const GET_OPTIONS_PATH_TITLE = 'miw_options_users';
    const CREATE_USER_PATH_TITLE = 'miw_create_user';
    const UPDATE_USER_PATH_TITLE = 'miw_update_user';

    const GET_ALL_USERS_FUNCTION_NAME =  __CLASS__ . ':getAll';
    const GET_USER_BY_ID_FUNCTION_NAME =  __CLASS__ . ':getByID';
    const DELETE_USER_BY_ID_FUNCTION_NAME =  __CLASS__ . ':deleteByID';
    const GET_OPTIONS_FUNCTION_NAME =  __CLASS__ . ':options';
    const CREATE_USER_FUNCTION_NAME =  __CLASS__ . ':create';
    const UPDATE_USER_FUNCTION_NAME =  __CLASS__ . ':update';

    const NOT_FOUND_RESPONSE_CODE = 404;
    const NO_CONTENT_RESPONSE_CODE = 204;
    const CREATED_RESPONSE_CODE = 201;
    const BAD_REQUEST_RESPONSE_CODE = 400;
    const UNPROCESSABLE_ENTITY_RESPONSE_CODE = 422;
    const OK_RESPONSE_CODE = 200;

    const USER_NOT_FOUND_RESPONSE_MESSAGE = 'User not found';
    const USERS_NOT_FOUND_RESPONSE_MESSAGE = 'Users not found';
    const USERNAME_OR_EMAIL_ALREADY_EXISTS = 'Username or email already exists';
    const USERNAME_OR_EMAIL_OR_PASSWORD_IS_LEFT_OUT = 'Username, e-mail or password is left out';
    const USER_ID_IS_LEFT_OUT = 'User ID is left out';

    const CODE_RESPONSE_ATTRIBUTE = 'code';
    const MESSAGE_RESPONSE_ATTRIBUTE = 'message';
    const USERS_RESPONSE_ATTRIBUTE = 'users';

    const TEMPLATES_PATH = __DIR__ . '/../../templates';
    const LOG_PATH = '../logs/app.log';
    const TEMPLATE = 'message.phtml';

    const LOGGER_ID = 'MiW16';

    private $app;
    private $logger;
    private $userController;
    private $view;

    /**
     * UserRoutesProvider constructor.
     * @param \Slim\App $app
     */
    public function __construct($app)
    {
        $this->app = $app;
        $this->logger = new Logger(UserRoutesProvider::LOGGER_ID);
        $this->userController = new UserController();
        $this->view = new PhpRenderer(UserRoutesProvider::TEMPLATES_PATH);

        $this->logger->pushHandler(new StreamHandler(UserRoutesProvider::LOG_PATH, Logger::INFO));
    }

    /**
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args
     * @return \Slim\Http\Response|\Psr\Http\Message\ResponseInterface
     */
    public function getAll($request, $response, $args)
    {
        $this->logger->info(UserRoutesProvider::GET_USERS_LOGGER);
        $users = $this->userController->getAll();

        if (empty($users)) {
            $data = [UserRoutesProvider::CODE_RESPONSE_ATTRIBUTE => UserRoutesProvider::NOT_FOUND_RESPONSE_CODE,
                UserRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE => UserRoutesProvider::USERS_NOT_FOUND_RESPONSE_MESSAGE];

            return $this->view->render($response->withStatus(UserRoutesProvider::NOT_FOUND_RESPONSE_CODE),
                UserRoutesProvider::TEMPLATE, $data);
        }

        return $response->withJson(array(UserRoutesProvider::USERS_RESPONSE_ATTRIBUTE => $users));
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
        $this->logger->info(str_replace('{' . User::ID_ATTRIBUTE . '}', $args[User::ID_ATTRIBUTE],
            UserRoutesProvider::GET_USER_BY_ID_LOGGER));

        $user = $this->userController->getByID($args);

        if (empty($user)) {
            $data = [UserRoutesProvider::CODE_RESPONSE_ATTRIBUTE => UserRoutesProvider::NOT_FOUND_RESPONSE_CODE,
                UserRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE => UserRoutesProvider::USER_NOT_FOUND_RESPONSE_MESSAGE];

            return $this->view->render($response->withStatus(UserRoutesProvider::NOT_FOUND_RESPONSE_CODE),
                UserRoutesProvider::TEMPLATE, $data);
        }

        return $response->withJson($user);
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
        $this->logger->info(str_replace('{' . User::ID_ATTRIBUTE . '}', $args[User::ID_ATTRIBUTE],
            UserRoutesProvider::DELETE_USER_BY_ID_LOGGER));

        $user = $this->userController->getByID($args);

        if (empty($user)) {
            $data = [UserRoutesProvider::CODE_RESPONSE_ATTRIBUTE => UserRoutesProvider::NOT_FOUND_RESPONSE_CODE,
                UserRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE => UserRoutesProvider::USER_NOT_FOUND_RESPONSE_MESSAGE];

            return $this->view->render($response->withStatus(UserRoutesProvider::NOT_FOUND_RESPONSE_CODE),
                UserRoutesProvider::TEMPLATE, $data);
        } else
            $this->userController->delete($args);

        return $response->withStatus(UserRoutesProvider::NO_CONTENT_RESPONSE_CODE);
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
        $this->logger->info(UserRoutesProvider::OPTIONS_LOGGER);

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
        $this->logger->info(UserRoutesProvider::CREATE_USER_LOGGER);
        $data = $request->getParsedBody();

        if ((!array_key_exists(UserController::USERNAME_REQUEST_PARAMETER, $data)
                || !array_key_exists(UserController::EMAIL_REQUEST_PARAMETER, $data)
                || !array_key_exists(UserController::PASSWORD_REQUEST_PARAMETER, $data)
            ) || ((strlen($data[UserController::USERNAME_REQUEST_PARAMETER]) === 0)
                || (strlen($data[UserController::EMAIL_REQUEST_PARAMETER]) === 0)
                || (strlen($data[UserController::PASSWORD_REQUEST_PARAMETER]) === 0)
            )
        ) {
            $data = [UserRoutesProvider::CODE_RESPONSE_ATTRIBUTE =>
                UserRoutesProvider::UNPROCESSABLE_ENTITY_RESPONSE_CODE,
                UserRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE =>
                    UserRoutesProvider::USERNAME_OR_EMAIL_OR_PASSWORD_IS_LEFT_OUT];

            return $this->view->render($response->withStatus(UserRoutesProvider::UNPROCESSABLE_ENTITY_RESPONSE_CODE),
                UserRoutesProvider::TEMPLATE, $data);
        }

        if ($this->userController->userNameExists($data) || $this->userController->userEmailExists($data)) {
            $data = [UserRoutesProvider::CODE_RESPONSE_ATTRIBUTE => UserRoutesProvider::BAD_REQUEST_RESPONSE_CODE,
                UserRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE => UserRoutesProvider::USERNAME_OR_EMAIL_ALREADY_EXISTS];

            return $this->view->render($response->withStatus(UserRoutesProvider::BAD_REQUEST_RESPONSE_CODE),
                UserRoutesProvider::TEMPLATE, $data);
        }

        $user = $this->userController->create($data);

        return $response->withStatus(UserRoutesProvider::CREATED_RESPONSE_CODE)->withJson($user);
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
        $this->logger->info(UserRoutesProvider::UPDATE_USER_LOGGER);

        $userID = $args[User::ID_ATTRIBUTE];
        $data = $request->getParsedBody();
        $data[User::ID_ATTRIBUTE] = $userID;
        $user = $this->userController->getByID($args);

        if (empty($user)) {
            $data = [UserRoutesProvider::CODE_RESPONSE_ATTRIBUTE => UserRoutesProvider::NOT_FOUND_RESPONSE_CODE,
                UserRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE => UserRoutesProvider::USER_NOT_FOUND_RESPONSE_MESSAGE];

            return $this->view->render($response->withStatus(UserRoutesProvider::NOT_FOUND_RESPONSE_CODE),
                UserRoutesProvider::TEMPLATE, $data);
        }

        if ($this->userController->userNameExists($data) || $this->userController->userEmailExists($data)) {
            $data = [UserRoutesProvider::CODE_RESPONSE_ATTRIBUTE => UserRoutesProvider::BAD_REQUEST_RESPONSE_CODE,
                UserRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE => UserRoutesProvider::USERNAME_OR_EMAIL_ALREADY_EXISTS];

            return $this->view->render($response->withStatus(UserRoutesProvider::BAD_REQUEST_RESPONSE_CODE),
                UserRoutesProvider::TEMPLATE, $data);
        }

        $user = $this->userController->update($data);

        return $response->withStatus(UserRoutesProvider::OK_RESPONSE_CODE)->withJson($user);
    }

    /**
     *
     */
    public function defineRoutes()
    {
        /**
         * Summary: Returns all users
         * Notes: Returns all users from the system that the user has access to.
         *
         * @SWG\Get(
         *     method      = "GET",
         *     path        = "/users",
         *     tags        = { "Users" },
         *     summary     = "Returns all users",
         *     description = "Returns all users from the system that the user has access to.",
         *     operationId = "miw_get_all_users",
         *     @SWG\Response(
         *          response    = 200,
         *          description = "User array response",
         *          schema      = { "$ref": "#/definitions/UsersArray" }
         *     ),
         *     @SWG\Response(
         *          response    = 404,
         *          description = "Users not found",
         *          schema      = { "$ref": "#/definitions/Message" }
         *     )
         * )
         */
        $this->app->get(
            UserRoutesProvider::GET_USERS_PATH,
            UserRoutesProvider::GET_ALL_USERS_FUNCTION_NAME
        )->setName(UserRoutesProvider::GET_USERS_PATH_TITLE);

        /**
         * Summary: Returns a user based on a single ID
         * Notes: Returns the user identified by &#x60;userId&#x60;.
         *
         * @SWG\Get(
         *     method      = "GET",
         *     path        = "/users/{userId}",
         *     tags        = { "Users" },
         *     summary     = "Returns a user based on a single ID",
         *     description = "Returns the user identified by `userId`.",
         *     operationId = "miw_get_user_by_id",
         *     parameters  = {
         *          { "$ref" = "#/parameters/userId" }
         *     },
         *     @SWG\Response(
         *          response    = 200,
         *          description = "User",
         *          schema      = { "$ref": "#/definitions/User" }
         *     ),
         *     @SWG\Response(
         *          response    = 404,
         *          description = "User not found",
         *          schema      = { "$ref": "#/definitions/Message" }
         *     )
         * )
         */
        $this->app->get(
            UserRoutesProvider::GET_USER_BY_ID_PATH,
            UserRoutesProvider::GET_USER_BY_ID_FUNCTION_NAME
        )->setName(UserRoutesProvider::GET_USER_BY_ID_PATH_TITLE);

        /**
         * Summary: Deletes a user
         * Notes: Deletes the user identified by &#x60;userId&#x60;.
         *
         * @SWG\Delete(
         *     method      = "DELETE",
         *     path        = "/users/{userId}",
         *     tags        = { "Users" },
         *     summary     = "Deletes a user",
         *     description = "Deletes the user identified by `userId`.",
         *     operationId = "miw_delete_user_by_id",
         *     parameters={
         *          { "$ref" = "#/parameters/userId" }
         *     },
         *     @SWG\Response(
         *          response    = 204,
         *          description = "User deleted &lt;Response body is empty&gt;"
         *     ),
         *     @SWG\Response(
         *          response    = 404,
         *          description = "User not found",
         *          schema      = { "$ref": "#/definitions/Message" }
         *     )
         * )
         */
        $this->app->delete(
            UserRoutesProvider::DELETE_USER_BY_ID_PATH,
            UserRoutesProvider::DELETE_USER_BY_ID_FUNCTION_NAME
        )->setName(UserRoutesProvider::DELETE_USER_BY_ID_PATH_TITLE);

        /**
         * Summary: Provides the list of HTTP supported methods
         * Notes: Return a &#x60;Allow&#x60; header with a list of HTTP supported methods.
         *
         * @SWG\Options(
         *     method      = "OPTIONS",
         *     path        = "/users",
         *     tags        = { "Users" },
         *     summary     = "Provides the list of HTTP supported methods",
         *     description = "Return a `Allow` header with a list of HTTP supported methods.",
         *     operationId = "miw_options_users",
         *     @SWG\Response(
         *          response    = 200,
         *          description = "`Allow` header &lt;Response body is empty&gt;",
         *     )
         * )
         */
        $this->app->options(
            UserRoutesProvider::GET_USERS_PATH,
            UserRoutesProvider::GET_OPTIONS_FUNCTION_NAME
        )->setName(UserRoutesProvider::GET_OPTIONS_PATH_TITLE);

        /**
         * Summary: Creates a new user
         * Notes: Creates a new user
         *
         * @SWG\Post(
         *     method      = "POST",
         *     path        = "/users",
         *     tags        = { "Users" },
         *     summary     = "Creates a new user",
         *     description = "Creates a new user",
         *     operationId = "miw_create_user",
         *     parameters  = {
         *          {
         *          "name":        "data",
         *          "in":          "body",
         *          "description": "`User` properties to add to the system",
         *          "required":    true,
         *          "schema":      { "$ref": "#/definitions/UserData" }
         *          }
         *     },
         *     @SWG\Response(
         *          response    = 201,
         *          description = "`Created` User created",
         *          schema      = { "$ref": "#/definitions/User" }
         *     ),
         *     @SWG\Response(
         *          response    = 400,
         *          description = "`Bad Request` Username or email already exists.",
         *          schema      = { "$ref": "#/definitions/Message" }
         *     ),
         *     @SWG\Response(
         *          response    = 422,
         *          description = "`Unprocessable entity` Username, e-mail or password is left out",
         *          schema      = { "$ref": "#/definitions/Message" }
         *     )
         * )
         */
        $this->app->post(
            UserRoutesProvider::CREATE_USER_PATH,
            UserRoutesProvider::CREATE_USER_FUNCTION_NAME
        )->setName(UserRoutesProvider::CREATE_USER_PATH_TITLE);

        /**
         * Summary: Updates a user
         * Notes: Updates the user identified by &#x60;userId&#x60;.
         *
         * @SWG\Put(
         *     method      = "PUT",
         *     path        = "/users/{userId}",
         *     tags        = { "Users" },
         *     summary     = "Updates a user",
         *     description = "Updates the user identified by `userId`.",
         *     operationId = "miw_update_user",
         *     parameters={
         *          { "$ref" = "#/parameters/userId" },
         *          {
         *          "name":        "data",
         *          "in":          "body",
         *          "description": "`User` data to update",
         *          "required":    true,
         *          "schema":      { "$ref": "#/definitions/UserData" }
         *          }
         *     },
         *     @SWG\Response(
         *          response    = 200,
         *          description = "`Ok` User previously existed and is now updated",
         *          schema      = { "$ref": "#/definitions/User" }
         *     ),
         *     @SWG\Response(
         *          response    = 400,
         *          description = "`Bad Request` User name or email already exists",
         *          schema      = { "$ref": "#/definitions/Message" }
         *     ),
         *     @SWG\Response(
         *          response    = 404,
         *          description = "`Not Found` User not found",
         *          schema      = { "$ref": "#/definitions/Message" }
         *     )
         * )
         */
        $this->app->put(
            UserRoutesProvider::UPDATE_USER_PATH,
            UserRoutesProvider::UPDATE_USER_FUNCTION_NAME
        )->setName(UserRoutesProvider::UPDATE_USER_PATH_TITLE);
    }
}
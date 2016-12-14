<?php // src/routes_user.php

use Swagger\Annotations as SWG;

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
 *     operationId = "miw_cget_users",
 *     @SWG\Response(
 *          response    = 200,
 *          description = "User array response",
 *          schema      = { "$ref": "#/definitions/UsersArray" }
 *     ),
 *     @SWG\Response(
 *          response    = 404,
 *          description = "User object not found",
 *          schema      = { "$ref": "#/definitions/Message" }
 *     )
 * )
 * @var \Slim\App $app
 */
$app->get(
    '/users',
    function ($request, $response, $args) {
        $this->logger->info('GET \'/users\'');
        $usuarios = getEntityManager()
            ->getRepository('MiW16\Results\Entity\User')
            ->findAll();

        if (empty($usuarios)) { // 404 - User object not found
            $newResponse = $response->withStatus(404);
            $datos = array(
                'code' => 404,
                'message' => 'User object not found'
            );
            return $this->renderer->render($newResponse, 'message.phtml', $datos);
        }

        return $response->withJson(array('users' => $usuarios));
    }
)->setName('miw_cget_users');

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
 *     operationId = "miw_get_users",
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
 *          description = "User id. not found",
 *          schema      = { "$ref": "#/definitions/Message" }
 *     )
 * )
 */
$app->get(
    '/users/{id:[0-9]+}',
    function ($request, $response, $args) {
        $this->logger->info('GET \'/users/' . $args['id'] . '\'');
        $usuario = getEntityManager()
            ->getRepository('MiW16\Results\Entity\User')
            ->findOneById($args['id']);

        if (empty($usuario)) {  // 404 - User id. not found
            $newResponse = $response->withStatus(404);
            $datos = array(
                'code' => 404,
                'message' => 'User not found'
            );
            return $this->renderer->render($newResponse, 'message.phtml', $datos);
        }

        return $response->withJson($usuario);
    }
)->setName('miw_get_users');

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
 *     operationId = "miw_delete_users",
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
$app->delete(
    '/users/{id:[0-9]+}',
    function ($request, $response, $args) {
        $this->logger->info('DELETE \'/users/' . $args['id'] . '\'');
        $em = getEntityManager();
        $usuario = $em
            ->getRepository('MiW16\Results\Entity\User')
            ->findOneById($args['id']);
        if (empty($usuario)) {  // 404 - User id. not found
            $newResponse = $response->withStatus(404);
            $datos = array(
                'code' => 404,
                'message' => 'User not found'
            );
            return $this->renderer->render($newResponse, 'message.phtml', $datos);
        } else {
            $em->remove($usuario);
            $em->flush();
        }

        return $response->withStatus(204);
    }
)->setName('miw_delete_users');

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
$app->options(
    '/users',
    function ($request, $response, $args) {
        $this->logger->info('OPTIONS \'/users\'');

        return $response
            ->withHeader(
                'Allow',
                'OPTIONS, GET, POST, PUT, DELETE'
            );
    }
)->setName('miw_options_users');

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
 *     operationId = "miw_post_users",
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
$app->post(
    '/users',
    function ($request, $response, $args) {
        $this->logger->info('POST \'/users\'');
        $data = json_decode($request->getBody(), true); // parse the JSON into an assoc. array
        // process $data...

        // TODO
        $newResponse = $response->withStatus(501);
        return $newResponse;
    }
)->setName('miw_post_users');

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
 *     operationId = "miw_put_users",
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
 *          description = "`Bad Request` User name or e-mail already exists",
 *          schema      = { "$ref": "#/definitions/Message" }
 *     ),
 *     @SWG\Response(
 *          response    = 404,
 *          description = "`Not Found` The user could not be found",
 *          schema      = { "$ref": "#/definitions/Message" }
 *     )
 * )
 */
$app->put(
    '/users/{id:[0-9]+}',
    function ($request, $response, $args) {
        $this->logger->info('PUT \'/users\'');
        $data = json_decode($request->getBody(), true); // parse the JSON into an assoc. array
        // process $data...

        // TODO
        $newResponse = $response->withStatus(501);
        return $newResponse;
    }
)->setName('miw_post_users');


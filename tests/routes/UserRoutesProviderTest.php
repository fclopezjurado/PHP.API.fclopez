<?php

namespace MiW16\Results\Tests\Routes;

use MiW16\Results\Models\User;
use MiW16\Results\Routes\UserRoutesProvider;

/**
 * Class UserRoutesProviderTest
 * @package MiW16\Results\Tests\Routes
 *
 * NOTE: To run this test correctly, 'users' database table must be empty.
 */
class UserRoutesProviderTest extends RunAppTest
{
    const ALLOW_HTTP_RESPONSE_HEADER = 'Allow';
    const OPTIONS_HTTP_METHOD = 'OPTIONS';
    const GET_HTTP_METHOD = 'GET';
    const POST_HTTP_METHOD = 'POST';
    const PUT_HTTP_METHOD = 'PUT';
    const DELETE_HTTP_METHOD = 'DELETE';

    const FIRST_USER_NAME_FOR_TESTS = 'Francisco';
    const FIRST_USER_MAIL_FOR_TESTS = 'fc.lopez@alumnos.upm.es';
    const FIRST_ENABLED_USER_FOR_TESTS = true;
    const FIRST_USER_PASSWORD_FOR_TESTS = 'df4a5d4as46da46';
    const FIRST_USER_TOKEN_FOR_TESTS = 'df4sf4sa5f4sa6f45fsa6';

    const SECOND_USER_NAME_FOR_TESTS = 'Carlos';
    const SECOND_USER_MAIL_FOR_TESTS = 'cj.perez@alumnos.upm.es';
    const SECOND_ENABLED_USER_FOR_TESTS = false;
    const SECOND_USER_PASSWORD_FOR_TESTS = 'fhsf8hs7hs6f';
    const SECOND_USER_TOKEN_FOR_TESTS = 'yj4hgj4dg5j4gf5';

    /**
     * GET ALL USERS: Function to test 404 server response (Users not found).
     */
    public function testGetAllUsersNotFound()
    {
        $response = $this->runApp(UserRoutesProviderTest::GET_HTTP_METHOD, '/users');

        self::assertEquals(UserRoutesProvider::NOT_FOUND_RESPONSE_CODE, $response->getStatusCode());
        self::assertJson(strval($response->getBody()));
        self::assertContains(UserRoutesProvider::CODE_RESPONSE_ATTRIBUTE, strval($response->getBody()));
        self::assertContains(UserRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE, strval($response->getBody()));

        $responseBody = json_decode(strval($response->getBody()), true);

        self::assertEquals(UserRoutesProvider::NOT_FOUND_RESPONSE_CODE,
            $responseBody[UserRoutesProvider::CODE_RESPONSE_ATTRIBUTE]);
        self::assertEquals(UserRoutesProvider::USERS_NOT_FOUND_RESPONSE_MESSAGE,
            $responseBody[UserRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE]);
    }

    /**
     * CREATE USER: Function to test 201 server response (User has been created).
     */
    public function testCreateUserOK()
    {
        $user = new User();

        $user->setUsername(UserRoutesProviderTest::FIRST_USER_NAME_FOR_TESTS);
        $user->setEmail(UserRoutesProviderTest::FIRST_USER_MAIL_FOR_TESTS);
        $user->setEnabled(UserRoutesProviderTest::FIRST_ENABLED_USER_FOR_TESTS);
        $user->setPassword(UserRoutesProviderTest::FIRST_USER_PASSWORD_FOR_TESTS);
        $user->setToken(UserRoutesProviderTest::FIRST_USER_TOKEN_FOR_TESTS);

        $serializedUser = $user->jsonSerialize();
        $serializedUser[User::PASSWORD_ATTRIBUTE] = UserRoutesProviderTest::FIRST_USER_PASSWORD_FOR_TESTS;
        $response = $this->runApp(UserRoutesProviderTest::POST_HTTP_METHOD, '/users', $serializedUser);

        self::assertJson(strval($response->getBody()));

        $responseBody = json_decode(strval($response->getBody()), true);

        self::assertInternalType('array', $responseBody);
        self::assertEquals(UserRoutesProvider::CREATED_RESPONSE_CODE, $response->getStatusCode());

        self::assertArrayHasKey(User::ID_ATTRIBUTE, $responseBody);
        self::assertArrayHasKey(User::USERNAME_ATTRIBUTE, $responseBody);
        self::assertArrayHasKey(User::EMAIL_ATTRIBUTE, $responseBody);
        self::assertArrayHasKey(User::ENABLED_ATTRIBUTE, $responseBody);
        self::assertArrayHasKey(User::PASSWORD_ATTRIBUTE, $responseBody);
        self::assertArrayHasKey(User::TOKEN_ATTRIBUTE, $responseBody);

        self::assertInternalType('int', $responseBody[User::ID_ATTRIBUTE]);
        self::assertEquals(UserRoutesProviderTest::FIRST_USER_NAME_FOR_TESTS, $responseBody[User::USERNAME_ATTRIBUTE]);
        self::assertEquals(UserRoutesProviderTest::FIRST_USER_MAIL_FOR_TESTS, $responseBody[User::EMAIL_ATTRIBUTE]);
        self::assertEquals(UserRoutesProviderTest::FIRST_ENABLED_USER_FOR_TESTS, $responseBody[User::ENABLED_ATTRIBUTE]);
        self::assertEquals(UserRoutesProviderTest::FIRST_USER_TOKEN_FOR_TESTS, $responseBody[User::TOKEN_ATTRIBUTE]);
    }

    /**
     * CREATE USER: Function to test 400 server response (Username or email already exists).
     */
    public function testCreateUserBadRequest()
    {
        $user = new User();

        $user->setUsername(UserRoutesProviderTest::FIRST_USER_NAME_FOR_TESTS);
        $user->setEmail(UserRoutesProviderTest::FIRST_USER_MAIL_FOR_TESTS);
        $user->setEnabled(UserRoutesProviderTest::FIRST_ENABLED_USER_FOR_TESTS);
        $user->setPassword(UserRoutesProviderTest::FIRST_USER_PASSWORD_FOR_TESTS);
        $user->setToken(UserRoutesProviderTest::FIRST_USER_TOKEN_FOR_TESTS);

        $serializedUser = $user->jsonSerialize();
        $serializedUser[User::PASSWORD_ATTRIBUTE] = UserRoutesProviderTest::FIRST_USER_PASSWORD_FOR_TESTS;
        $response = $this->runApp(UserRoutesProviderTest::POST_HTTP_METHOD, '/users', $serializedUser);

        self::assertEquals(UserRoutesProvider::BAD_REQUEST_RESPONSE_CODE, $response->getStatusCode());
        self::assertJson(strval($response->getBody()));
        self::assertContains(UserRoutesProvider::CODE_RESPONSE_ATTRIBUTE, strval($response->getBody()));
        self::assertContains(UserRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE, strval($response->getBody()));

        $responseBody = json_decode(strval($response->getBody()), true);

        self::assertEquals(UserRoutesProvider::BAD_REQUEST_RESPONSE_CODE,
            $responseBody[UserRoutesProvider::CODE_RESPONSE_ATTRIBUTE]);
        self::assertEquals(UserRoutesProvider::USERNAME_OR_EMAIL_ALREADY_EXISTS,
            $responseBody[UserRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE]);
    }

    /**
     * CREATE USER: Function to test 422 server response (Username, e-mail or password is left out).
     */
    public function testCreateUserUnprocessableEntity()
    {
        $user = new User();

        $user->setUsername(UserRoutesProviderTest::FIRST_USER_NAME_FOR_TESTS);
        $user->setEmail(UserRoutesProviderTest::FIRST_USER_MAIL_FOR_TESTS);
        $user->setEnabled(UserRoutesProviderTest::FIRST_ENABLED_USER_FOR_TESTS);
        $user->setPassword(UserRoutesProviderTest::FIRST_USER_PASSWORD_FOR_TESTS);
        $user->setToken(UserRoutesProviderTest::FIRST_USER_TOKEN_FOR_TESTS);

        $serializedUser = $user->jsonSerialize();
        $serializedUser[User::PASSWORD_ATTRIBUTE] = UserRoutesProviderTest::FIRST_USER_PASSWORD_FOR_TESTS;

        unset($serializedUser[User::USERNAME_ATTRIBUTE]);

        $response = $this->runApp(UserRoutesProviderTest::POST_HTTP_METHOD, '/users', $serializedUser);

        self::assertEquals(UserRoutesProvider::UNPROCESSABLE_ENTITY_RESPONSE_CODE, $response->getStatusCode());
        self::assertJson(strval($response->getBody()));
        self::assertContains(UserRoutesProvider::CODE_RESPONSE_ATTRIBUTE, strval($response->getBody()));
        self::assertContains(UserRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE, strval($response->getBody()));

        $responseBody = json_decode(strval($response->getBody()), true);

        self::assertEquals(UserRoutesProvider::UNPROCESSABLE_ENTITY_RESPONSE_CODE,
            $responseBody[UserRoutesProvider::CODE_RESPONSE_ATTRIBUTE]);
        self::assertEquals(UserRoutesProvider::USERNAME_OR_EMAIL_OR_PASSWORD_IS_LEFT_OUT,
            $responseBody[UserRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE]);
    }

    /**
     * GET ALL USERS: Function to test 200 server response (Users information).
     */
    public function testGetAllUsersOK()
    {
        $response = $this->runApp(UserRoutesProviderTest::GET_HTTP_METHOD, '/users');

        self::assertEquals(UserRoutesProvider::OK_RESPONSE_CODE, $response->getStatusCode());
        self::assertJson(strval($response->getBody()));
        self::assertContains(UserRoutesProvider::USERS_RESPONSE_ATTRIBUTE, strval($response->getBody()));

        $responseBody = json_decode(strval($response->getBody()), true);
        $users = $responseBody[UserRoutesProvider::USERS_RESPONSE_ATTRIBUTE];
        $user = array_shift($users);

        self::assertArrayHasKey(User::ID_ATTRIBUTE, $user);
        self::assertArrayHasKey(User::USERNAME_ATTRIBUTE, $user);
        self::assertArrayHasKey(User::EMAIL_ATTRIBUTE, $user);
        self::assertArrayHasKey(User::ENABLED_ATTRIBUTE, $user);
        self::assertArrayHasKey(User::PASSWORD_ATTRIBUTE, $user);
        self::assertArrayHasKey(User::TOKEN_ATTRIBUTE, $user);

        self::assertInternalType('int', $user[User::ID_ATTRIBUTE]);
        self::assertEquals(UserRoutesProviderTest::FIRST_USER_NAME_FOR_TESTS, $user[User::USERNAME_ATTRIBUTE]);
        self::assertEquals(UserRoutesProviderTest::FIRST_USER_MAIL_FOR_TESTS, $user[User::EMAIL_ATTRIBUTE]);
        self::assertEquals(UserRoutesProviderTest::FIRST_ENABLED_USER_FOR_TESTS, $user[User::ENABLED_ATTRIBUTE]);
        self::assertEquals(UserRoutesProviderTest::FIRST_USER_TOKEN_FOR_TESTS, $user[User::TOKEN_ATTRIBUTE]);
    }

    /**
     * GET USER BY ID: Function to test 404 server response (User not found).
     */
    public function testGetUserNotFound()
    {
        $response = $this->runApp(UserRoutesProviderTest::GET_HTTP_METHOD, '/users/9999999');

        self::assertEquals(UserRoutesProvider::NOT_FOUND_RESPONSE_CODE, $response->getStatusCode());
        self::assertJson(strval($response->getBody()));
        self::assertContains(UserRoutesProvider::CODE_RESPONSE_ATTRIBUTE, strval($response->getBody()));
        self::assertContains(UserRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE, strval($response->getBody()));

        $responseBody = json_decode(strval($response->getBody()), true);

        self::assertEquals(UserRoutesProvider::NOT_FOUND_RESPONSE_CODE,
            $responseBody[UserRoutesProvider::CODE_RESPONSE_ATTRIBUTE]);
        self::assertEquals(UserRoutesProvider::USER_NOT_FOUND_RESPONSE_MESSAGE,
            $responseBody[UserRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE]);
    }

    /**
     * GET USER BY ID: Function to test 200 server response (User information).
     */
    public function testGetUserOK()
    {
        $response = $this->runApp(UserRoutesProviderTest::GET_HTTP_METHOD, '/users/1');

        self::assertEquals(UserRoutesProvider::OK_RESPONSE_CODE, $response->getStatusCode());
        self::assertJson(strval($response->getBody()));

        $user = json_decode(strval($response->getBody()), true);

        self::assertArrayHasKey(User::ID_ATTRIBUTE, $user);
        self::assertArrayHasKey(User::USERNAME_ATTRIBUTE, $user);
        self::assertArrayHasKey(User::EMAIL_ATTRIBUTE, $user);
        self::assertArrayHasKey(User::ENABLED_ATTRIBUTE, $user);
        self::assertArrayHasKey(User::PASSWORD_ATTRIBUTE, $user);
        self::assertArrayHasKey(User::TOKEN_ATTRIBUTE, $user);

        self::assertInternalType('int', $user[User::ID_ATTRIBUTE]);
        self::assertEquals(UserRoutesProviderTest::FIRST_USER_NAME_FOR_TESTS, $user[User::USERNAME_ATTRIBUTE]);
        self::assertEquals(UserRoutesProviderTest::FIRST_USER_MAIL_FOR_TESTS, $user[User::EMAIL_ATTRIBUTE]);
        self::assertEquals(UserRoutesProviderTest::FIRST_ENABLED_USER_FOR_TESTS, $user[User::ENABLED_ATTRIBUTE]);
        self::assertEquals(UserRoutesProviderTest::FIRST_USER_TOKEN_FOR_TESTS, $user[User::TOKEN_ATTRIBUTE]);
    }

    /**
     * UPDATE USER: Function to test 404 server response (User not found).
     */
    public function testUpdateUserNotFound()
    {
        $response = $this->runApp(UserRoutesProviderTest::PUT_HTTP_METHOD, '/users/9999999');

        self::assertEquals(UserRoutesProvider::NOT_FOUND_RESPONSE_CODE, $response->getStatusCode());
        self::assertJson(strval($response->getBody()));
        self::assertContains(UserRoutesProvider::CODE_RESPONSE_ATTRIBUTE, strval($response->getBody()));
        self::assertContains(UserRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE, strval($response->getBody()));

        $responseBody = json_decode(strval($response->getBody()), true);

        self::assertEquals(UserRoutesProvider::NOT_FOUND_RESPONSE_CODE,
            $responseBody[UserRoutesProvider::CODE_RESPONSE_ATTRIBUTE]);
        self::assertEquals(UserRoutesProvider::USER_NOT_FOUND_RESPONSE_MESSAGE,
            $responseBody[UserRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE]);
    }

    /**
     * UPDATE USER: Function to test 200 server response (User has been updated).
     */
    public function testUpdateUserOK()
    {
        $user = new User();

        $user->setUsername(UserRoutesProviderTest::SECOND_USER_NAME_FOR_TESTS);
        $user->setEmail(UserRoutesProviderTest::SECOND_USER_MAIL_FOR_TESTS);
        $user->setEnabled(UserRoutesProviderTest::SECOND_ENABLED_USER_FOR_TESTS);
        $user->setPassword(UserRoutesProviderTest::SECOND_USER_PASSWORD_FOR_TESTS);
        $user->setToken(UserRoutesProviderTest::SECOND_USER_TOKEN_FOR_TESTS);

        $serializedUser = $user->jsonSerialize();
        $serializedUser[User::PASSWORD_ATTRIBUTE] = UserRoutesProviderTest::SECOND_USER_PASSWORD_FOR_TESTS;
        $response = $this->runApp(UserRoutesProviderTest::PUT_HTTP_METHOD, '/users/1', $serializedUser);

        self::assertJson(strval($response->getBody()));

        $responseBody = json_decode(strval($response->getBody()), true);

        self::assertInternalType('array', $responseBody);
        self::assertEquals(UserRoutesProvider::OK_RESPONSE_CODE, $response->getStatusCode());

        self::assertArrayHasKey(User::ID_ATTRIBUTE, $responseBody);
        self::assertArrayHasKey(User::USERNAME_ATTRIBUTE, $responseBody);
        self::assertArrayHasKey(User::EMAIL_ATTRIBUTE, $responseBody);
        self::assertArrayHasKey(User::ENABLED_ATTRIBUTE, $responseBody);
        self::assertArrayHasKey(User::PASSWORD_ATTRIBUTE, $responseBody);
        self::assertArrayHasKey(User::TOKEN_ATTRIBUTE, $responseBody);

        self::assertInternalType('int', $responseBody[User::ID_ATTRIBUTE]);
        self::assertEquals(UserRoutesProviderTest::SECOND_USER_NAME_FOR_TESTS,
            $responseBody[User::USERNAME_ATTRIBUTE]);
        self::assertEquals(UserRoutesProviderTest::SECOND_USER_MAIL_FOR_TESTS, $responseBody[User::EMAIL_ATTRIBUTE]);
        self::assertEquals(UserRoutesProviderTest::SECOND_ENABLED_USER_FOR_TESTS,
            $responseBody[User::ENABLED_ATTRIBUTE]);
        self::assertEquals(UserRoutesProviderTest::SECOND_USER_TOKEN_FOR_TESTS, $responseBody[User::TOKEN_ATTRIBUTE]);
    }

    /**
     * UPDATE USER: Function to test 400 server response (User name or e-mail already exists).
     */
    public function testUpdateUserBadRequest()
    {
        $user = new User();

        $user->setUsername(UserRoutesProviderTest::SECOND_USER_NAME_FOR_TESTS);
        $user->setEmail(UserRoutesProviderTest::SECOND_USER_MAIL_FOR_TESTS);
        $user->setEnabled(UserRoutesProviderTest::SECOND_ENABLED_USER_FOR_TESTS);
        $user->setPassword(UserRoutesProviderTest::SECOND_USER_PASSWORD_FOR_TESTS);
        $user->setToken(UserRoutesProviderTest::SECOND_USER_TOKEN_FOR_TESTS);

        $serializedUser = $user->jsonSerialize();
        $serializedUser[User::PASSWORD_ATTRIBUTE] = UserRoutesProviderTest::SECOND_USER_PASSWORD_FOR_TESTS;
        $response = $this->runApp(UserRoutesProviderTest::PUT_HTTP_METHOD, '/users/1', $serializedUser);

        self::assertEquals(UserRoutesProvider::BAD_REQUEST_RESPONSE_CODE, $response->getStatusCode());
        self::assertJson(strval($response->getBody()));
        self::assertContains(UserRoutesProvider::CODE_RESPONSE_ATTRIBUTE, strval($response->getBody()));
        self::assertContains(UserRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE, strval($response->getBody()));

        $responseBody = json_decode(strval($response->getBody()), true);

        self::assertEquals(UserRoutesProvider::BAD_REQUEST_RESPONSE_CODE,
            $responseBody[UserRoutesProvider::CODE_RESPONSE_ATTRIBUTE]);
        self::assertEquals(UserRoutesProvider::USERNAME_OR_EMAIL_ALREADY_EXISTS,
            $responseBody[UserRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE]);
    }

    /**
     * SUPPORTED HTTP METHODS: Function to test all supported HTTP methods (OPTIONS, GET, POST, PUT & DELETE).
     */
    public function testHTTPSupportedMethods()
    {
        $response = $this->runApp(UserRoutesProviderTest::OPTIONS_HTTP_METHOD, '/users');

        self::assertEquals(UserRoutesProvider::OK_RESPONSE_CODE, $response->getStatusCode());

        $supportedHTTPMethods = $response->getHeader('Allow');

        self::assertInternalType('array', $supportedHTTPMethods);
        self::assertArrayHasKey(0, $supportedHTTPMethods);

        $supportedHTTPMethods = array_shift($supportedHTTPMethods);

        self::assertContains(UserRoutesProviderTest::OPTIONS_HTTP_METHOD, $supportedHTTPMethods);
        self::assertContains(UserRoutesProviderTest::GET_HTTP_METHOD, $supportedHTTPMethods);
        self::assertContains(UserRoutesProviderTest::POST_HTTP_METHOD, $supportedHTTPMethods);
        self::assertContains(UserRoutesProviderTest::PUT_HTTP_METHOD, $supportedHTTPMethods);
        self::assertContains(UserRoutesProviderTest::DELETE_HTTP_METHOD, $supportedHTTPMethods);
    }

    /**
     * DELETE USER: Function to test 404 server response (User not found).
     */
    public function testDeleteUserNotFound()
    {
        $response = $this->runApp(UserRoutesProviderTest::DELETE_HTTP_METHOD, '/users/9999999');

        self::assertEquals(UserRoutesProvider::NOT_FOUND_RESPONSE_CODE, $response->getStatusCode());
        self::assertJson(strval($response->getBody()));
        self::assertContains(UserRoutesProvider::CODE_RESPONSE_ATTRIBUTE, strval($response->getBody()));
        self::assertContains(UserRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE, strval($response->getBody()));

        $responseBody = json_decode(strval($response->getBody()), true);

        self::assertEquals(UserRoutesProvider::NOT_FOUND_RESPONSE_CODE,
            $responseBody[UserRoutesProvider::CODE_RESPONSE_ATTRIBUTE]);
        self::assertEquals(UserRoutesProvider::USER_NOT_FOUND_RESPONSE_MESSAGE,
            $responseBody[UserRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE]);
    }

    /**
     * DELETE USER: Function to test 204 server response (User deleted).
     */
    public function testDeleteUserOK()
    {
        $response = $this->runApp(UserRoutesProviderTest::DELETE_HTTP_METHOD, '/users/1');

        self::assertEquals(UserRoutesProvider::NO_CONTENT_RESPONSE_CODE, $response->getStatusCode());
        self::assertEmpty(strval($response->getBody()));
    }
}
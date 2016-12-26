<?php

/**
 * Created by PhpStorm.
 * User: fran lopez
 * Date: 25/12/2016
 * Time: 14:28
 */

namespace MiW16\Results\Tests\Routes;

use MiW16\Results\Models\User;
use MiW16\Results\Routes\UserRoutesProvider;
use MiW16\Results\Models\Result;
use MiW16\Results\Routes\ResultRoutesProvider;

/**
 * Class ResultRoutesProviderTest
 * @package MiW16\Results\Tests\Routes
 *
 * NOTE: To run this test correctly, 'users' and 'results' database tables must be empty.
 */
class ResultRoutesProviderTest extends RunAppTest
{
    const ALLOW_HTTP_RESPONSE_HEADER = 'Allow';
    const OPTIONS_HTTP_METHOD = 'OPTIONS';
    const GET_HTTP_METHOD = 'GET';
    const POST_HTTP_METHOD = 'POST';
    const PUT_HTTP_METHOD = 'PUT';
    const DELETE_HTTP_METHOD = 'DELETE';

    const USER_ID_FOR_TESTS = 1;
    const INVALID_USER_ID_FOR_TESTS = 2;
    const FIRST_RESULT_FOR_TESTS = 2;
    const SECOND_RESULT_FOR_TESTS = 3;

    /**
     * In this function, an associated user is created to test the results.
     */
    public static function setUpBeforeClass()
    {
        $user = new User();

        $user->setUsername(UserRoutesProviderTest::FIRST_USER_NAME_FOR_TESTS);
        $user->setEmail(UserRoutesProviderTest::FIRST_USER_MAIL_FOR_TESTS);
        $user->setEnabled(UserRoutesProviderTest::FIRST_ENABLED_USER_FOR_TESTS);
        $user->setPassword(UserRoutesProviderTest::FIRST_USER_PASSWORD_FOR_TESTS);
        $user->setToken(UserRoutesProviderTest::FIRST_USER_TOKEN_FOR_TESTS);

        $serializedUser = $user->jsonSerialize();
        $serializedUser[User::PASSWORD_ATTRIBUTE] = UserRoutesProviderTest::FIRST_USER_PASSWORD_FOR_TESTS;
        self::runApp(UserRoutesProviderTest::POST_HTTP_METHOD, '/users', $serializedUser);
    }

    /**
     * In this function, the associated user created to test the results, is deleted.
     */
    public static function tearDownAfterClass()
    {
        $response = self::runApp(UserRoutesProviderTest::DELETE_HTTP_METHOD, '/users/1');

        self::assertEquals(UserRoutesProvider::NO_CONTENT_RESPONSE_CODE, $response->getStatusCode());
        self::assertEmpty(strval($response->getBody()));
    }

    /**
     * GET ALL RESULTS: Function to test 404 server response (Results not found).
     */
    public function testGetAllResultsNotFound()
    {
        $response = $this->runApp(ResultRoutesProviderTest::GET_HTTP_METHOD, '/results');

        self::assertEquals(ResultRoutesProvider::NOT_FOUND_RESPONSE_CODE, $response->getStatusCode());
        self::assertJson(strval($response->getBody()));
        self::assertContains(ResultRoutesProvider::CODE_RESPONSE_ATTRIBUTE, strval($response->getBody()));
        self::assertContains(ResultRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE, strval($response->getBody()));

        $responseBody = json_decode(strval($response->getBody()), true);

        self::assertEquals(ResultRoutesProvider::NOT_FOUND_RESPONSE_CODE,
            $responseBody[ResultRoutesProvider::CODE_RESPONSE_ATTRIBUTE]);
        self::assertEquals(ResultRoutesProvider::RESULTS_NOT_FOUND,
            $responseBody[ResultRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE]);
    }

    /**
     * CREATE RESULT: Function to test 201 server response (Result has been created).
     */
    public function testCreateResultOK()
    {
        $serializedUser = array();
        $serializedUser[Result::USER_ID_ATTRIBUTE] = ResultRoutesProviderTest::USER_ID_FOR_TESTS;
        $serializedUser[Result::RESULT_ATTRIBUTE] = ResultRoutesProviderTest::FIRST_RESULT_FOR_TESTS;
        $response = $this->runApp(ResultRoutesProviderTest::POST_HTTP_METHOD, '/results', $serializedUser);

        self::assertJson(strval($response->getBody()));

        $responseBody = json_decode(strval($response->getBody()), true);

        self::assertInternalType('array', $responseBody);
        self::assertEquals(ResultRoutesProvider::CREATED_RESPONSE_CODE, $response->getStatusCode());

        self::assertArrayHasKey(Result::ID_ATTRIBUTE, $responseBody);
        self::assertArrayHasKey(Result::USER_ATTRIBUTE, $responseBody);
        self::assertArrayHasKey(Result::RESULT_ATTRIBUTE, $responseBody);
        self::assertArrayHasKey(Result::TIME_ATTRIBUTE, $responseBody);

        self::assertInternalType('int', $responseBody[Result::ID_ATTRIBUTE]);
        self::assertEquals(ResultRoutesProviderTest::FIRST_RESULT_FOR_TESTS, $responseBody[Result::RESULT_ATTRIBUTE]);
        self::assertInternalType('array', $responseBody[Result::USER_ATTRIBUTE]);

        $user = $responseBody[Result::USER_ATTRIBUTE];

        self::assertEquals(ResultRoutesProviderTest::USER_ID_FOR_TESTS, $user[User::ID_ATTRIBUTE]);
        self::assertEquals(UserRoutesProviderTest::FIRST_USER_NAME_FOR_TESTS, $user[User::USERNAME_ATTRIBUTE]);
        self::assertEquals(UserRoutesProviderTest::FIRST_USER_MAIL_FOR_TESTS, $user[User::EMAIL_ATTRIBUTE]);
        self::assertEquals(UserRoutesProviderTest::FIRST_ENABLED_USER_FOR_TESTS, $user[User::ENABLED_ATTRIBUTE]);
        self::assertEquals(UserRoutesProviderTest::FIRST_USER_TOKEN_FOR_TESTS, $user[User::TOKEN_ATTRIBUTE]);
    }

    /**
     * CREATE RESULT: Function to test 400 server response (User not found).
     */
    public function testCreateResultBadRequest()
    {
        $serializedUser = array();
        $serializedUser[Result::USER_ID_ATTRIBUTE] = ResultRoutesProviderTest::INVALID_USER_ID_FOR_TESTS;
        $serializedUser[Result::RESULT_ATTRIBUTE] = ResultRoutesProviderTest::FIRST_RESULT_FOR_TESTS;
        $response = $this->runApp(ResultRoutesProviderTest::POST_HTTP_METHOD, '/results', $serializedUser);

        self::assertEquals(ResultRoutesProvider::BAD_REQUEST_RESPONSE_CODE, $response->getStatusCode());
        self::assertJson(strval($response->getBody()));
        self::assertContains(ResultRoutesProvider::CODE_RESPONSE_ATTRIBUTE, strval($response->getBody()));
        self::assertContains(ResultRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE, strval($response->getBody()));

        $responseBody = json_decode(strval($response->getBody()), true);

        self::assertEquals(ResultRoutesProvider::BAD_REQUEST_RESPONSE_CODE,
            $responseBody[ResultRoutesProvider::CODE_RESPONSE_ATTRIBUTE]);
        self::assertEquals(ResultRoutesProvider::USER_NOT_FOUND,
            $responseBody[ResultRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE]);
    }

    /**
     * CREATE RESULT: Function to test 422 server response (User ID or result is left out).
     */
    public function testCreateResultUnprocessableEntity()
    {
        $serializedUser = array();
        $serializedUser[Result::USER_ID_ATTRIBUTE] = ResultRoutesProviderTest::INVALID_USER_ID_FOR_TESTS;
        $response = $this->runApp(ResultRoutesProviderTest::POST_HTTP_METHOD, '/results', $serializedUser);

        self::assertEquals(ResultRoutesProvider::UNPROCESSABLE_ENTITY_RESPONSE_CODE, $response->getStatusCode());
        self::assertJson(strval($response->getBody()));
        self::assertContains(ResultRoutesProvider::CODE_RESPONSE_ATTRIBUTE, strval($response->getBody()));
        self::assertContains(ResultRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE, strval($response->getBody()));

        $responseBody = json_decode(strval($response->getBody()), true);

        self::assertEquals(ResultRoutesProvider::UNPROCESSABLE_ENTITY_RESPONSE_CODE,
            $responseBody[ResultRoutesProvider::CODE_RESPONSE_ATTRIBUTE]);
        self::assertEquals(ResultRoutesProvider::USER_ID_OR_RESULT_IS_LEFT_OUT,
            $responseBody[ResultRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE]);
    }

    /**
     * GET ALL RESULTS: Function to test 200 server response (Results information).
     */
    public function testGetAllResultsOK()
    {
        $response = $this->runApp(ResultRoutesProviderTest::GET_HTTP_METHOD, '/results');

        self::assertEquals(ResultRoutesProvider::OK_RESPONSE_CODE, $response->getStatusCode());
        self::assertJson(strval($response->getBody()));
        self::assertContains(ResultRoutesProvider::RESULTS_RESPONSE_ATTRIBUTE, strval($response->getBody()));

        $responseBody = json_decode(strval($response->getBody()), true);
        $results = $responseBody[ResultRoutesProvider::RESULTS_RESPONSE_ATTRIBUTE];
        $result = array_shift($results);

        self::assertArrayHasKey(Result::ID_ATTRIBUTE, $result);
        self::assertArrayHasKey(Result::USER_ATTRIBUTE, $result);
        self::assertArrayHasKey(Result::RESULT_ATTRIBUTE, $result);
        self::assertArrayHasKey(Result::TIME_ATTRIBUTE, $result);

        self::assertInternalType('int', $result[Result::ID_ATTRIBUTE]);
        self::assertEquals(ResultRoutesProviderTest::FIRST_RESULT_FOR_TESTS, $result[Result::RESULT_ATTRIBUTE]);
        self::assertInternalType('array', $result[Result::USER_ATTRIBUTE]);

        $user = $result[Result::USER_ATTRIBUTE];

        self::assertEquals(ResultRoutesProviderTest::USER_ID_FOR_TESTS, $user[User::ID_ATTRIBUTE]);
        self::assertEquals(UserRoutesProviderTest::FIRST_USER_NAME_FOR_TESTS, $user[User::USERNAME_ATTRIBUTE]);
        self::assertEquals(UserRoutesProviderTest::FIRST_USER_MAIL_FOR_TESTS, $user[User::EMAIL_ATTRIBUTE]);
        self::assertEquals(UserRoutesProviderTest::FIRST_ENABLED_USER_FOR_TESTS, $user[User::ENABLED_ATTRIBUTE]);
        self::assertEquals(UserRoutesProviderTest::FIRST_USER_TOKEN_FOR_TESTS, $user[User::TOKEN_ATTRIBUTE]);
    }

    /**
     * GET RESULT BY ID: Function to test 404 server response (Result not found).
     */
    public function testGetResultNotFound()
    {
        $response = $this->runApp(ResultRoutesProviderTest::GET_HTTP_METHOD, '/results/9999999');

        self::assertEquals(ResultRoutesProvider::NOT_FOUND_RESPONSE_CODE, $response->getStatusCode());
        self::assertJson(strval($response->getBody()));
        self::assertContains(ResultRoutesProvider::CODE_RESPONSE_ATTRIBUTE, strval($response->getBody()));
        self::assertContains(ResultRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE, strval($response->getBody()));

        $responseBody = json_decode(strval($response->getBody()), true);

        self::assertEquals(ResultRoutesProvider::NOT_FOUND_RESPONSE_CODE,
            $responseBody[ResultRoutesProvider::CODE_RESPONSE_ATTRIBUTE]);
        self::assertEquals(ResultRoutesProvider::RESULT_NOT_FOUND,
            $responseBody[ResultRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE]);
    }

    /**
     * GET RESULT BY ID: Function to test 200 server response (Result information).
     */
    public function testGetResultOK()
    {
        $response = $this->runApp(ResultRoutesProviderTest::GET_HTTP_METHOD, '/results/1');

        self::assertEquals(ResultRoutesProvider::OK_RESPONSE_CODE, $response->getStatusCode());
        self::assertJson(strval($response->getBody()));

        $result = json_decode(strval($response->getBody()), true);

        self::assertArrayHasKey(Result::ID_ATTRIBUTE, $result);
        self::assertArrayHasKey(Result::USER_ATTRIBUTE, $result);
        self::assertArrayHasKey(Result::RESULT_ATTRIBUTE, $result);
        self::assertArrayHasKey(Result::TIME_ATTRIBUTE, $result);

        self::assertInternalType('int', $result[Result::ID_ATTRIBUTE]);
        self::assertEquals(ResultRoutesProviderTest::FIRST_RESULT_FOR_TESTS, $result[Result::RESULT_ATTRIBUTE]);
        self::assertInternalType('array', $result[Result::USER_ATTRIBUTE]);

        $user = $result[Result::USER_ATTRIBUTE];

        self::assertEquals(ResultRoutesProviderTest::USER_ID_FOR_TESTS, $user[User::ID_ATTRIBUTE]);
        self::assertEquals(UserRoutesProviderTest::FIRST_USER_NAME_FOR_TESTS, $user[User::USERNAME_ATTRIBUTE]);
        self::assertEquals(UserRoutesProviderTest::FIRST_USER_MAIL_FOR_TESTS, $user[User::EMAIL_ATTRIBUTE]);
        self::assertEquals(UserRoutesProviderTest::FIRST_ENABLED_USER_FOR_TESTS, $user[User::ENABLED_ATTRIBUTE]);
        self::assertEquals(UserRoutesProviderTest::FIRST_USER_TOKEN_FOR_TESTS, $user[User::TOKEN_ATTRIBUTE]);
    }

    /**
     * UPDATE RESULT: Function to test 404 server response (Result not found).
     */
    public function testUpdateResultNotFound()
    {
        $serializedUser = array();
        $serializedUser[Result::RESULT_ATTRIBUTE] = ResultRoutesProviderTest::SECOND_RESULT_FOR_TESTS;
        $response = $this->runApp(ResultRoutesProviderTest::PUT_HTTP_METHOD, '/results/9999999', $serializedUser);

        self::assertEquals(ResultRoutesProvider::NOT_FOUND_RESPONSE_CODE, $response->getStatusCode());
        self::assertJson(strval($response->getBody()));
        self::assertContains(ResultRoutesProvider::CODE_RESPONSE_ATTRIBUTE, strval($response->getBody()));
        self::assertContains(ResultRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE, strval($response->getBody()));

        $responseBody = json_decode(strval($response->getBody()), true);

        self::assertEquals(ResultRoutesProvider::NOT_FOUND_RESPONSE_CODE,
            $responseBody[ResultRoutesProvider::CODE_RESPONSE_ATTRIBUTE]);
        self::assertEquals(ResultRoutesProvider::RESULT_NOT_FOUND,
            $responseBody[ResultRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE]);
    }

    /**
     * UPDATE RESULT: Function to test 200 server response (Result has been updated).
     */
    public function testUpdateResultOK()
    {
        $serializedUser = array();
        $serializedUser[Result::USER_ID_ATTRIBUTE] = ResultRoutesProviderTest::USER_ID_FOR_TESTS;
        $serializedUser[Result::RESULT_ATTRIBUTE] = ResultRoutesProviderTest::SECOND_RESULT_FOR_TESTS;
        $response = $this->runApp(ResultRoutesProviderTest::PUT_HTTP_METHOD, '/results/1', $serializedUser);

        self::assertJson(strval($response->getBody()));

        $responseBody = json_decode(strval($response->getBody()), true);

        self::assertInternalType('array', $responseBody);
        self::assertEquals(ResultRoutesProvider::OK_RESPONSE_CODE, $response->getStatusCode());

        self::assertArrayHasKey(Result::ID_ATTRIBUTE, $responseBody);
        self::assertArrayHasKey(Result::USER_ATTRIBUTE, $responseBody);
        self::assertArrayHasKey(Result::RESULT_ATTRIBUTE, $responseBody);
        self::assertArrayHasKey(Result::TIME_ATTRIBUTE, $responseBody);

        self::assertInternalType('int', $responseBody[Result::ID_ATTRIBUTE]);
        self::assertEquals(ResultRoutesProviderTest::SECOND_RESULT_FOR_TESTS, $responseBody[Result::RESULT_ATTRIBUTE]);
        self::assertInternalType('array', $responseBody[Result::USER_ATTRIBUTE]);

        $user = $responseBody[Result::USER_ATTRIBUTE];

        self::assertEquals(ResultRoutesProviderTest::USER_ID_FOR_TESTS, $user[User::ID_ATTRIBUTE]);
        self::assertEquals(UserRoutesProviderTest::FIRST_USER_NAME_FOR_TESTS, $user[User::USERNAME_ATTRIBUTE]);
        self::assertEquals(UserRoutesProviderTest::FIRST_USER_MAIL_FOR_TESTS, $user[User::EMAIL_ATTRIBUTE]);
        self::assertEquals(UserRoutesProviderTest::FIRST_ENABLED_USER_FOR_TESTS, $user[User::ENABLED_ATTRIBUTE]);
        self::assertEquals(UserRoutesProviderTest::FIRST_USER_TOKEN_FOR_TESTS, $user[User::TOKEN_ATTRIBUTE]);
    }

    /**
     * UPDATE RESULT: Function to test 400 server response (User not found).
     */
    public function testUpdateResultBadRequest()
    {
        $serializedUser = array();
        $serializedUser[Result::USER_ID_ATTRIBUTE] = ResultRoutesProviderTest::INVALID_USER_ID_FOR_TESTS;
        $serializedUser[Result::RESULT_ATTRIBUTE] = ResultRoutesProviderTest::SECOND_RESULT_FOR_TESTS;
        $response = $this->runApp(ResultRoutesProviderTest::PUT_HTTP_METHOD, '/results/1', $serializedUser);

        self::assertEquals(ResultRoutesProvider::BAD_REQUEST_RESPONSE_CODE, $response->getStatusCode());
        self::assertJson(strval($response->getBody()));
        self::assertContains(ResultRoutesProvider::CODE_RESPONSE_ATTRIBUTE, strval($response->getBody()));
        self::assertContains(ResultRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE, strval($response->getBody()));

        $responseBody = json_decode(strval($response->getBody()), true);

        self::assertEquals(ResultRoutesProvider::BAD_REQUEST_RESPONSE_CODE,
            $responseBody[ResultRoutesProvider::CODE_RESPONSE_ATTRIBUTE]);
        self::assertEquals(ResultRoutesProvider::USER_NOT_FOUND,
            $responseBody[ResultRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE]);
    }

    /**
     * UPDATE RESULT: Function to test 422 server response (Result field is left out).
     */
    public function testUpdateResultUnprocessableEntity()
    {
        $serializedUser = array();
        $serializedUser[Result::USER_ID_ATTRIBUTE] = ResultRoutesProviderTest::USER_ID_FOR_TESTS;
        $response = $this->runApp(ResultRoutesProviderTest::PUT_HTTP_METHOD, '/results/1', $serializedUser);

        self::assertEquals(ResultRoutesProvider::UNPROCESSABLE_ENTITY_RESPONSE_CODE, $response->getStatusCode());
        self::assertJson(strval($response->getBody()));
        self::assertContains(ResultRoutesProvider::CODE_RESPONSE_ATTRIBUTE, strval($response->getBody()));
        self::assertContains(ResultRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE, strval($response->getBody()));

        $responseBody = json_decode(strval($response->getBody()), true);

        self::assertEquals(ResultRoutesProvider::UNPROCESSABLE_ENTITY_RESPONSE_CODE,
            $responseBody[ResultRoutesProvider::CODE_RESPONSE_ATTRIBUTE]);
        self::assertEquals(ResultRoutesProvider::RESULT_IS_LEFT_OUT,
            $responseBody[ResultRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE]);
    }

    /**
     * SUPPORTED HTTP METHODS: Function to test all supported HTTP methods (OPTIONS, GET, POST, PUT & DELETE).
     */
    public function testHTTPSupportedMethods()
    {
        $response = $this->runApp(ResultRoutesProviderTest::OPTIONS_HTTP_METHOD, '/results');

        self::assertEquals(ResultRoutesProvider::OK_RESPONSE_CODE, $response->getStatusCode());

        $supportedHTTPMethods = $response->getHeader('Allow');

        self::assertInternalType('array', $supportedHTTPMethods);
        self::assertArrayHasKey(0, $supportedHTTPMethods);

        $supportedHTTPMethods = array_shift($supportedHTTPMethods);

        self::assertContains(ResultRoutesProviderTest::OPTIONS_HTTP_METHOD, $supportedHTTPMethods);
        self::assertContains(ResultRoutesProviderTest::GET_HTTP_METHOD, $supportedHTTPMethods);
        self::assertContains(ResultRoutesProviderTest::POST_HTTP_METHOD, $supportedHTTPMethods);
        self::assertContains(ResultRoutesProviderTest::PUT_HTTP_METHOD, $supportedHTTPMethods);
        self::assertContains(ResultRoutesProviderTest::DELETE_HTTP_METHOD, $supportedHTTPMethods);
    }

    /**
     * DELETE RESULT: Function to test 404 server response (Result not found).
     */
    public function testDeleteResultNotFound()
    {
        $response = $this->runApp(ResultRoutesProviderTest::DELETE_HTTP_METHOD, '/results/9999999');

        self::assertEquals(ResultRoutesProvider::NOT_FOUND_RESPONSE_CODE, $response->getStatusCode());
        self::assertJson(strval($response->getBody()));
        self::assertContains(ResultRoutesProvider::CODE_RESPONSE_ATTRIBUTE, strval($response->getBody()));
        self::assertContains(ResultRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE, strval($response->getBody()));

        $responseBody = json_decode(strval($response->getBody()), true);

        self::assertEquals(ResultRoutesProvider::NOT_FOUND_RESPONSE_CODE,
            $responseBody[ResultRoutesProvider::CODE_RESPONSE_ATTRIBUTE]);
        self::assertEquals(ResultRoutesProvider::RESULT_NOT_FOUND,
            $responseBody[ResultRoutesProvider::MESSAGE_RESPONSE_ATTRIBUTE]);
    }

    /**
     * DELETE RESULT: Function to test 204 server response (Result deleted).
     */
    public function testDeleteResultOK()
    {
        $response = $this->runApp(ResultRoutesProviderTest::DELETE_HTTP_METHOD, '/results/1');

        self::assertEquals(ResultRoutesProvider::NO_CONTENT_RESPONSE_CODE, $response->getStatusCode());
        self::assertEmpty(strval($response->getBody()));
    }
}
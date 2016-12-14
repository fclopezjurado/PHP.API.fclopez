<?php   // tests/Functional/UserApiTest.php

namespace MiW16\Results\Tests\Functional;

/**
 * Class UserApiTest
 * @package MiW16\Results\Tests\Functional
 */
class UserApiTest extends BaseTestCase
{

    public function testGetAllUsers()
    {
        $response = $this->runApp('GET', '/users');

        self::assertEquals(200, $response->getStatusCode());
        self::assertJson((string) $response->getBody());
        self::assertContains('users', (string)$response->getBody());
    }

    public function testGetUser200()
    {
        $response = $this->runApp('GET', '/users/1');

        self::assertEquals(200, $response->getStatusCode());
        self::assertJson((string) $response->getBody());
        $usuario = json_decode((string) $response->getBody(), true);
        self::assertEquals(1, $usuario['id']);
        self::assertNotEmpty($usuario['username']);
    }

    public function testGetUser404()
    {
        $response = $this->runApp('GET', '/users/9999999999999');

        self::assertEquals(404, $response->getStatusCode());
    }
}
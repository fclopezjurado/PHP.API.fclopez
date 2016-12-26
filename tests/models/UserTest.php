<?php // tests/Entity/UserTest.php

namespace MiW16\Results\Tests\Models;

use MiW16\Results\Models\User;
use PHPUnit_Framework_Error_Notice;
use PHPUnit_Framework_Error_Warning;

/**
 * Class UserTest
 * @package MiW16\Results\Tests\Entity
 * @group users
 */
class UserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var User $user
     */
    protected $user;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        # Warning:
        PHPUnit_Framework_Error_Warning::$enabled = false;

        # notice, strict:
        PHPUnit_Framework_Error_Notice::$enabled = false;
        $this->user = new User();
    }

    /**
     * @covers \MiW16\Results\Models\User::__construct()
     */
    public function testConstructor()
    {
        self::assertEquals(0, $this->user->getId());
        self::assertEmpty($this->user->getUsername());
        self::assertEmpty($this->user->getEmail());
        self::assertFalse($this->user->isEnabled());
        self::assertNotNull($this->user->getToken());
    }

    /**
     * @covers \MiW16\Results\Models\User::getId()
     * @covers \MiW16\Results\Models\User::setId()
     */
    public function testGetSetId()
    {
        self::assertEquals(0, $this->user->getId());
        $this->user->setId(1);
        self::assertEquals(1, $this->user->getId());
    }

    /**
     * @covers \MiW16\Results\Models\User::setUsername()
     * @covers \MiW16\Results\Models\User::getUsername()
     */
    public function testGetSetUsername()
    {
        self::assertEmpty($this->user->getUsername());
        $userName = 'Testing \'setUsername()\' and \'getUsername()\' methods #' . rand(0, 10000);
        $this->user->setUsername($userName);
        self::assertEquals($userName, $this->user->getUsername());
    }

    /**
     * @covers \MiW16\Results\Models\User::getEmail()
     * @covers \MiW16\Results\Models\User::setEmail()
     */
    public function testGetSetEmail()
    {
        $userEmail = 'UsEr_' . rand(0, 10000) . '@example.com';
        self::assertEmpty($this->user->getEmail());
        $this->user->setEmail($userEmail);
        self::assertEquals($userEmail, $this->user->getEmail());
    }

    /**
     * @covers \MiW16\Results\Models\User::setEnabled()
     * @covers \MiW16\Results\Models\User::isEnabled()
     */
    public function testIsSetEnabled()
    {
        $this->user->setEnabled(true);
        self::assertTrue($this->user->isEnabled());
        $this->user->setEnabled(false);
        self::assertFalse($this->user->isEnabled());
    }

    /**
     * @covers \MiW16\Results\Models\User::getPassword()
     * @covers \MiW16\Results\Models\User::setPassword()
     * @covers \MiW16\Results\Models\User::validatePassword()
     */
    public function testGetSetPassword()
    {
        $password = 'UseR pa$?w0rD #' . rand(0, 1000);
        $this->user->setPassword($password);
        self::assertTrue(password_verify($password, $this->user->getPassword()));
        self::assertTrue($this->user->validatePassword($password));
    }

    /**
     * @covers \MiW16\Results\Models\User::getToken()
     * @covers \MiW16\Results\Models\User::setToken()
     */
    public function testGetSetToken()
    {
        $token = md5('UsEr tESt tOkEn #' . rand(0, 1000));
        $this->user->setToken($token);
        self::assertEquals($token, $this->user->getToken());
    }


    /**
     * @covers \MiW16\Results\Models\User::getLastLogin()
     * @covers \MiW16\Results\Models\User::setLastLogin()
     */
    public function testGetSetLastLogin()
    {
        $time = new \DateTime('now');
        $this->user->setLastLogin($time);
        self::assertEquals($time, $this->user->getLastLogin());
    }

    /**
     * @covers \MiW16\Results\Models\User::__toString()
     */
    public function testToString()
    {
        $username = 'USer Te$t nAMe #' . rand(0, 10000);
        $this->user->setUsername($username);
        self::assertEquals($username, $this->user->__toString());
    }

    /**
     * @covers \MiW16\Results\Models\User::jsonSerialize()
     */
    public function testJsonSerialize()
    {
        $json = json_encode($this->user);
        self::assertJson($json);
    }
}

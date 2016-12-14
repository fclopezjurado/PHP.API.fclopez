<?php   // tests/Entity/UserTest.php

namespace MiW16\Results\Tests\Entity;

use MiW16\Results\Entity\User;
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
     * @covers \MiW16\Results\Entity\User::__construct()
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
     * @covers \MiW16\Results\Entity\User::getId()
     */
    public function testGetId()
    {
        self::assertEquals(0, $this->user->getId());
    }

    /**
     * @covers \MiW16\Results\Entity\User::setUsername()
     * @covers \MiW16\Results\Entity\User::getUsername()
     */
    public function testGetSetUsername()
    {
        static::assertEmpty($this->user->getUsername());
        $username = 'UsEr TESt NaMe #' . rand(0, 10000);
        $this->user->setUsername($username);
        static::assertEquals($username, $this->user->getUsername());
    }

    /**
     * @covers \MiW16\Results\Entity\User::getEmail()
     * @covers \MiW16\Results\Entity\User::setEmail()
     */
    public function testGetSetEmail()
    {
        $userEmail = 'UsEr_' . rand(0, 10000) . '@example.com';
        static::assertEmpty($this->user->getEmail());
        $this->user->setEmail($userEmail);
        static::assertEquals($userEmail, $this->user->getEmail());
    }

    /**
     * @covers \MiW16\Results\Entity\User::setEnabled()
     * @covers \MiW16\Results\Entity\User::isEnabled()
     */
    public function testIsSetEnabled()
    {
        $this->user->setEnabled(true);
        self::assertTrue($this->user->isEnabled());

        $this->user->setEnabled(false);
        self::assertFalse($this->user->isEnabled());
    }

    /**
     * @covers \MiW16\Results\Entity\User::getPassword()
     * @covers \MiW16\Results\Entity\User::setPassword()
     * @covers \MiW16\Results\Entity\User::validatePassword()
     */
    public function testGetSetPassword()
    {
        $password = 'UseR pa$?w0rD #' . rand(0, 1000);
        $this->user->setPassword($password);
        self::assertTrue(password_verify($password, $this->user->getPassword()));
        self::assertTrue($this->user->validatePassword($password));
    }

    /**
     * @covers \MiW16\Results\Entity\User::getToken()
     * @covers \MiW16\Results\Entity\User::setToken()
     */
    public function testGetSetToken()
    {
        $token = md5('UsEr tESt tOkEn #' . rand(0, 1000));
        $this->user->setToken($token);
        self::assertEquals($token, $this->user->getToken());
    }


    /**
     * @covers \MiW16\Results\Entity\User::getLastLogin()
     * @covers \MiW16\Results\Entity\User::setLastLogin()
     */
    public function testGetSetLastLogin()
    {
        $time = new \DateTime('now');
        $this->user->setLastLogin($time);
        self::assertEquals($time, $this->user->getLastLogin());
    }

    /**
     * @covers \MiW16\Results\Entity\User::__toString()
     */
    public function testToString()
    {
        $username = 'USer Te$t nAMe #' . rand(0, 10000);
        $this->user->setUsername($username);
        self::assertEquals($username, $this->user->__toString());
    }

    /**
     * @covers \MiW16\Results\Entity\User::jsonSerialize()
     */
    public function testJsonSerialize()
    {
        $json = json_encode($this->user);
        self::assertJson($json);
    }
}

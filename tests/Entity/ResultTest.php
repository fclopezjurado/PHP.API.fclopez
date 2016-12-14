<?php   // tests/Entity/ResultTest.php

namespace MiW16\Results\Tests\Entity;

use MiW16\Results\Entity\Result;
use MiW16\Results\Entity\User;

/**
 * Class ResultTest
 * @package MiW16\Results\Tests\Entity
 */
class ResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \MiW16\Results\Entity\User $user
     */
    protected $user;

    /**
     * @var \MiW16\Results\Entity\Result $result
     */
    protected $result;

    const USERNAME = 'uSeR ñ¿?Ñ';
    const POINTS = 2016;
    /**
     * @var \DateTime $time
     */
    private $time;


    /**
     * Sets up the fixture.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->user = new User();
        $this->user->setUsername(self::USERNAME);
        $this->time = new \DateTime('now');
        $this->result = new Result(
            self::POINTS,
            $this->user,
            $this->time
        );
    }

    /**
     * Implement testConstructor
     *
     * @covers \MiW16\Results\Entity\Result::__construct()
     * @covers \MiW16\Results\Entity\Result::getId()
     * @covers \MiW16\Results\Entity\Result::getResult()
     * @covers \MiW16\Results\Entity\Result::getUser()
     * @covers \MiW16\Results\Entity\Result::getTime()
     */
    public function testConstructor()
    {
        $time = new \DateTime('now');
        $this->result = new Result(0, $this->user, $time);
        self::assertEmpty($this->result->getId());
        self::assertEmpty($this->result->getResult());
        self::assertNotEmpty($this->result->getUser());
        self::assertEquals(
            $time,
            $this->result->getTime()
        );
    }

    /**
     * Implement testGet_Id().
     *
     * @covers \MiW16\Results\Entity\Result::getId
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    public function testGet_Id()
    {
        self::assertEmpty($this->result->getId());
    }

    /**
     * Implement testUsername().
     *
     * @covers \MiW16\Results\Entity\Result::setResult
     * @covers \MiW16\Results\Entity\Result::getResult
     */
    public function testResult()
    {
        $this->result->setResult(self::POINTS);
        self::assertSame(
            self::POINTS,
            $this->result->getResult()
        );
    }

    /**
     * Implement testUser().
     *
     * @covers \MiW16\Results\Entity\Result::setUser()
     * @covers \MiW16\Results\Entity\Result::getUser()
     */
    public function testUser()
    {
        $this->result->setUser($this->user);
        self::assertSame(
            $this->user,
            $this->result->getUser()
        );
    }

    /**
     * Implement testTime().
     *
     * @covers \MiW16\Results\Entity\Result::setTime
     * @covers \MiW16\Results\Entity\Result::getTime
     */
    public function testTime()
    {
        $this->result->setTime($this->time);
        self::assertSame(
            $this->time,
            $this->result->getTime()
        );
    }

    /**
     * Implement testTo_String().
     *
     * @covers \MiW16\Results\Entity\Result::__toString
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    public function testTo_String()
    {
        $this->result->setUser($this->user);
        self::assertContains(
            self::USERNAME,
            $this->result->__toString()
        );
    }

    /**
     * Implement testJson_Serialize().
     *
     * @covers \MiW16\Results\Entity\Result::jsonSerialize
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    public function testJson_Serialize()
    {
        $json = $this->result->jsonSerialize();
        self::assertJson(json_encode($json));
        self::assertArrayHasKey('id', $json);
        self::assertArrayHasKey('result', $json);
        self::assertArrayHasKey('user', $json);
        self::assertArrayHasKey('time', $json);
    }
}

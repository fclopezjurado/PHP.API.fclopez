<?php   // tests/Entity/ResultTest.php

namespace MiW16\Results\Tests\Models;

use MiW16\Results\Models\Result;
use MiW16\Results\Models\User;

/**
 * Class ResultTest
 * @package MiW16\Results\Tests\Entity
 */
class ResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \MiW16\Results\Models\User $user
     */
    protected $user;

    /**
     * @var \MiW16\Results\Models\Result $result
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
     * @covers \MiW16\Results\Models\Result::__construct()
     * @covers \MiW16\Results\Models\Result::getId()
     * @covers \MiW16\Results\Models\Result::getResult()
     * @covers \MiW16\Results\Models\Result::getUser()
     * @covers \MiW16\Results\Models\Result::getTime()
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
     * Implement testGetSetId().
     *
     * @covers \MiW16\Results\Models\Result::getId
     * @covers \MiW16\Results\Models\Result::setId
     */
    public function testGetSetId()
    {
        self::assertEmpty($this->result->getId());
        $this->result->setId(1);
        self::assertEquals(1, $this->result->getId());
    }

    /**
     * Implement testUsername().
     *
     * @covers \MiW16\Results\Models\Result::setResult
     * @covers \MiW16\Results\Models\Result::getResult
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
     * @covers \MiW16\Results\Models\Result::setUser()
     * @covers \MiW16\Results\Models\Result::getUser()
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
     * @covers \MiW16\Results\Models\Result::setTime
     * @covers \MiW16\Results\Models\Result::getTime
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
     * @covers \MiW16\Results\Models\Result::__toString
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
     * @covers \MiW16\Results\Models\Result::jsonSerialize
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

<?php

namespace MiW16\Results\Tests\Routes;

use MiW16\Results\Routes\MainRoutesProvider;

/**
 * Class MainRoutesProviderTest
 * @package MiW16\Results\Tests\Routes
 */
class MainRoutesProviderTest extends RunAppTest
{
    const METHOD_NOT_ALLOWED_RESPONSE_CODE = 405;
    const METHOD_NOT_ALLOWED_RESPONSE_MESSAGE = 'Method not allowed';
    const GET_REQUEST = 'GET';
    const POST_REQUEST = 'POST';

    /**
     * Test that the index route returns a rendered response containing the text 'SlimFramework' but not a greeting
     */
    public function testGetRoot()
    {
        $response = $this->runApp(MainRoutesProviderTest::GET_REQUEST, MainRoutesProvider::ROOT_PATH);
        $this->assertEquals(MainRoutesProvider::FOUND_RESPONSE_CODE, $response->getStatusCode());
    }

    /**
     * Test that the index route won't accept a post request
     */
    public function testPostRoot()
    {
        $response = $this->runApp(MainRoutesProviderTest::POST_REQUEST, MainRoutesProvider::ROOT_PATH);

        $this->assertEquals(MainRoutesProviderTest::METHOD_NOT_ALLOWED_RESPONSE_CODE, $response->getStatusCode());
        $this->assertContains(MainRoutesProviderTest::METHOD_NOT_ALLOWED_RESPONSE_MESSAGE,
            strval($response->getBody()));
    }
}
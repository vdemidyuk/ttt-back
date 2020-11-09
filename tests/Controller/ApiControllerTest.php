<?php 

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiControllerTest extends WebTestCase
{

    private function _getResponseData(&$client)
    {
        return json_decode($client->getResponse()->getContent(), true);
    }

    public function testMakeMove404()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/v1/games',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"board":"----X----"}'
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $responseData = $this->_getResponseData($client);

        $this->assertArrayHasKey('location', $responseData);

        $client->request('GET', $responseData['location']);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request(
            'PUT',
            $responseData['location'],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"board":"OXX-X----"}'
        );

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testMakeMove()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/v1/games',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"board":"----X----"}'
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $responseData = $this->_getResponseData($client);

        $this->assertArrayHasKey('location', $responseData);

        $client->request('GET', $responseData['location']);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request(
            'PUT',
            $responseData['location'],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"board":"OX--X----"}'
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testDeleteGame404()
    {
        $client = static::createClient();

        $client->request('DELETE', '/api/v1/games/noID');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
    public function testDeleteGame()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/v1/games',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"board":"----X----"}'
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $responseData = $this->_getResponseData($client);

        $this->assertArrayHasKey('location', $responseData);

        $client->request('GET', $responseData['location']);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('DELETE', $responseData['location']);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', $responseData['location']);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testPostGame400()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/v1/games',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"board":"---------"}'
        );
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testPostGame()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/v1/games',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"board":"----X----"}'
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testGetGame404()
    {
        $client = static::createClient();
        $client->request('GET', '/api/v1/games/404id');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testGetGame()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/v1/games',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"board":"----X----"}'
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $responseData = $this->_getResponseData($client);

        $this->assertArrayHasKey('location', $responseData);
        $this->assertTrue(strlen($responseData['location']) > 0);

        $client->request('GET', $responseData['location']);

        $responseData = $this->_getResponseData($client);

        $this->assertArrayHasKey('id', $responseData);
        $this->assertArrayHasKey('board', $responseData);
        $this->assertArrayHasKey('status', $responseData);
    }

    public function testGamesList()
    {
        $client = static::createClient();

        $client->request('DELETE', '/api/v1/games');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            '/api/v1/games',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"board":"----X----"}'
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', '/api/v1/games');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $responseData = $this->_getResponseData($client);

        $this->assertIsArray($responseData);
        $this->assertCount(1, $responseData);
    }
}
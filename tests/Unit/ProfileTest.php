<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\Response;
use Cocoiti\TrackerGG\Apex\Profile;
use Cocoiti\TrackerGG\Apex\Exception\InvalidDataException;

class ProfileTest extends TestCase
{
    private $profile;

    protected function setUp(): void
    {
        $this->profile = new Profile();
    }

    public function testSetResponseWithValidData()
    {
        $responseData = [
            'data' => [
                'platformInfo' => ['platformUserHandle' => 'testuser'],
                'segments' => [
                    [
                        'stats' => [
                            'rankScore' => ['value' => 1000.5],
                            'arenaRankScore' => ['value' => 500.2],
                            'level' => ['value' => 50],
                            'kills' => ['value' => 100]
                        ]
                    ]
                ]
            ]
        ];

        $response = new Response(200, [], json_encode($responseData));
        $this->profile->setResponse($response);

        $this->assertEquals(1000, $this->profile->getRankRP());
        $this->assertEquals(500, $this->profile->getArenaRankRP());
        $this->assertEquals('testuser', $this->profile->getPlayerName());
        $this->assertEquals(50, $this->profile->getLevel());
        $this->assertEquals(100, $this->profile->getKills());
    }

    public function testSetResponseWithInvalidJson()
    {
        $this->expectException(InvalidDataException::class);
        $this->expectExceptionMessage('Failed to decode JSON response');

        $response = new Response(200, [], 'invalid json');
        $this->profile->setResponse($response);
    }

    public function testSetResponseWithMissingDataKey()
    {
        $this->expectException(InvalidDataException::class);
        $this->expectExceptionMessage('Invalid API response structure');

        $response = new Response(200, [], json_encode(['invalid' => 'structure']));
        $this->profile->setResponse($response);
    }

    public function testGetRankRPWithMissingData()
    {
        $responseData = [
            'data' => [
                'segments' => [
                    [
                        'stats' => []
                    ]
                ]
            ]
        ];

        $response = new Response(200, [], json_encode($responseData));
        $this->profile->setResponse($response);

        $this->assertEquals(0, $this->profile->getRankRP());
    }

    public function testGetArenaRankRPWithMissingData()
    {
        $responseData = [
            'data' => [
                'segments' => [
                    [
                        'stats' => []
                    ]
                ]
            ]
        ];

        $response = new Response(200, [], json_encode($responseData));
        $this->profile->setResponse($response);

        $this->assertEquals(0, $this->profile->getArenaRankRP());
    }

    public function testGetPlayerNameWithMissingData()
    {
        $responseData = [
            'data' => [
                'segments' => [
                    [
                        'stats' => []
                    ]
                ]
            ]
        ];

        $response = new Response(200, [], json_encode($responseData));
        $this->profile->setResponse($response);

        $this->assertEquals('', $this->profile->getPlayerName());
    }

    public function testGetLevelWithMissingData()
    {
        $responseData = [
            'data' => [
                'segments' => [
                    [
                        'stats' => []
                    ]
                ]
            ]
        ];

        $response = new Response(200, [], json_encode($responseData));
        $this->profile->setResponse($response);

        $this->assertEquals(0, $this->profile->getLevel());
    }

    public function testGetKillsWithMissingData()
    {
        $responseData = [
            'data' => [
                'segments' => [
                    [
                        'stats' => []
                    ]
                ]
            ]
        ];

        $response = new Response(200, [], json_encode($responseData));
        $this->profile->setResponse($response);

        $this->assertEquals(0, $this->profile->getKills());
    }

    public function testGetResponse()
    {
        $response = new Response(200, [], json_encode(['data' => []]));
        $this->profile->setResponse($response);

        $this->assertSame($response, $this->profile->getResponse());
    }
}
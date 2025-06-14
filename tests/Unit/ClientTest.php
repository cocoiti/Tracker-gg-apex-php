<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\RequestInterface;
use Cocoiti\TrackerGG\Apex\Client;
use Cocoiti\TrackerGG\Apex\Profile;
use Cocoiti\TrackerGG\Apex\Exception\InvalidDataException;
use Cocoiti\TrackerGG\Apex\Exception\ApiException;

class ClientTest extends TestCase
{
    private $mockGuzzleClient;
    private $client;

    protected function setUp(): void
    {
        $this->mockGuzzleClient = $this->createMock(GuzzleClient::class);
        
        $this->client = new Client('test-api-key');
        
        $reflection = new \ReflectionClass($this->client);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($this->client, $this->mockGuzzleClient);
    }

    public function testConstructorWithEmptyApiKey()
    {
        $this->expectException(InvalidDataException::class);
        $this->expectExceptionMessage('API key cannot be empty');
        
        new Client('');
    }

    public function testGetProfileWithInvalidPlatform()
    {
        $this->expectException(InvalidDataException::class);
        $this->expectExceptionMessage('invalid is not supported platform');
        
        $this->client->getProfile('testuser', 'invalid');
    }

    public function testGetProfileWithInvalidUserIdentifier()
    {
        $this->expectException(InvalidDataException::class);
        $this->expectExceptionMessage('test@user is not valid user identifier');
        
        $this->client->getProfile('test@user');
    }

    public function testGetProfileSuccess()
    {
        $responseData = [
            'data' => [
                'platformInfo' => ['platformUserHandle' => 'testuser'],
                'segments' => [
                    [
                        'stats' => [
                            'rankScore' => ['value' => 1000],
                            'arenaRankScore' => ['value' => 500],
                            'level' => ['value' => 50],
                            'kills' => ['value' => 100]
                        ]
                    ]
                ]
            ]
        ];

        $response = new Response(200, [], json_encode($responseData));
        
        $this->mockGuzzleClient
            ->expects($this->once())
            ->method('request')
            ->with('GET', 'https://public-api.tracker.gg/v2/apex/standard/profile/origin/testuser', [
                'headers' => ['TRN-Api-Key' => 'test-api-key']
            ])
            ->willReturn($response);

        $profile = $this->client->getProfile('testuser');
        
        $this->assertInstanceOf(Profile::class, $profile);
    }

    public function testRequestWithApiException()
    {
        $this->expectException(ApiException::class);
        
        $this->mockGuzzleClient
            ->expects($this->once())
            ->method('request')
            ->willThrowException(new RequestException('Network error', $this->createMock(RequestInterface::class)));

        $this->client->getProfile('testuser');
    }

    public function testRequestWithBadStatusCode()
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('API request failed with status: 404');
        
        $response = new Response(404, [], '');
        
        $this->mockGuzzleClient
            ->expects($this->once())
            ->method('request')
            ->willReturn($response);

        $this->client->getProfile('testuser');
    }

    public function testIsValidPlatform()
    {
        $reflection = new \ReflectionClass($this->client);
        $method = $reflection->getMethod('isValidPlatform');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke($this->client, 'origin'));
        $this->assertFalse($method->invoke($this->client, 'invalid'));
    }

    public function testIsValidPlatformUserIdentifier()
    {
        $reflection = new \ReflectionClass($this->client);
        $method = $reflection->getMethod('isValidPlatformUserIdentifier');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke($this->client, 'validuser123'));
        $this->assertTrue($method->invoke($this->client, 'user_name'));
        $this->assertFalse($method->invoke($this->client, 'user@name'));
        $this->assertFalse($method->invoke($this->client, 'user name'));
    }
}
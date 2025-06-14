<?php

namespace Cocoiti\TrackerGG\Apex;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response;
use Cocoiti\TrackerGG\Apex\Exception\ApiException;
use Cocoiti\TrackerGG\Apex\Exception\InvalidDataException;

/**
 * TrackerGG\Apex\Client
 */
class Client
{
    public const PLATFORM_ORIGIN = 'origin';
    public const PLATFORMS = [self::PLATFORM_ORIGIN];
    protected const BASE_URL = 'https://public-api.tracker.gg/v2/apex/standard/';
    protected string $apiKey = '';
    protected GuzzleClient $client;

    /**
     * construct
     *
     * @param string $api_key
     */
    public function __construct(string $api_key)
    {
        if (empty($api_key)) {
            throw new InvalidDataException('API key cannot be empty');
        }
        $this->apiKey = $api_key;
        $this->client = new GuzzleClient();
    }

    /**
     * get user profile
     *
     * @param string $platform_user_identifier
     * @param string $platform
     */
    public function getProfile(string $platform_user_identifier, string $platform = self::PLATFORM_ORIGIN)
    {
        if (!$this->isValidPlatform($platform)) {
            throw new InvalidDataException(sprintf('%s is not supported platform', $platform));
        }

        if (!$this->isValidPlatformUserIdentifier($platform_user_identifier)) {
            throw new InvalidDataException(sprintf('%s is not valid user identifier', $platform_user_identifier));
        }
        $path = sprintf('profile/%s/%s', $platform, $platform_user_identifier);

        $response = $this->request($path);

        $profile = new Profile();
        $profile->setResponse($response);

        return $profile;
    }

    /**
     * Make API request
     *
     * @param string $path
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function request(string $path): Response
    {
        try {
            $response = $this->client->request('GET', self::BASE_URL . $path, [
                'headers' => [
                    'TRN-Api-Key' => $this->apiKey,
                ],
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new ApiException('API request failed with status: ' . $response->getStatusCode());
            }

            return $response;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            throw new ApiException('API request failed: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * valid Platform
     *
     * @param string $platform
     * @return boolean
     */
    protected function isValidPlatform(string $platform): bool
    {
        return in_array($platform, self::PLATFORMS);
    }

    /**
     * valid user identifer
     *
     * @param string $user
     * @return boolean
     */
    protected function isValidPlatformUserIdentifier(string $platform_user_identifier): bool
    {
        return preg_match("/^[a-zA-Z0-9\_]+$/", $platform_user_identifier);
    }
}

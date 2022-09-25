<?php

namespace Cocoiti\TrackerGG\Apex;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response;

/**
 * TrackerGG\Apaex\Cleint
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
            throw new \InvalidArgumentException(sprintf('%s is not suport platform', $platform));
        }

        if (!$this->isValidPlatformUserIdentifier($platform_user_identifier)) {
            throw new \InvalidArgumentException(sprintf('%s is not invalid user string', $user));
        }
        $path = sprintf('profile/%s/%s', $platform, $platform_user_identifier);

        $response = $this->request($path);

        $profile = new Profile();
        $profile->setResponce($response);

        return $profile;
    }

    /**
     * Undocumented function
     *
     * @param string $path
     * @return \Response
     */
    protected function request(string $path): Response
    {
        $responce = $this->client->request('GET', self::BASE_URL . $path, [
            'headers' => [
                'TRN-Api-Key' => $this->apiKey,
            ],
        ]);

        return $responce;
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

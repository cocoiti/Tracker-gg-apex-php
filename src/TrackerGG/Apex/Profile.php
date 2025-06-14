<?php

namespace Cocoiti\TrackerGG\Apex;

use GuzzleHttp\Psr7\Response;
use Cocoiti\TrackerGG\Apex\Exception\InvalidDataException;

/**
 * TrackerGG\Apex\Profile
 */
class Profile
{
    protected \GuzzleHttp\Psr7\Response $response;
    protected array $responseData;

    public function setResponse(Response $response)
    {
        $this->response = $response;
        $body = $response->getBody()->getContents();
        
        $decodedData = json_decode($body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidDataException('Failed to decode JSON response: ' . json_last_error_msg());
        }
        
        $this->responseData = $decodedData;
        
        if (!isset($this->responseData['data'])) {
            throw new InvalidDataException('Invalid API response structure');
        }
    }

    public function getResponse(): \GuzzleHttp\Psr7\Response
    {
        return $this->response;
    }

    public function getRankRP(): int
    {
        $rankRP = $this->responseData['data']['segments'][0]['stats']['rankScore']['value'] ?? null;
        if (!is_numeric($rankRP)) {
            return 0;
        }
        return intval($rankRP);
    }

    public function getArenaRankRP(): int
    {
        $rankRP = $this->responseData['data']['segments'][0]['stats']['arenaRankScore']['value'] ?? null;
        if (!is_numeric($rankRP)) {
            return 0;
        }
        return intval($rankRP);
    }

    public function getPlayerName(): string
    {
        return $this->responseData['data']['platformInfo']['platformUserHandle'] ?? '';
    }

    public function getLevel(): int
    {
        $level = $this->responseData['data']['segments'][0]['stats']['level']['value'] ?? null;
        if (!is_numeric($level)) {
            return 0;
        }
        return intval($level);
    }

    public function getKills(): int
    {
        $kills = $this->responseData['data']['segments'][0]['stats']['kills']['value'] ?? null;
        if (!is_numeric($kills)) {
            return 0;
        }
        return intval($kills);
    }
}

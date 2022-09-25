<?php

namespace Cocoiti\TrackerGG\Apex;

use GuzzleHttp\Psr7\Response;

/**
 * TrackerGG\Apaex\Profile
 */
class Profile
{
    protected \GuzzleHttp\Psr7\Response $responce;
    protected array $responceData;

    public function setResponce(Response $responce)
    {
        $this->responce = $responce;
        $this->responceData = json_decode($responce->getBody(), true);
    }

    public function getResonce(Response $responce): GuzzleHttp\Psr7\Responce
    {
        return $responce;
    }

    public function getRankRP(): int
    {
        $rankRP = $this->responceData['data']['segments'][0]['stats']['rankScore']['value'] ?? null;
        if (!is_float($rankRP)) {
            $rankRP = 0;
        }
        return intval($rankRP);
    }

    public function getArenaRankRP(): int
    {
        $rankRP = $this->responceData['data']['segments'][0]['stats']['arenaRankScore']['value'] ?? null;
        if (!is_float($rankRP)) {
            $rankRP = 0;
        }
        return intval($rankRP);
    }
}

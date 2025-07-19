<?php

namespace App\HttpClient\EpisodeClient;

use App\Dto\EpisodeDto;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class EpisodeClient
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $apiUrl
    )
    {
    }

    /**
     * Получаем все эпизоды с пагинацией
     */
    public function findAllEpisodesByPage(int $page = 1): array
    {
        $response = $this->httpClient->request('GET', $this->apiUrl, [
            'query' => ['page' => $page]
        ]);

        $data =  $response->toArray();

        return [
            'info' => $data['info'],
            'episodes' => $this->transformApiData($data['results']),
        ];
    }

    /**
     * Получаем один эпизод по ID
     */
    public function findEpisodeById(int $id): ?EpisodeDto
    {
        $response = $this->httpClient->request('GET', $this->apiUrl.'/'.$id);

        if ($response->getStatusCode() !== 200) {
            return null;
        }

        $apiEpisode = $response->toArray();

        return $this->transformEpisodeData($apiEpisode);
    }

    private function transformApiData(array $apiEpisodes): array
    {
        return array_map(function($apiEpisode) {
            return $this->transformEpisodeData($apiEpisode);
        }, $apiEpisodes);
    }


    // Все преобразования тут по паттерну GRASP Information Expert
    private function transformEpisodeData(array $apiEpisode): EpisodeDto
    {
        return EpisodeDto::createFromArray([
            'id'                => $apiEpisode['id'],
            'name'              => $apiEpisode['name'],
            'air_date'          => $apiEpisode['air_date'],
            'episode'           => $apiEpisode['episode'],
            'characters'        => $apiEpisode['characters'],
            'season'            => $this->extractSeasonNumber($apiEpisode['episode']),
            'episode_number'    => $this->extractEpisodeNumber($apiEpisode['episode'])
        ]);
    }

    //
    private function extractSeasonNumber(string $episodeCode): int
    {
        return (int) substr(explode('E', $episodeCode)[0], 1);
    }

    private function extractEpisodeNumber(string $episodeCode): int
    {
        return (int) explode('E', $episodeCode)[1];
    }
}

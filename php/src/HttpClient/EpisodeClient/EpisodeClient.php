<?php

namespace App\HttpClient\EpisodeClient;

use App\Dto\EpisodeDto;
use Exception;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class EpisodeClient
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface     $logger,
        private string              $apiUrl
    )
    {
    }

    /**
     * Получаем все эпизоды с пагинацией
     */
    public function findAllEpisodesByPage(int $page = 1): array
    {
        try {
            $response = $this->httpClient->request('GET', $this->apiUrl, [
                'query' => ['page' => $page]
            ]);
            $data =  $response->toArray();
        } catch (Exception $exception) {
            $this->logger->error('В процессе получения списка эпизодов из API возникла ошибка',
                [
                    'exception' => $exception->getMessage()
                ]
            );
            throw new RuntimeException('Получение списка эпизодов временно недоступно');
        }

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
        try {
            $response = $this->httpClient->request('GET', $this->apiUrl.'/'.$id);
            $apiEpisode = $response->toArray();
        } catch (Exception $exception) {
            $this->logger->error('В процессе получения эпизода из API возникла ошибка',
                [
                    'exception' => $exception->getMessage()
                ]
            );
            throw new RuntimeException('Получение эпизодов временно недоступно');
        }

        return $this->transformEpisodeData($apiEpisode);
    }

    private function transformApiData(array $apiEpisodes): array
    {
        return array_map(function($apiEpisode) {
            return $this->transformEpisodeData($apiEpisode);
        }, $apiEpisodes);
    }

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

    private function extractSeasonNumber(string $episodeCode): int
    {
        return (int) substr(explode('E', $episodeCode)[0], 1);
    }

    private function extractEpisodeNumber(string $episodeCode): int
    {
        return (int) explode('E', $episodeCode)[1];
    }
}

<?php

namespace App\Repository;

use App\Dto\EpisodeDto;
use App\Dto\EpisodeViewDto;
use App\HttpClient\EpisodeClient\EpisodeClient;
use App\Repository\Contracts\EpisodeRepositoryInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

// Хранение в БД в этом классе не реализую, по причинам описанным ниже
// При уточнении ТЗ и необходимости просто подменим реализацию D из SOLID
class EpisodeRepository implements EpisodeRepositoryInterface
{
    protected const CACHE_TTL = 3600 * 24;

    public function __construct(
        private readonly EpisodeClient $episodeClient,
        private readonly CacheInterface $cache,
        private readonly ReviewRepository $reviewRepository
    ) {
    }

    //todo обработки ошибок
    /**
     * @param int $page
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     *
     * @inheritDoc
    */
    public function findAllEpisodesByPage(int $page = 1): array
    {
        // Предполагаем, что обращение к Api:
        // 1) Может быть платным
        // 2) Обновляется не чаще, чем раз в день
        $cacheKey = vsprintf('%s%s',['episodesPage', $page]);
        $episodesData = $this->cache->get($cacheKey, function (ItemInterface $item) use ($page) {
            $item->expiresAfter(self::CACHE_TTL);
            return $this->episodeClient->findAllEpisodesByPage($page); // Вычисление значения только если кэш пуст
        });

        return [
            'episodes' => $episodesData['episodes'],
            'pagination' => [
                'pages' => $episodesData['info']['pages'],
                'current' => $page,
                'count' => $episodesData['info']['count']
            ]
        ];
    }

    private function findEpisodeById(int $id): ?EpisodeDto
    {
        // Предполагаем, что обращение к Api:
        // 1) Может быть платным
        // 2) Обновляется не чаще, чем раз в день
        $cacheKey = vsprintf('%s%s',['episode', $id]);
        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($id) {
            $item->expiresAfter(3600);
            return $this->episodeClient->findEpisodeById($id);
        });
    }

    /**
     * @param int $id
     * @return array|null
     *
     * @inheritDoc
     */
    public function getEpisodeData(int $id): ?EpisodeViewDto
    {
        $episode = $this->findEpisodeById($id);
        $averageRating = $this->reviewRepository->getAverageRating($id);
        $latestReviews = $this->reviewRepository->findLatestReviews($id, 3);

        return EpisodeViewDto::createFromArray([
            'episode'       => $episode,
            'averageRating' => round($averageRating,2),
            'latestReviews' => $latestReviews,
        ]);
    }
}

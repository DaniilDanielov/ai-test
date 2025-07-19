<?php
namespace App\Repository;

use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Psr\Log\LoggerInterface;
use RuntimeException;

class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(
        protected ManagerRegistry $registry,
        private readonly LoggerInterface $logger,
    )
    {
        parent::__construct($registry, Review::class);
    }

    public function getAverageRating(int $episodeId): float
    {
        $query = $this->createQueryBuilder('r')
            ->select('AVG(r.sentimentRating)')
            ->where('r.episodeId = :episodeId')
            ->setParameter('episodeId', $episodeId)
            ->getQuery();

        try {
            $result = $query->getSingleScalarResult();
        } catch (Exception $exception) {
            $this->logger->error('В процессе получения средней оценки серии возникла ошибка',
                [
                    'exception' => $exception->getMessage()
                ]
            );
            throw new RuntimeException('Получение средней оценки отзывов временно недоступно');
        }

        return (float) $result;
    }

    /**
     * Получение последних отзывов
     *
     * @param int $episodeId
     * @param int $limit
     *
     * @return array
     */
    public function findLatestReviews(int $episodeId, int $limit = 3): array
    {
        $query = $this->createQueryBuilder('r')
            ->where('r.episodeId = :episodeId')
            ->setParameter('episodeId', $episodeId)
            ->orderBy('r.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery();

        try {
            $result =$query->getResult();
        } catch (Exception $exception) {
            $this->logger->error('При обращении последних отзывов возникла ошибка',
                [
                    'exception' => $exception->getMessage()
                ]
            );
            throw new \RuntimeException('Получение списка отзывов временно недоступно');
        }
        return $result;
    }
}

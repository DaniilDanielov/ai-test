<?php
namespace App\Services;

use App\Dto\CreateReviewDto;
use App\Entity\Review;
use App\Repository\EpisodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;

// Разместил в отдельном по 2 причинам:
// 1) Функция создания связана с обращениями к анализу текста и не совсем сочетается с концепцией Репозитория, как источника данных
// Репозиторию уж точно не обязательно иметь в зависимостях этот сервис
// На мой взгляд это разные функциональные единицы
// 2) При размещении в репозитории будет циклическая зависимости между 2 репозиториями
readonly class ReviewProcessor
{
    public function __construct(
        private LoggerInterface          $logger,
        private EpisodeRepository        $episodeRepository,
        private EntityManagerInterface   $em,
        private SentimentAnalysisService $analysisService
    )
    {
    }

    /**
     * @param CreateReviewDto $createReviewDto
     * @return bool
     */
    public function createReview(CreateReviewDto $createReviewDto): bool
    {
        $episodeId = $createReviewDto->getEpisodeId();

        $episode = $this->episodeRepository->getEpisodeData($episodeId);

        if (! $episode) {
            throw new \RuntimeException('Серия с таким идентификатором не найдена');
        }

        $review = new Review();
        $review->setEpisodeId($episodeId);
        $review->setAuthor($createReviewDto->getAuthor());
        $review->setContent($createReviewDto->getContent());

        $text = $createReviewDto->getContent();
        $reviewRating = $this->analysisService->analyzeText($text);
        $review->setSentimentRating($reviewRating);

        try {
            $this->em->persist($review);
            $this->em->flush();
        } catch (Exception $exception) {
            $this->logger->error('В процессе создания отзыва возникла ошибка',
                [
                    'exception' => $exception->getMessage()
                ]
            );
            return false;
        }

        return true;
    }
}

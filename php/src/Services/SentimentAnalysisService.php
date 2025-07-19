<?php

namespace App\Services;

use App\HttpClient\SentimentAnalysis\SentimentAnalysisClientInterface;
use Psr\Log\LoggerInterface;

readonly class SentimentAnalysisService
{
    // тут оставляю возможность подменить реализацию на другую API или библиотеку
    // todo Добавить реализацию
    public function __construct(
        private SentimentAnalysisClientInterface    $sentimentAnalysis,
        private LoggerInterface                     $logger
    )
    {
    }

    public function analyzeText(string $text): ?int
    {
        try {
            // todo заглушка
            $result = $this->sentimentAnalysis->getSentimentAnalysisResult($text);


            $result = $this->sentimentAnalysis->getSentimentAnalysisResult($text);
        } catch (\Exception $exception) {
            $this->logger->error('При обращении к сервису определения эмоциональной окраски текста возникла ошибка',
                [
                    'exception' => $exception->getMessage()
                ]
            );
            return null;
        }
        return $result;
    }
}

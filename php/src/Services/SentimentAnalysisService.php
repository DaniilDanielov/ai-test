<?php

namespace App\Services;

use App\HttpClient\SentimentAnalysis\SentimentAnalysisHuggingFaceClient;
use App\HttpClient\SentimentAnalysis\SentimentAnalysisNlpCloudClient;
use App\HttpClient\SentimentAnalysis\SentimentAnalysisProviderInterface;
use LanguageDetection\Language;
use Psr\Log\LoggerInterface;

readonly class SentimentAnalysisService
{
    public function __construct(
        private LoggerInterface $logger,
        private SentimentAnalysisNlpCloudClient $analysisNlpCloudClient,
        private SentimentAnalysisHuggingFaceClient $sentimentAnalysisHuggingFaceClient,
    )
    {
    }

    public function analyzeText(string $text): ?int
    {
        $ld = new Language(['ru', 'en']);
        $languageDetectionResult = $ld->detect($text)->bestResults();
        try {
            $sentimentServiceProvider = $this->resolveSentimentService($languageDetectionResult);
            $result = $sentimentServiceProvider->getSentimentAnalysisResult($text);
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

    protected function resolveSentimentService(
        string $locale,
    ): SentimentAnalysisProviderInterface {
        return match ($locale) {
            'en' => $this->analysisNlpCloudClient ,
            default => $this->sentimentAnalysisHuggingFaceClient,
        };
    }
}

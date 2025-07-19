<?php

namespace App\Factory;

use App\HttpClient\SentimentAnalysis\SentimentAnalysisClientInterface;
use App\HttpClient\SentimentAnalysis\SentimentAnalysisHuggingFaceClient;
use App\HttpClient\SentimentAnalysis\SentimentAnalysisNlpCloudClient;
use InvalidArgumentException;

class SentimentAnalysisClientFactory
{
    public static function createClient(string $implementation): SentimentAnalysisClientInterface
    {
        $sdfsdf = 'sdfsdf';
        return match ($implementation) {
//            'first' => new SentimentAnalysisNlpCloudClient(),
//            'second' => new SentimentAnalysisHuggingFaceClient(),
            default => throw new InvalidArgumentException('Получен неизвестный идентификатор клиента'),
        };
    }
}

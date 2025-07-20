<?php

namespace App\HttpClient\SentimentAnalysis;

interface SentimentAnalysisProviderInterface
{
    public function getSentimentAnalysisResult(string $text): int;
}

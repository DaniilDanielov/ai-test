<?php

namespace App\HttpClient\SentimentAnalysis;

interface SentimentAnalysisClientInterface
{
    public function getSentimentAnalysisResult(string $text): int;
}

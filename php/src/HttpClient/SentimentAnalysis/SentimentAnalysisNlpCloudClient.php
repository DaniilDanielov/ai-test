<?php

namespace App\HttpClient\SentimentAnalysis;

use App\Exception\SentimentIdentificationException;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

// Лучше подходит для английского текста, и ответ у нее удобнее парсить

// todo доделать фабрику
readonly class SentimentAnalysisNlpCloudClient implements SentimentAnalysisClientInterface
{
    protected const NLP_STATUS_MAPPING = [
        'NEGATIVE' => 0,
        'POSITIVE' => 1
    ];

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $apiUrl,
        private readonly string $apiKey,
        private readonly LoggerInterface $logger,
    )
    {
    }

    public function getSentimentAnalysisResult(string $text): int
    {
        $requestData = $this->prepareRequestData($text);
        $response = $this->httpClient->request('POST', $this->apiUrl, $requestData);

        $data = $response->toArray();

        if (isset($data['scored_labels'][0]['label'])) {
            $sentimentAnalysisResult = $data['scored_labels'][0]['label'];
            $result = static::NLP_STATUS_MAPPING[$sentimentAnalysisResult];
        } else {
            $this->logger->error('Ошибка интерпретации ответа API',[
                'request' => $requestData,
                'response' => $requestData,
                'url' => $this->apiUrl,
            ]);
            throw new SentimentIdentificationException();
        }

        return $result;
    }

    protected function prepareRequestData(string $text): array
    {
        $body = [
            "text" => $text,
            "target" => "NLP Cloud",
        ];

        return [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => $body,
        ];
    }
}

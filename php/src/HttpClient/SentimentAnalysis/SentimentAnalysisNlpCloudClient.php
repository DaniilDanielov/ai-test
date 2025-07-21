<?php

namespace App\HttpClient\SentimentAnalysis;

use App\Exception\SentimentIdentificationException;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

// Лучше подходит для английского текста, и ответ у нее удобнее парсить
readonly class SentimentAnalysisNlpCloudClient implements SentimentAnalysisProviderInterface
{
    protected const NLP_STATUS_MAPPING = [
        'NEGATIVE' => 0,
        'POSITIVE' => 1
    ];

    public function __construct(
        private string $apiUrl,
        private string $apiKey,
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger,
    )
    {
    }

    public function getSentimentAnalysisResult(string $text): int
    {
        $data = $this->getDataFromApi($text);
        if (isset($data['scored_labels'][0]['label'])) {
            $sentimentAnalysisResult = $data['scored_labels'][0]['label'];
            $result = static::NLP_STATUS_MAPPING[$sentimentAnalysisResult];
        } else {
            $this->logger->error('Ошибка интерпретации ответа API',[
                'data' => $data,
                'url' => $this->apiUrl,
            ]);
            throw new SentimentIdentificationException();
        }

        return $result;
    }

    protected function getDataFromApi(string $text): array
    {
        $requestData = $this->prepareRequestData($text);
        $response = $this->httpClient->request('POST', $this->apiUrl, $requestData);

        return $response->toArray();
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

<?php

namespace App\HttpClient\SentimentAnalysis;

use App\Exception\SentimentIdentificationException;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

// Хорошо работает со всеми языками, но не очень удобно парсить ответы
class SentimentAnalysisHuggingFaceClient implements SentimentAnalysisProviderInterface
{
    // Можно заменить на другие модели AI https://huggingface.co/models
    protected const DEFAULT_MODEL = 'deepseek-ai/DeepSeek-V3-0324';
    public function __construct(
        private readonly string $apiUrl,
        private readonly string $apiKey,
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
    )
    {
    }

    public function getSentimentAnalysisResult(string $text): int
    {
        $requestData = $this->prepareRequestData($text);
        $response = $this->httpClient->request('POST', $this->apiUrl, $requestData);

        $data = $response->toArray();

        if (isset($data['choices'][0]['message']['content'])) {
            $sentimentAnalysisResult = $data['choices'][0]['message']['content'];
            preg_match('/(?<rating>\d)/', $sentimentAnalysisResult, $matches);
        } else {
            $this->logger->error('Ошибка интерпретации ответа API',[
                'request' => $requestData,
                'response' => $requestData,
                'url' => $this->apiUrl,
                'model' => static::DEFAULT_MODEL,
            ]);
            throw new SentimentIdentificationException();
        }

        return (int) $matches['rating'];
    }

    protected function prepareRequestData(string $text): array
    {
        $query = <<<TXT
"I need the rating of the text '{$text}' only in json format with two fields rating 0 - for negative or neutral, 1 - for good answer only in json format"
TXT;

        $body = [
            "messages" => [
                [
                    "role" => "user",
                    "content" => $query
                ],
            ],
            "model" => static::DEFAULT_MODEL,
            "stream" => false
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

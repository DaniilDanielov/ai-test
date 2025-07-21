<?php

namespace App\Tests\Unit;

use App\HttpClient\SentimentAnalysis\SentimentAnalysisHuggingFaceClient;
use App\HttpClient\SentimentAnalysis\SentimentAnalysisNlpCloudClient;
use App\Services\SentimentAnalysisService;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SentimentAnalysisServiceTest extends KernelTestCase
{
    private array $testResponses;
    private SentimentAnalysisNlpCloudClient $analysisNlpCloudClient;
    private SentimentAnalysisHuggingFaceClient $analysisHuggingFaceClient;

    protected function setUp(): void
    {
        $file = sprintf('%s/Data/sentiment_api_data.json', __DIR__);
        $this->assertFileExists($file, 'Test responses file not found');

        $responsesContent = file_get_contents($file);
        $this->testResponses = json_decode($responsesContent, true, 512, JSON_THROW_ON_ERROR);

        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->analysisNlpCloudClient = $this->getMockBuilder(SentimentAnalysisNlpCloudClient::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getDataFromApi'])
            ->getMock();

        $this->analysisHuggingFaceClient = $this->getMockBuilder(SentimentAnalysisHuggingFaceClient::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getDataFromApi'])
            ->getMock();

        $this->service = new SentimentAnalysisService(
            $this->loggerMock,
            $this->analysisNlpCloudClient,
            $this->analysisHuggingFaceClient
        );
    }

    #[DataProvider('englishTextProvider')]
    public function testAnalyzeEnglishText(string $text, int $expectedResult, string $responseKey)
    {
        $this->analysisNlpCloudClient->expects($this->once())
            ->method('getDataFromApi')
            ->willReturn($this->testResponses[$responseKey]);
        $result = $this->service->analyzeText($text);
        $this->assertEquals($expectedResult, $result);
    }

    #[DataProvider('russianTextProvider')]
    public function testAnalyzeRussianText(string $text, int $expectedResult, string $responseKey)
    {
        $this->analysisHuggingFaceClient->expects($this->once())
            ->method('getDataFromApi')
            ->willReturn($this->testResponses[$responseKey]);
        $result = $this->service->analyzeText($text);
        $this->assertEquals($expectedResult, $result);
    }

    public static function englishTextProvider(): array
    {
        return [
            'English Positive' => ['Awesome episode', 1,'english_positive'],
            'English Negative' => ['Worst episode of the season', 0, 'english_negative'],
        ];
    }

    public static function russianTextProvider(): array
    {
        return [
            'Russian Positive' => ['Серия супер интересная', 1, 'russian_positive'],
            'Russian Negative' => ['Мне не понравилась серия', 0, 'russian_negative'],
        ];
    }
}

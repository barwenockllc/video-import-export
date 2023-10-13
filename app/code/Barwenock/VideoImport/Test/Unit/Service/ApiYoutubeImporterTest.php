<?php

declare(strict_types=1);

namespace Barwenock\VideoImport\Test\Unit\Service;

use PHPUnit\Framework\TestCase;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Barwenock\VideoImport\Service\ApiYoutubeImporter;

class ApiYoutubeImporterTest extends TestCase
{
    /**
     * @var ApiYoutubeImporter
     */
    private $apiYoutubeImporter;

    /**
     * @var Curl|\PHPUnit\Framework\MockObject\MockObject
     */
    private $curl;

    /**
     * @var ScopeConfigInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $scopeConfig;

    protected function setUp(): void
    {
        $this->curl = $this->createMock(Curl::class);
        $this->scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $this->apiYoutubeImporter = new ApiYoutubeImporter($this->curl, $this->scopeConfig);
    }

    public function testGetVideoInfoWithValidUrl()
    {
        // Mock the response from YouTube API
        $youtubeApiResponse = json_encode([
            'items' => [
                [
                    'snippet' => [
                        'title' => 'Video Title',
                        'description' => 'Video Description',
                        'thumbnails' => [
                            'default' => [
                                'url' => 'https://example.com/thumbnail.jpg',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        // Set up the expected behavior for the mock Curl object
        $this->curl->expects($this->once())
            ->method('get')
            ->willReturn($youtubeApiResponse);

        // Set up the API key in the mock ScopeConfigInterface
        $this->scopeConfig->method('getValue')
            ->willReturn('your-api-key');

        // Call the method under test
        $videoInfo = $this->apiYoutubeImporter->getVideoInfo('https://www.youtube.com/watch?v=your-video-id');

        // Perform assertions
        $this->assertEquals('Video Title', $videoInfo['title']);
        $this->assertEquals('Video Description', $videoInfo['description']);
        $this->assertEquals('https://example.com/thumbnail.jpg', $videoInfo['thumbnail_url']);
    }

    public function testGetVideoInfoWithInvalidUrl()
    {
        // Mock the HTTP request
        $this->curl->expects($this->never())
            ->method('get');

        // Call the method under test with an invalid URL
        $this->expectException(\Exception::class);
        $this->apiYoutubeImporter->getVideoInfo('invalid-url');
    }
}

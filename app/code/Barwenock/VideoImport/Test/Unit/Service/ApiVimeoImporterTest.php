<?php

declare(strict_types=1);

namespace Barwenock\VideoImport\Test\Unit\Service;

use PHPUnit\Framework\TestCase;
use Magento\Framework\HTTP\Client\Curl;
use Barwenock\VideoImport\Service\ApiVimeoImporter;

class ApiVimeoImporterTest extends TestCase
{
    /**
     * @var ApiVimeoImporter
     */
    private $apiVimeoImporter;

    /**
     * @var Curl|\PHPUnit\Framework\MockObject\MockObject
     */
    private $curl;

    protected function setUp(): void
    {
        $this->curl = $this->createMock(Curl::class);
        $this->apiVimeoImporter = new ApiVimeoImporter($this->curl);
    }

    public function testGetVideoInfoWithValidUrl()
    {
        // Call the method under test
        $videoInfo = $this->apiVimeoImporter->getVideoInfo('https://vimeo.com/538517463');

        // Perform assertions
        $this->assertEquals('The Journey', $videoInfo['title']);
        $this->assertEquals('', $videoInfo['description']);
        $this->assertEquals(
            'https://i.vimeocdn.com/video/1114984600-0774cc9743e095920042784a9a7cc5f304053a26ec7d06a290988e4e36799fa3-d_640',
            $videoInfo['thumbnail_url']
        );
    }

    public function testGetVideoInfoWithInvalidUrl()
    {
        // Mock the HTTP request
        $this->curl->expects($this->never())
            ->method('get');

        // Call the method under test with an invalid URL
        $this->expectException(\Exception::class);
        $this->apiVimeoImporter->getVideoInfo('invalid-url');
    }
}

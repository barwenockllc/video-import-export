<?php
/**
 * @author Barwenock
 * @copyright Copyright (c) Barwenock
 * @package Video Import Export for Magento 2
 */

declare(strict_types=1);

namespace Barwenock\VideoImportExport\Test\Unit\Service;

use PHPUnit\Framework\TestCase;
use Magento\Framework\HTTP\Client\Curl;
use Barwenock\VideoImportExport\Service\ApiVimeoImporter;

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

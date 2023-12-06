<?php
/**
 * @author Barwenock
 * @copyright Copyright (c) Barwenock
 * @package Video Import Export for Magento 2
 */

declare(strict_types=1);

namespace Barwenock\VideoImportExport\Test\Unit\Service;

class ApiVimeoImporterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Barwenock\VideoImportExport\Service\ApiVimeoImporter
     */
    protected $apiVimeoImporter;

    /**
     * @var \Magento\Framework\HTTP\Client\Curl|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $curl;

    protected function setUp(): void
    {
        $this->curl = new \Magento\Framework\HTTP\Client\Curl;
        $this->fileDriver = new \Magento\Framework\Filesystem\Driver\File;
        $this->apiVimeoImporter = new \Barwenock\VideoImportExport\Service\ApiVimeoImporter(
            $this->curl,
            $this->fileDriver
        );
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
        // Expect a LocalizedException with the specified error message
        $this->expectException(\Magento\Framework\Exception\LocalizedException::class);
        $this->expectExceptionMessage('No video information found.');

        // Call the method under test with an invalid URL
        $this->apiVimeoImporter->getVideoInfo('https://vimeo.com/347373734737');
    }
}

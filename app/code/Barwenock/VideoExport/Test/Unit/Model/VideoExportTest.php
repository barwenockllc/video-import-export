<?php

namespace Barwenock\VideoExport\Test\Unit\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Filesystem\DirectoryList;

class VideoExportTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Barwenock\VideoExport\Model\VideoExport
     */
    private $videoExport;

    /**
     * @var \Magento\Framework\File\Csv|\PHPUnit\Framework\MockObject\MockObject
     */
    private $csv;

    /**
     * @var ProductRepositoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $productRepositoryMock;

    /**
     * @var SearchCriteriaBuilder|\PHPUnit\Framework\MockObject\MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var \Barwenock\VideoExport\Helper\FilesystemHelper
     */
    private $filesystemHelper;

    /**
     * @var DirectoryList|\PHPUnit\Framework\MockObject\MockObject
     */
    private $directoryListMock;

    protected function setUp(): void
    {
        $this->productRepositoryMock = $this->createMock(ProductRepositoryInterface::class);
        $this->searchCriteriaBuilderMock = $this->createMock(SearchCriteriaBuilder::class);
        $this->directoryListMock = $this->createMock(DirectoryList::class);
        $this->csv = new \Magento\Framework\File\Csv(new \Magento\Framework\Filesystem\Driver\File);
        $this->filesystemHelper = new \Barwenock\VideoExport\Helper\FilesystemHelper;

        $this->videoExport = new \Barwenock\VideoExport\Model\VideoExport(
            $this->csv,
            $this->productRepositoryMock,
            $this->searchCriteriaBuilderMock,
            $this->filesystemHelper,
            $this->directoryListMock
        );
    }

    public function testExportVideoData()
    {
        // Prepare mock data for a test product
        $productMock = $this->createMock(\Magento\Catalog\Model\Product::class);
        $productSearchResultsMock = $this
            ->createMock(\Magento\Catalog\Api\Data\ProductSearchResultsInterface::class);

        $productMock
            ->method('getSku')
            ->willReturn('test_sku');

        $mediaGalleryEntry = $this->createMock(\Magento\Framework\DataObject::class);
        $mediaGalleryEntry
            ->method('getData')
            ->willReturn(['media_type' => 'external-video', 'video_url' => 'https://example.com/video.mp4']);

        $productMock
            ->method('getMediaGalleryImages')
            ->willReturn([$mediaGalleryEntry]);

        $productSearchResultsMock
            ->method('getItems')
            ->willReturn([$productMock]);

        // Set up expectations for method calls
        $this->productRepositoryMock
            ->expects($this->once())
            ->method('getList')
            ->willReturn($productSearchResultsMock);

        $this->searchCriteriaBuilderMock
            ->method('create')
            ->willReturn($this->createMock(\Magento\Framework\Api\SearchCriteria::class));

        $this->directoryListMock
            ->method('getPath')
            ->willReturn('/var/www/html/pub/media');

        // Call the method to be tested
        $this->videoExport->exportVideoData();

        // Check if the CSV file is created in the expected location
        $exportFilePath = $this->directoryListMock->getPath('media') . '/export/video.csv';
        $this->assertTrue(file_exists($exportFilePath));

        // Read the CSV file and check if it contains the expected data
        $csvData = $this->csv->getData($exportFilePath);
        $this->assertCount(2, $csvData); // Expecting header and one data row

        // Check header
        $this->assertEquals(['Product ID', 'Video URL'], $csvData[0]);

        // Check data row
        $this->assertEquals(['test_sku', 'https://example.com/video.mp4'], $csvData[1]);
    }
}

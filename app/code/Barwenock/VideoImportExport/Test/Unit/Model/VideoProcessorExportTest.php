<?php
/**
 * @author Barwenock
 * @copyright Copyright (c) Barwenock
 * @package Video Import Export for Magento 2
 */

declare(strict_types=1);

namespace Barwenock\VideoImportExport\Test\Unit\Model;

class VideoProcessorExportTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Barwenock\VideoImportExport\Model\Video\VideoProcessorExport
     */
    protected $videoExport;

    /**
     * @var \Magento\Framework\File\Csv|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $csv;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $productRepositoryMock;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $searchCriteriaBuilderMock;

    /**
     * @var \Barwenock\VideoImportExport\Helper\FilesystemHelper
     */
    protected $filesystemHelper;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $directoryListMock;

    protected function setUp(): void
    {
        $this->productRepositoryMock = $this
            ->createMock(\Magento\Catalog\Api\ProductRepositoryInterface::class);
        $this->searchCriteriaBuilderMock = $this
            ->createMock(\Magento\Framework\Api\SearchCriteriaBuilder::class);
        $this->directoryListMock = $this
            ->createMock(\Magento\Framework\App\Filesystem\DirectoryList::class);
        $this->csv = new \Magento\Framework\File\Csv(new \Magento\Framework\Filesystem\Driver\File);
        $this->filesystemHelper = $this
            ->createMock(\Barwenock\VideoImportExport\Helper\FilesystemHelper::class);

        $this->videoExport = new \Barwenock\VideoImportExport\Model\Video\VideoProcessorExport(
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
        $exportFilePath = $this->directoryListMock->getPath('media') . '/export/video/video.csv';
        $this->assertTrue(file_exists($exportFilePath));

        // Read the CSV file and check if it contains the expected data
        $csvData = $this->csv->getData($exportFilePath);
        $this->assertCount(2, $csvData); // Expecting header and one data row

        // Check header
        $this->assertEquals(['Product SKU', 'Video URL'], $csvData[0]);

        // Check data row
        $this->assertEquals(['test_sku', 'https://example.com/video.mp4'], $csvData[1]);
    }
}

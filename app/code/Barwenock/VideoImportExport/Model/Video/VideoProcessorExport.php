<?php
/**
 * @author Barwenock
 * @copyright Copyright (c) Barwenock
 * @package Video Import Export for Magento 2
 */

declare(strict_types=1);

namespace Barwenock\VideoImportExport\Model\Video;

class VideoProcessorExport
{
    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $csv;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Barwenock\VideoImportExport\Helper\FilesystemHelper
     */
    protected $filesystemHelper;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * @param \Magento\Framework\File\Csv $csv
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Barwenock\VideoImportExport\Helper\FilesystemHelper $filesystemHelper
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     */
    public function __construct(
        \Magento\Framework\File\Csv $csv,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Barwenock\VideoImportExport\Helper\FilesystemHelper $filesystemHelper,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList
    ) {
        $this->csv = $csv;
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filesystemHelper = $filesystemHelper;
        $this->directoryList = $directoryList;
    }

    /**
     * Export video execution
     *
     * @return void
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function exportVideoData()
    {
        $data = [['Product SKU', 'Video URL']];

        $products = $this->productRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        foreach ($products as $product) {
            $this->getVideoUrl($product, $data);
        }

        // Creation of folder & file
        $exportFile = $this->getExportFilePath();
        $this->filesystemHelper->ifFilePathExist($exportFile);

        $this->csv->appendData($exportFile, $data);
    }

    /**
     * Get video url from product for export
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param array $data
     * @return array
     */
    protected function getVideoUrl(\Magento\Catalog\Api\Data\ProductInterface $product, &$data)
    {
        $galleryImages = $product->getMediaGalleryImages();
        if ($galleryImages) {
            foreach ($galleryImages as $image) {
                $imageData = $image->getData();

                // Check media type
                if (isset($imageData['media_type']) && $imageData['media_type'] == 'external-video') {
                    $data[] = [$product->getSku(), $imageData['video_url']];
                }
            }
        }

        return $data;
    }

    /**
     * Get an export file path
     *
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function getExportFilePath(): string
    {
        $mediaDirectory = $this->directoryList->getPath('media');
        return $mediaDirectory . '/export/video/video.csv';
    }
}

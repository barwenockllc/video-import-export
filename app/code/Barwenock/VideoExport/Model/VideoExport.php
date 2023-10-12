<?php

namespace Barwenock\VideoExport\Model;

class VideoExport
{
    /**
     * @param \Magento\Framework\File\Csv $csv
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Barwenock\VideoExport\Helper\FilesystemHelper $filesystemHelper
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     */
    public function __construct(
        \Magento\Framework\File\Csv $csv,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Barwenock\VideoExport\Helper\FilesystemHelper $filesystemHelper,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList
    ) {
        $this->csv = $csv;
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filesystemHelper = $filesystemHelper;
        $this->directoryList = $directoryList;
    }

    public function exportVideoData()
    {
        $data = [['Product ID', 'Video URL']];

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
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param $data
     * @return array|void
     */
    protected function getVideoUrl(\Magento\Catalog\Api\Data\ProductInterface $product, &$data)
    {
        $galleryImages = $product->getMediaGalleryImages();
        if ($galleryImages) {
            foreach ($galleryImages as $image) {
                $imageData = $image->getData();

                // Check media type
                if (isset($imageData['media_type']) && $imageData['media_type'] == 'external-video') {
                    return $data[] = [$product->getSku(), $imageData['video_url']];
                }
            }
        }
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function getExportFilePath()
    {
        $mediaDirectory = $this->directoryList->getPath('media');
        return $mediaDirectory . '/export/video.csv';
    }
}

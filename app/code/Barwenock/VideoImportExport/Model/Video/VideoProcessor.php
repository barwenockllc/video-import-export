<?php
/**
 * @author Barwenock
 * @copyright Copyright (c) Barwenock
 * @package Video Import Export for Magento 2
 */

declare(strict_types=1);

namespace Barwenock\VideoImportExport\Model\Video;

class VideoProcessor
{
    /**
     * @var \Barwenock\VideoImportExport\Service\ApiProductUpdate
     */
    protected \Barwenock\VideoImportExport\Service\ApiProductUpdate $apiProductUpdate;

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected \Magento\Framework\Filesystem\DirectoryList $directoryList;

    /**
     * @var \Barwenock\VideoImportExport\Model\File\Reader
     */
    protected \Barwenock\VideoImportExport\Model\File\Reader $fileReader;

    /**
     * @param \Barwenock\VideoImportExport\Service\ApiProductUpdate $apiProductUpdate
     * @param \Magento\Framework\Filesystem\DirectoryList $directoryList
     * @param \Barwenock\VideoImportExport\Model\File\Reader $fileReader
     */
    public function __construct(
        \Barwenock\VideoImportExport\Service\ApiProductUpdate $apiProductUpdate,
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Barwenock\VideoImportExport\Model\File\Reader $fileReader
    ) {
        $this->apiProductUpdate = $apiProductUpdate;
        $this->directoryList = $directoryList;
        $this->fileReader = $fileReader;
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function process()
    {
        try {
            $csvFilePath = sprintf('%s/import/video/video.csv', $this->directoryList->getPath('media'));

            return $this->fileReader->processFile($csvFilePath);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}

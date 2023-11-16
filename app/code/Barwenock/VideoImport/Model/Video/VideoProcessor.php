<?php
/**
 * @author Barwenock
 * @copyright Copyright (c) Barwenock
 * @package Video Import for Magento 2
 */

declare(strict_types=1);

namespace Barwenock\VideoImport\Model\Video;

class VideoProcessor
{
    /**
     * @var \Barwenock\VideoImport\Service\ApiProductUpdate
     */
    protected \Barwenock\VideoImport\Service\ApiProductUpdate $apiProductUpdate;

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected \Magento\Framework\Filesystem\DirectoryList $directoryList;

    /**
     * @var \Barwenock\VideoImport\Model\File\Reader
     */
    protected \Barwenock\VideoImport\Model\File\Reader $fileReader;

    /**
     * @param \Barwenock\VideoImport\Service\ApiProductUpdate $apiProductUpdate
     * @param \Magento\Framework\Filesystem\DirectoryList $directoryList
     * @param \Barwenock\VideoImport\Model\File\Reader $fileReader
     */
    public function __construct(
        \Barwenock\VideoImport\Service\ApiProductUpdate $apiProductUpdate,
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Barwenock\VideoImport\Model\File\Reader $fileReader
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

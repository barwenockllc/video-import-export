<?php
/**
 * @author Barwenock
 * @copyright Copyright (c) Barwenock
 * @package Video Import Export for Magento 2
 */

declare(strict_types=1);

namespace Barwenock\VideoImportExport\Helper;

class FilesystemHelper
{
    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $file;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $driverFile;

    /**
     * @param \Magento\Framework\Filesystem\Io\File $file
     * @param \Magento\Framework\Filesystem\Driver\File $driverFile
     */
    public function __construct(
        \Magento\Framework\Filesystem\Io\File $file,
        \Magento\Framework\Filesystem\Driver\File $driverFile
    ) {
        $this->file = $file;
        $this->driverFile = $driverFile;
    }

    /**
     * Checks if file or dir exists by path
     *
     * @param string $filePath
     * @return void
     * @throws \Exception
     */
    public function ifFilePathExist($filePath)
    {
        try {
            $directory = $this->driverFile->getParentDirectory($filePath);

            if (!$this->driverFile->isDirectory($directory)) {
                $this->driverFile->createDirectory($directory);
            }

            if (!$this->file->fileExists($filePath)) {
                $this->driverFile->touch($filePath);
            }
        } catch (\Magento\Framework\Exception\FileSystemException $fileSystemException) {
            throw new \Magento\Framework\Exception\FileSystemException(
                __($fileSystemException->getMessage()),
                $fileSystemException->getCode(),
                $fileSystemException
            );
        }
    }
}

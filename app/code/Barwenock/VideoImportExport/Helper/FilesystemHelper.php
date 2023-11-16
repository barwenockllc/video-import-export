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
     * @param $filePath
     * @return void
     * @throws \Exception
     */
    public function ifFilePathExist($filePath)
    {
        try {
            $directory = dirname($filePath);

            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }

            if (!file_exists($filePath)) {
                touch($filePath);
            }
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}

<?php

namespace Barwenock\VideoExport\Helper;

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
            throw new \Exception($exception->getMessage());
        }
    }
}

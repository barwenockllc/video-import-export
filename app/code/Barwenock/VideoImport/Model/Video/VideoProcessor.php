<?php
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
     * @param \Barwenock\VideoImport\Service\ApiProductUpdate $apiProductUpdate
     * @param \Magento\Framework\Filesystem\DirectoryList $directoryList
     */
    public function __construct(
        \Barwenock\VideoImport\Service\ApiProductUpdate $apiProductUpdate,
        \Magento\Framework\Filesystem\DirectoryList       $directoryList,
    ) {
        $this->apiProductUpdate = $apiProductUpdate;
        $this->directoryList = $directoryList;
    }

    /**
     * @param $isConsole
     * @param $output
     * @return bool
     * @throws \Exception
     */
    public function process()
    {
        try {
            $csvFile = $this->directoryList->getPath('media') . '/import/video/video.csv';

            if (($handle = fopen($csvFile, "r")) !== false) {
                $row = 0;
                while (($data = fgetcsv($handle, 1000, ";")) !== false) {
                    if ($row == 0) {
                        $row++;
                        continue; // skip headers
                    }

                    $sku = $data[0];
                    $videos = explode(',', $data[1]);

                    foreach ($videos as $video) {
                        // Here, we call your method for each video code
                        $this->apiProductUpdate->updateProductWithExternalVideo(trim($video), $sku);
                    }

                    $row++;
                }
                fclose($handle);

                return true;
            } else {
                return false;
            }
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }
}

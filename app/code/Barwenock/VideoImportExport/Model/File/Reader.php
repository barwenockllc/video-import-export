<?php
/**
 * @author Barwenock
 * @copyright Copyright (c) Barwenock
 * @package Video Import Export for Magento 2
 */

declare(strict_types=1);

namespace Barwenock\VideoImportExport\Model\File;

class Reader
{
    /**
     * @var \Barwenock\VideoImportExport\Service\ApiProductUpdate
     */
    protected $apiProductUpdate;

    /**
     * @param \Barwenock\VideoImportExport\Service\ApiProductUpdate $apiProductUpdate
     */
    public function __construct(
        \Barwenock\VideoImportExport\Service\ApiProductUpdate $apiProductUpdate
    ) {
        $this->apiProductUpdate = $apiProductUpdate;
    }

    /**
     * @param $csvFilePath
     * @param $output
     * @return int
     * @throws \Exception
     */
    public function processFile($csvFilePath, $output = null)
    {
        try {
            if (($handle = fopen($csvFilePath, 'r')) !== false) {
                $row = 0;
                while (($data = fgetcsv($handle, 1000, ';')) !== false) {
                    if ($row++ === 0) {
                        continue; // skip headers
                    }

                    [$sku, $videoCodes] = $data;
                    $videos = explode(',', $videoCodes);

                    foreach ($videos as $video) {
                        $this->apiProductUpdate->updateProductWithExternalVideo(trim($video), $sku);
                    }

                    if ($output) {
                        $output->writeln(sprintf('<info>Processed SKU: %s</info>', $sku));
                    }
                }

                fclose($handle);

                if ($output) {
                    $output->writeln('<info>Finished processing CSV file.</info>');
                }

                return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
            } else {
                return \Magento\Framework\Console\Cli::RETURN_FAILURE;
            }
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}

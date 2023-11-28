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
     * @var \Magento\Framework\File\Csv
     */
    protected $csv;

    /**
     * @param \Barwenock\VideoImportExport\Service\ApiProductUpdate $apiProductUpdate
     * @param \Magento\Framework\File\Csv $csv
     */
    public function __construct(
        \Barwenock\VideoImportExport\Service\ApiProductUpdate $apiProductUpdate,
        \Magento\Framework\File\Csv $csv
    ) {
        $this->apiProductUpdate = $apiProductUpdate;
        $this->csv = $csv;
    }

    /**
     * Process csv file
     *
     * @param string $csvFilePath
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     * @throws \Exception
     */
    public function processFile($csvFilePath, $output = null)
    {
        try {
            $this->csv->setLineLength(1000);
            $this->csv->setDelimiter(';');

            $data = $this->csv->getData($csvFilePath);

            foreach ($data as $row => $rowData) {
                if ($row === 0) {
                    continue; // skip headers
                }

                [$sku, $videoCodes] = $rowData;
                $videos = explode(',', $videoCodes);

                foreach ($videos as $video) {
                    $this->apiProductUpdate->updateProductWithExternalVideo(trim($video), $sku);
                }

                if ($output instanceof \Symfony\Component\Console\Output\OutputInterface) {
                    $output->writeln(sprintf('<info>Processed SKU: %s</info>', $sku));
                }
            }

            if ($output instanceof \Symfony\Component\Console\Output\OutputInterface) {
                $output->writeln('<info>Finished processing CSV file.</info>');
            }

            return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __($exception->getMessage()),
                $exception->getCode(),
                $exception
            );
        }
    }
}

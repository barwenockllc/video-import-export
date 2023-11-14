<?php
/**
 * @author Barwenock
 * @copyright Copyright (c) Barwenock
 * @package Video Import for Magento 2
 */

declare(strict_types=1);

namespace Barwenock\VideoImport\Console\Command;

class ImportVideo extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected \Magento\Framework\Filesystem\DirectoryList $directoryList;

    /**
     * @var \Barwenock\VideoImport\Service\ApiProductUpdate
     */
    protected \Barwenock\VideoImport\Service\ApiProductUpdate $apiProductUpdate;

    /**
     * @param \Magento\Framework\Filesystem\DirectoryList $directoryList
     * @param \Barwenock\VideoImport\Service\ApiProductUpdate $apiProductUpdate
     * @param null $name
     */
    public function __construct(
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Barwenock\VideoImport\Service\ApiProductUpdate $apiProductUpdate,
        $name = null
    ) {
        $this->directoryList = $directoryList;
        $this->apiProductUpdate = $apiProductUpdate;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('import:video');
        $this->setDescription('Import videos');
        parent::configure();
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        $csvFile = $this->directoryList->getPath('media') . '/import/video/video.csv';
        if (!file_exists($csvFile)) {
            $output->writeln("<error>File not found</error>");
            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }

        if (($handle = fopen($csvFile, "r")) !== false) {
            $row = 0;
            while (($data = fgetcsv($handle, 1000, ";")) !== false) {
                if ($row == 0) {
                    ++$row;
                    continue; // skip headers
                }

                $sku = $data[0];
                $videos = explode(',', $data[1]);

                foreach ($videos as $video) {
                    // Here, we call your method for each video code
                    $this->apiProductUpdate->updateProductWithExternalVideo($video, $sku);
                }

                $output->writeln(sprintf('<info>Processed SKU: %s</info>', $sku));
                ++$row;
            }

            fclose($handle);

            $output->writeln("<info>Finished processing CSV file.</info>");
            return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
        } else {
            $output->writeln("<error>Cannot open file</error>");
            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }
    }
}

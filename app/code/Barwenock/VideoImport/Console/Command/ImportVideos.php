<?php

namespace Barwenock\VideoImport\Console\Command;

class ImportVideos extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    private $directoryList;

    /**
     * @param \Barwenock\VideoImport\Model\VideoProcessor $videoProcessor
     * @param \Magento\Framework\Filesystem\DirectoryList $directoryList
     * @param $name
     */
    public function __construct(
        \Barwenock\VideoImport\Service\ApiVideoImporter $apiVideoImporter,
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        $name = null
    ) {
        $this->apiVideoImporter = $apiVideoImporter;
        $this->directoryList = $directoryList;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('import:video');
        $this->setDescription('Import videos');
        parent::configure();
    }

    public function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        $csvFile = $this->directoryList->getPath('media') . '/import/video/video.csv';
        if (!file_exists($csvFile)) {
            $output->writeln("<error>File not found</error>");
            return;
        }

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
                    $this->apiVideoImporter->updateProductWithExternalVideo(trim($video), $sku);
                }

                $output->writeln("<info>Processed SKU: {$sku}</info>");
                $row++;
            }
            fclose($handle);

            $output->writeln("<info>Finished processing CSV file.</info>");
        } else {
            $output->writeln("<error>Cannot open file</error>");
        }
    }
}

<?php

namespace Barwenock\VideoImport\Console\Command;

use Barwenock\VideoImport\Model\VideoImportList;
use Magento\Framework\Filesystem\DirectoryList;

class ImportVideo extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    private DirectoryList $directoryList;

    /**
     * @var VideoImportList
     */
    private \Barwenock\VideoImport\Model\VideoImportList $videoImportList;

    /**
     * @param VideoImportList $videoImportList
     * @param DirectoryList $directoryList
     * @param null $name
     */
    public function __construct(
        \Barwenock\VideoImport\Model\VideoImportList $videoImportList,
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        $name = null
    ) {
        $this->videoImportList = $videoImportList;
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
            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
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
                    if (str_contains(trim($video), 'youtube.com')
                        && preg_match('/[?&]v=([a-zA-Z0-9_-]+)/', trim($video), $matches)) {
                         $this->videoImportList->getVideoProvider('youtube')
                            ->updateProductWithExternalVideo(trim($video), $sku);
                    } elseif (str_contains(trim($video), 'vimeo.com')
                        && preg_match('/vimeo\.com\/(\d+)/', trim($video), $matches)) {
                      // Logic for vimeo video update
                    } else {
                        $output->writeln("<error>No available service for import found</error>");
                        return \Magento\Framework\Console\Cli::RETURN_FAILURE;
                    }
                }

                $output->writeln("<info>Processed SKU: {$sku}</info>");
                $row++;
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

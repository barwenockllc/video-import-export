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
     * @var \Barwenock\VideoImport\Model\File\Reader
     */
    protected \Barwenock\VideoImport\Model\File\Reader $fileReader;

    /**
     * @param \Magento\Framework\Filesystem\DirectoryList $directoryList
     * @param \Barwenock\VideoImport\Service\ApiProductUpdate $apiProductUpdate
     * @param \Barwenock\VideoImport\Model\File\Reader $fileReader
     * @param null $name
     */
    public function __construct(
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Barwenock\VideoImport\Service\ApiProductUpdate $apiProductUpdate,
        \Barwenock\VideoImport\Model\File\Reader $fileReader,
        $name = null
    ) {
        $this->directoryList = $directoryList;
        $this->apiProductUpdate = $apiProductUpdate;
        $this->fileReader = $fileReader;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('video:import');
        $this->setDescription('Import videos');
        parent::configure();
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        $csvFilePath = sprintf('%s/import/video/video.csv', $this->directoryList->getPath('media'));

        if (!file_exists($csvFilePath)) {
            $output->writeln('<error>File not found</error>');
            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }

        return $this->fileReader->processFile($csvFilePath, $output);
    }
}

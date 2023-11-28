<?php
/**
 * @author Barwenock
 * @copyright Copyright (c) Barwenock
 * @package Video Import Export for Magento 2
 */

declare(strict_types=1);

namespace Barwenock\VideoImportExport\Console\Command;

class ImportVideo extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected \Magento\Framework\Filesystem\DirectoryList $directoryList;

    /**
     * @var \Barwenock\VideoImportExport\Service\ApiProductUpdate
     */
    protected \Barwenock\VideoImportExport\Service\ApiProductUpdate $apiProductUpdate;

    /**
     * @var \Barwenock\VideoImportExport\Model\File\Reader
     */
    protected \Barwenock\VideoImportExport\Model\File\Reader $fileReader;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected \Magento\Framework\Filesystem\Driver\File $file;

    /**
     * Construct
     *
     * @param \Magento\Framework\Filesystem\DirectoryList $directoryList
     * @param \Barwenock\VideoImportExport\Service\ApiProductUpdate $apiProductUpdate
     * @param \Barwenock\VideoImportExport\Model\File\Reader $fileReader
     * @param \Magento\Framework\Filesystem\Driver\File $file
     * @param string|null $name
     */
    public function __construct(
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Barwenock\VideoImportExport\Service\ApiProductUpdate $apiProductUpdate,
        \Barwenock\VideoImportExport\Model\File\Reader $fileReader,
        \Magento\Framework\Filesystem\Driver\File $file,
        string $name = null
    ) {
        $this->directoryList = $directoryList;
        $this->apiProductUpdate = $apiProductUpdate;
        $this->fileReader = $fileReader;
        $this->file = $file;
        parent::__construct($name);
    }

    /**
     * Configures the current command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('video:import');
        $this->setDescription('Import videos');
        parent::configure();
    }

    /**
     * Executes the current command
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        $csvFilePath = sprintf('%s/import/video/video.csv', $this->directoryList->getPath('media'));

        if (!$this->file->isExists($csvFilePath)) {
            $output->writeln('<error>File not found</error>');
            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }

        return $this->fileReader->processFile($csvFilePath, $output);
    }
}

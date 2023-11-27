<?php
/**
 * @author Barwenock
 * @copyright Copyright (c) Barwenock
 * @package Video Import Export for Magento 2
 */

declare(strict_types=1);

namespace Barwenock\VideoImportExport\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class VideoExport extends Command
{
    /**
     * @var \Barwenock\VideoImportExport\Model\Video\VideoProcessorExport
     */
    protected $videoProcessorExport;

    /**
     * @param \Barwenock\VideoImportExport\Model\Video\VideoProcessorExport $videoProcessorExport
     */
    public function __construct(
        \Barwenock\VideoImportExport\Model\Video\VideoProcessorExport $videoProcessorExport
    ) {
        $this->videoProcessorExport = $videoProcessorExport;
        parent::__construct();
    }

    /**
     * Configures the current command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('video:export');
        $this->setDescription('Export product video data to a CSV file');
        parent::configure();
    }

    /**
     * Executes the current command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->videoProcessorExport->exportVideoData();

            $output->writeln("<info>Video export complete.</info>");
            return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
        } catch (\Exception $exception) {
            $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));
            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }
    }
}

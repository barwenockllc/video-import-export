<?php
/**
 * @author Barwenock
 * @copyright Copyright (c) Barwenock
 * @package Video Import Export for Magento 2
 */

declare(strict_types=1);

namespace Barwenock\VideoImportExport\Console\Command;

class VideoExport extends \Symfony\Component\Console\Command\Command
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
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     */
    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
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

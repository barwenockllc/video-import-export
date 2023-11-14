<?php
/**
 * @author Barwenock
 * @copyright Copyright (c) Barwenock
 * @package Video Export for Magento 2
 */

declare(strict_types=1);

namespace Barwenock\VideoExport\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Barwenock\VideoExport\Model\VideoExport;

class VideosExportCommand extends Command
{
    /**
     * @var VideoExport
     */
    protected $videoExport;

    /**
     * @param VideoExport $videoExport
     */
    public function __construct(VideoExport $videoExport)
    {
        $this->videoExport = $videoExport;
        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('video:export');
        $this->setDescription('Export product video data to a CSV file');
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->videoExport->exportVideoData();

            $output->writeln("<info>Video export complete.</info>");
            return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
        } catch (\Exception $exception) {
            $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));
            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }
    }
}

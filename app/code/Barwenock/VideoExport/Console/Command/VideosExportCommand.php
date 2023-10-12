<?php

namespace Barwenock\VideoExport\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Barwenock\VideoExport\Model\VideoExport;

class VideosExportCommand extends Command
{
    protected $videoExport;

    public function __construct(VideoExport $videoExport)
    {
        $this->videoExport = $videoExport;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('video:export');
        $this->setDescription('Export product video data to a CSV file');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->videoExport->exportVideoData();

            $output->writeln("<info>Video export complete.</info>");
            return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
        } catch (\Exception $exception) {
            $output->writeln("<error>{$exception->getMessage()}</error>");
            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }
    }
}
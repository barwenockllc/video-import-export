<?php
/**
 * @author Barwenock
 * @copyright Copyright (c) Barwenock
 * @package Video Import Export for Magento 2
 */

declare(strict_types=1);

namespace Barwenock\VideoImportExport\Controller\Adminhtml\Export;

class VideoExportFile extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @var \Barwenock\VideoImportExport\Model\Video\VideoProcessorExport
     */
    protected $videoProcessorExport;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $file;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Barwenock\VideoImportExport\Model\Video\VideoProcessorExport $videoProcessorExport
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Filesystem\Driver\File $file
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Barwenock\VideoImportExport\Model\Video\VideoProcessorExport $videoProcessorExport,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Filesystem\Driver\File $file
    ) {
        parent::__construct($context);
        $this->videoProcessorExport = $videoProcessorExport;
        $this->fileFactory = $fileFactory;
        $this->file = $file;
    }

    /**
     * Controller for exporting file
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);

        try {
            $this->videoProcessorExport->exportVideoData();

            // Get the file path
            $filePath = $this->videoProcessorExport->getExportFilePath();

            // Create and send the file for download
            $resultRaw = $this->fileFactory->create(
                'video.csv',
                $this->file->fileGetContents($filePath),
                \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR
            );

            $this->messageManager->addSuccessMessage('Export was successfully');

            return $resultRaw;
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage('Error uploading file: ' . $exception->getMessage());
        }

        return $resultRedirect->setPath('video/index/index');
    }
}

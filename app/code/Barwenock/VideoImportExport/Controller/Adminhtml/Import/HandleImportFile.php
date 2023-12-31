<?php
/**
 * @author Barwenock
 * @copyright Copyright (c) Barwenock
 * @package Video Import Export for Magento 2
 */

declare(strict_types=1);

namespace Barwenock\VideoImportExport\Controller\Adminhtml\Import;

class HandleImportFile extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected \Magento\Framework\Filesystem $fileSystem;

    /**
     * @var \Barwenock\VideoImportExport\Model\Video\VideoProcessor
     */
    protected \Barwenock\VideoImportExport\Model\Video\VideoProcessor $videoProcessor;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Barwenock\VideoImportExport\Model\Video\VideoProcessor $videoProcessor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Barwenock\VideoImportExport\Model\Video\VideoProcessor $videoProcessor
    ) {
        parent::__construct($context);
        $this->fileSystem = $filesystem;
        $this->videoProcessor = $videoProcessor;
    }

    /**
     * Controller for handling import file
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        $uploadedFile = $this->getRequest()->getFiles('file_upload');

        if ($uploadedFile && isset($uploadedFile['name']) && $uploadedFile['name']) {
            try {
                $targetDir = $this->fileSystem->getDirectoryWrite(
                    \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
                )->getAbsolutePath('import/video');

                $uploader = new \Magento\Framework\File\Uploader($uploadedFile);
                $uploader->setAllowedExtensions(['csv']);
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);

                $uploader->save($targetDir, $uploadedFile['name']);

                $ifVideosImported = $this->videoProcessor->process();
                if ($ifVideosImported === 0) {
                    $this->messageManager->addSuccessMessage('Import was successfully.');
                } else {
                    $this->messageManager->addErrorMessage('During video importing error happened');
                }
            } catch (\Exception $exception) {
                $this->messageManager->addErrorMessage('Error uploading file: ' . $exception->getMessage());
            }
        } else {
            $this->messageManager->addErrorMessage('Please select a file to upload.');
        }

        return $resultRedirect->setPath('video/index/index');
    }
}

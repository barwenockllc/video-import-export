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
     * @var \Barwenock\VideoImportExport\Model\Video\VideoProcessorExport
     */
    protected $videoProcessorExport;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Barwenock\VideoImportExport\Model\Video\VideoProcessorExport $videoProcessorExport
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Barwenock\VideoImportExport\Model\Video\VideoProcessorExport $videoProcessorExport,
    ) {
        parent::__construct($context);
        $this->videoProcessorExport = $videoProcessorExport;
    }

    /**
     * Controller for exporting file
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);

        try {
            $this->videoProcessorExport->exportVideoData();
            $this->messageManager->addSuccessMessage('Export was successfully');
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage('Error uploading file: ' . $exception->getMessage());
        }

        return $resultRedirect->setPath('video/index/index');
    }
}

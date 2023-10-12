<?php

namespace Barwenock\VideoExport\Controller\Adminhtml\Export;

class VideoExport extends \Magento\Backend\App\Action
{
    /**
     * @var \Barwenock\VideoExport\Model\VideoExport
     */
    protected $videoExport;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Barwenock\VideoExport\Model\VideoExport $videoExport
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Barwenock\VideoExport\Model\VideoExport $videoExport,
    ) {
        parent::__construct($context);
        $this->videoExport = $videoExport;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page|(\Magento\Framework\View\Result\Page&\Magento\Framework\Controller\ResultInterface)
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);

        try {
            $this->videoExport->exportVideoData();
            $this->messageManager->addSuccessMessage('Export was successfully');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage('Error uploading file: ' . $e->getMessage());
        }

        return $resultRedirect->setPath('video/import/videoImport');
    }
}

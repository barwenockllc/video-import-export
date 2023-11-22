<?php
/**
 * @author Barwenock
 * @copyright Copyright (c) Barwenock
 * @package Video Import Export for Magento 2
 */

declare(strict_types=1);

namespace Barwenock\VideoImportExport\Block\Adminhtml;

class Index extends \Magento\Backend\Block\Template
{
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('video/import/handleImportFile');
    }

    /**
     * @return string
     */
    public function getExportAction()
    {
        return $this->getUrl('video/export/videoExportFile');
    }
}

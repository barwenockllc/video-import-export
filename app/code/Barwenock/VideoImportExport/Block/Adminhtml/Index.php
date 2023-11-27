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
     * Get controller url for form
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('video/import/handleImportFile');
    }

    /**
     * Get controller url for export action
     *
     * @return string
     */
    public function getExportAction()
    {
        return $this->getUrl('video/export/videoExportFile');
    }
}

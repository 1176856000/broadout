<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ReviewsImportExport
 */


namespace Amasty\ReviewsImportExport\Controller\Adminhtml\Reviews;

use Amasty\Base\Controller\Adminhtml\Import\Download as BaseDownload;
use Magento\Backend\App\Action;

class Download extends \Magento\Backend\App\Action
{
    /**
     * @var BaseDownload
     */
    private $download;

    public function __construct(BaseDownload $download, Action\Context $context)
    {
        parent::__construct($context);
        $this->download = $download;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        return $this->download->downloadSample('Amasty_ReviewsImportExport');
    }
}

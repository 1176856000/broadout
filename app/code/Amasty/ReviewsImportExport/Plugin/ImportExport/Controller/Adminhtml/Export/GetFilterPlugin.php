<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ReviewsImportExport
 */


namespace Amasty\ReviewsImportExport\Plugin\ImportExport\Controller\Adminhtml\Export;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\ImportExport\Model\Export as ModelExport;
use Amasty\ReviewsImportExport\Api\ImportExport\ExportInterface;

class GetFilterPlugin
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ResultFactory
     */
    private $resultFactory;

    /**
     * @var MessageManager
     */
    private $messageManager;

    /**
     * @var ExportPlugin
     */
    private $export;

    public function __construct(
        RequestInterface $request,
        ResultFactory $resultFactory,
        MessageManager $messageManager,
        ModelExport $export
    ) {
        $this->request = $request;
        $this->resultFactory = $resultFactory;
        $this->messageManager = $messageManager;
        $this->export = $export;
    }

    /**
     * @param $subject
     * @param \Closure $proceed
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     * |\Magento\Framework\View\Result\Layout|mixed
     */
    public function aroundExecute($subject, \Closure $proceed)
    {
        $data = $this->request->getParams();
        if (!in_array($data['entity'], ExportInterface::EXPORT_TYPES)) {
            return $proceed();
        }
        if ($this->request->isXmlHttpRequest() && $data) {
            $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
            /** @var $resultLayout \Magento\Framework\View\Result\Layout */
            $resultLayout->getDefaultLayoutHandle();

            $resultLayout->getLayout()->addBlock(
                \Amasty\ReviewsImportExport\Block\Adminhtml\Export\Filter::class,
                ExportInterface::BLOCK_NAME,
                'root'
            );

            $this->export->setData($data);

            return $resultLayout;
        } else {
            $this->messageManager->addErrorMessage(__('Please correct the data sent.'));
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('adminhtml/*/index');

        return $resultRedirect;
    }
}

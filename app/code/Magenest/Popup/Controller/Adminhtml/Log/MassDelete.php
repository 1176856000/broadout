<?php
namespace Magenest\Popup\Controller\Adminhtml\Log;

use Magento\Ui\Component\MassAction;
use Psr\Log\LoggerInterface;

/**
 * Class MassDelete
 * @package Magenest\Popup\Controller\Adminhtml\Log
 */
class MassDelete extends \Magento\Backend\App\Action {
    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $_filer;
    /**
     * @var \Magenest\Popup\Model\ResourceModel\Log\CollectionFactory
     */
    protected $_popupLogCollectionFactory;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * MassDelete constructor.
     * @param \Magenest\Popup\Model\ResourceModel\Log\CollectionFactory $popupLogCollectionFactory
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magenest\Popup\Model\ResourceModel\Log\CollectionFactory $popupLogCollectionFactory,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Backend\App\Action\Context $context
    ){
        $this->_popupLogCollectionFactory = $popupLogCollectionFactory;
        $this->_filer = $filter;
        $this->_logger = $logger;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try{
            $collection = $this->_filer->getCollection($this->_popupLogCollectionFactory->create());
            $count = 0;
            foreach ($collection->getItems() as $item){
                $item->delete();
                $count++;
            }
            $this->messageManager->addSuccess(
                __('A total of %1 record(s) have been deleted.', $count)
            );
        }catch (\Exception $exception){
            $this->messageManager->addError($exception->getMessage());
            $this->_logger->critical($exception->getMessage());
        }
        return $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }
}
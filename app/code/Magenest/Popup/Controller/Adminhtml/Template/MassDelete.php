<?php
namespace Magenest\Popup\Controller\Adminhtml\Template;

/**
 * Class MassDelete
 * @package Magenest\Popup\Controller\Adminhtml\Template
 */
class MassDelete extends \Magenest\Popup\Controller\Adminhtml\Template\Template {

    /** @var \Magenest\Popup\Model\ResourceModel\Template\CollectionFactory $collectionFactory */
    protected $collectionFactory;

    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $_filter;

    /**
     * MassDelete constructor.
     * @param \Magenest\Popup\Model\ResourceModel\Template\CollectionFactory $collectionFactory
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Magenest\Popup\Model\PopupFactory $popupFactory
     * @param \Magenest\Popup\Model\TemplateFactory $popupTemplateFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magenest\Popup\Model\ResourceModel\Template\CollectionFactory $collectionFactory,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magenest\Popup\Model\PopupFactory $popupFactory,
        \Magenest\Popup\Model\TemplateFactory $popupTemplateFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ){
        $this->collectionFactory = $collectionFactory;
        $this->_filter = $filter;
        parent::__construct($popupFactory, $popupTemplateFactory, $logger, $coreRegistry, $context, $resultPageFactory);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try{
            $collection = $this->_filter->getCollection($this->collectionFactory->create());
            $count = 0;
            $templateIds = [];
            /** @var \Magenest\Popup\Model\Template $item */
            foreach ($collection->getItems() as $item) {
                if($this->getPopupsByTemplateId($item->getTemplateId())){
                    $message = __('%1 is currently being used for a popup. You must remove the template from this configuration before deleting the template',$item->getTemplateName());
                    throw new \Exception($message);
                }
                $templateIds[] = $item->getTemplateId();
                $count++;
            }
            /** @var \Magenest\Popup\Model\Template $templateModel */
            $templateModel = $this->_popupTemplateFactory->create();
            $templateModel->deleteMultiple($templateIds);
            $this->messageManager->addSuccess(
                __('A total of %1 record(s) have been deleted.', $count)
            );
        }catch (\Exception $e){
            $this->messageManager->addError($e->getMessage());
            $this->_logger->critical($e->getMessage());
        }
        return $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }
}
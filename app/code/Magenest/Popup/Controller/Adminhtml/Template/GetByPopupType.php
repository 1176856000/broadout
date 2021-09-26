<?php


namespace Magenest\Popup\Controller\Adminhtml\Template;


/**
 * Class GetByPopupType
 * @package Magenest\Popup\Controller\Adminhtml\Template
 */
class GetByPopupType extends \Magento\Backend\App\Action
{
    /**
     * @var \Magenest\Popup\Model\ResourceModel\Template\CollectionFactory
     */
    protected $_popupTemplateCollection;
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_jsonResult;

    /**
     * GetByPopupType constructor.
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonResult
     * @param \Magenest\Popup\Model\ResourceModel\Template\CollectionFactory $popupTemplateCollection
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $jsonResult,
        \Magenest\Popup\Model\ResourceModel\Template\CollectionFactory $popupTemplateCollection,
        \Magento\Backend\App\Action\Context $context)
    {
        $this->_jsonResult = $jsonResult;
        $this->_popupTemplateCollection = $popupTemplateCollection;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = $this->_jsonResult->create();
        $popupType = $this->_request->getParam('popup_type');
        $templateCollection = $this->_popupTemplateCollection->create();
        if ($popupType) {
            $templateCollection->addFieldToFilter('template_type', $popupType);
        }
        $result->setData($templateCollection->toOptionArray());
        return $result;
    }
}
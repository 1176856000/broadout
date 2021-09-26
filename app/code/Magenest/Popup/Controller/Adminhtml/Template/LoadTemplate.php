<?php


namespace Magenest\Popup\Controller\Adminhtml\Template;


/**
 * Class LoadTemplate
 * @package Magenest\Popup\Controller\Adminhtml\Template
 */
class LoadTemplate extends \Magento\Backend\App\Action
{
    /**
     * @var \Magenest\Popup\Model\ResourceModel\Template
     */
    protected $_templateResource;
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_jsonResult;
    /**
     * @var \Magenest\Popup\Model\TemplateFactory
     */
    protected $_templateModel;

    /**
     * LoadTemplate constructor.
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonResult
     * @param \Magenest\Popup\Model\ResourceModel\Template $templateResource
     * @param \Magenest\Popup\Model\TemplateFactory $templateModel
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $jsonResult,
        \Magenest\Popup\Model\ResourceModel\Template $templateResource,
        \Magenest\Popup\Model\TemplateFactory $templateModel,
        \Magento\Backend\App\Action\Context $context)
    {
        $this->_jsonResult = $jsonResult;
        $this->_templateModel = $templateModel;
        $this->_templateResource = $templateResource;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = $this->_jsonResult->create();
        $templateId = $this->_request->getParam('template_id');
        $template = $this->_templateModel->create();
        $this->_templateResource->load($template,$templateId);
        $content = $template->getHtmlContent() ?? null;
        $result->setData($content);
        return $result;
    }
}
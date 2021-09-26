<?php

namespace Magenest\Popup\Setup;

use Magenest\Popup\Model\LogFactory;
use Magenest\Popup\Model\PopupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

/**
 * Class UpgradeData
 * @package Magenest\Popup\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Magenest\Popup\Helper\Data
     */
    protected $_helperData;
    /**
     * @var \Magenest\Popup\Model\TemplateFactory
     */
    protected $_popupTemplateFactory;
    /**
     * @var \Magenest\Popup\Model\ResourceModel\Template\CollectionFactory
     */
    protected $_popupTemplateCollection;
    /**
     * @var \Magenest\Popup\Model\LogFactory
     */
    private   $_logModel;
    /**
     * @var \Magenest\Popup\Model\PopupFactory
     */
    private   $_popupModel;
    /**
     * @var \Magenest\Popup\Model\ResourceModel\Popup\CollectionFactory
     */
    protected $_popupCollection;
    /**
     * @var \Magenest\Popup\Model\ResourceModel\Popup
     */
    protected $_popupResource;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $_json;

    /**
     * UpgradeData constructor.
     * @param \Magenest\Popup\Model\ResourceModel\Popup\CollectionFactory $popupCollection
     * @param \Magenest\Popup\Model\ResourceModel\Popup $popupResource
     * @param \Magenest\Popup\Helper\Data $helperData
     * @param \Magenest\Popup\Model\TemplateFactory $popupTemplateFactory
     * @param \Magenest\Popup\Model\ResourceModel\Template\CollectionFactory $popupTemplateCollection
     * @param \Magenest\Popup\Model\LogFactory $logModel
     * @param \Magenest\Popup\Model\PopupFactory $popupModel
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magenest\Popup\Model\ResourceModel\Popup\CollectionFactory $popupCollection,
        \Magenest\Popup\Model\ResourceModel\Popup $popupResource,
        \Magenest\Popup\Helper\Data $helperData,
        \Magenest\Popup\Model\TemplateFactory $popupTemplateFactory,
        \Magenest\Popup\Model\ResourceModel\Template\CollectionFactory $popupTemplateCollection,
        LogFactory $logModel,
        PopupFactory $popupModel,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_helperData = $helperData;
        $this->_popupTemplateFactory = $popupTemplateFactory;
        $this->_popupTemplateCollection = $popupTemplateCollection;
        $this->_logModel = $logModel;
        $this->_popupModel = $popupModel;
        $this->_popupCollection = $popupCollection;
        $this->_popupResource = $popupResource;
        $this->_storeManager = $storeManager;
        $this->_json = $json;
    }

    /**
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.1.0') < 0) {
            $popup_type = [
                [
                    'path' => 'hot_deal/popup_1',
                    'type' => '6',
                    'class' => 'popup-default-40',
                ],
                [
                    'path' => 'hot_deal/popup_2',
                    'type' => '6',
                    'class' => 'popup-default-41',
                ]
            ];
            $data = [];

            $count = 0;
            $templateCollection = $this->_popupTemplateCollection->create()->getData();
            foreach ($templateCollection as $template) {
                $count++;
            }

            if (!empty($popup_type)) {
                foreach ($popup_type as $type) {
                    $data[] = [
                        'template_name' => "Default Template " . $count,
                        'template_type' => $type['type'],
                        'html_content' => $this->_helperData->getTemplateDefault($type['path']),
                        'css_style' => '',
                        'class' => $type['class'],
                        'status' => 1
                    ];
                    $count++;
                }
                $popupTemplateModel = $this->_popupTemplateFactory->create();
                $popupTemplateModel->insertMultiple($data);
            }
            $logModel = $this->_logModel->create()->getCollection()->getData();
            foreach ($logModel as $log) {
                $string = $log['content'];
                if ($this->isJSON($string)) {
                    $content = $this->_json->unserialize($string ?? 'null');
                    if (is_array($content)) {
                        $result = '';
                        $count = 0;
                        foreach ($content as $raw) {
                            if ($count == 0) {
                                $count++;
                                continue;
                            }
                            if (isset($raw['name'])) {
                                $result .= $raw['name'] . ": " . $raw['value'] . "| ";
                            }
                        }
                        $string = $result != '' ? $result : $content;
                    }
                }
                $log_id = $log['log_id'];
                $popup_id = $log['popup_id'];
                $popup_name = $this->_popupModel->create()->load($popup_id)->getPopupName();
                $created_at = $log['created_at'];
                $log = $this->_logModel->create()->load($log_id);
                $log->setPopupId($popup_id);
                $log->setPopupName($popup_name);
                $log->setContent($string);
                $log->setCreatedAt($created_at);
                $log->save();
            }
        }
        if (version_compare($context->getVersion(), '1.2.0') < 0) {
            $popups = $this->_popupCollection->create();
            /** @var  \Magenest\Popup\Model\Popup $popup */
            foreach ($popups as $popup) {
                if ($popup->getData('floating_button_text_color') && $popup->getData('floating_button_text_color')[0] != '#') {
                    $popup->setData('floating_button_text_color', '#' . $popup->getData('floating_button_text_color'));
                }
                if ($popup->getData('floating_button_background_color') && $popup->getData('floating_button_background_color')[0] != '#') {
                    $popup->setData('floating_button_background_color', '#' . $popup->getData('floating_button_background_color'));
                }
                if (!$popup->getData('floating_button_hover_color')) {
                    $popup->setData('floating_button_hover_color', '#eaeaea');
                }
                if (!$popup->getData('floating_button_text_hover_color')) {
                    $popup->setData('floating_button_text_hover_color', '#0e3e81');
                }
                if (!$popup->setData('customer_group_ids')) {
                    $popup->setData('customer_group_ids', '32000');
                }
                $this->_popupResource->save($popup);
            }

            // Update new default template
            $popup_type_default = $this->_helperData->getPopupTemplateDefault();

            // set status for template default
            $templateCollection = $this->_popupTemplateFactory->create()->getCollection()->getData();
            foreach ($templateCollection as $template) {
                if ($template['status'] == 0) {
                    foreach ($popup_type_default as $type) {
                        if ($type['class'] === $template['class']
                            && $type['name'] === $template['template_name']
                            && $type['type'] === $template['template_type']
                            && $this->_helperData->getTemplateDefault($type['path']) === $template['html_content']
                            && $template['css_style'] === '') {
                            $this->_popupTemplateFactory->create()->load($template['template_id'])
                                                        ->setStatus(1)->save();
                            break;
                        }
                    }
                }
            }

            // set status for tempalate_edited
            $templateEdited = $this->_popupTemplateFactory->create()->getCollection()
                                                          ->addFieldToFilter('status', ['nin' => [1]])->getData();
            foreach ($templateEdited as $template) {
                $this->_popupTemplateFactory->create()->load($template['template_id'])
                                            ->setStatus(2)->save();
            }

            // set status for tempalate_default_deleted
            $data_template_default = [];
            $templateDefault = $this->_popupTemplateFactory->create()->getCollection()
                                                           ->addFieldToFilter('status', ['eq' => [1]])->getData();
            foreach ($templateDefault as $template) {
                $templateClass[] = $template['class'];
            }
            foreach ($popup_type_default as $type) {
                $check = in_array($type['class'], $templateClass);
                if (!$check) {
                    $data_template_default[] = [
                        'template_name' => $type['name'],
                        'template_type' => $type['type'],
                        'html_content' => $this->_helperData->getTemplateDefault($type['path']),
                        'css_style' => '',
                        'class' => $type['class'],
                        'status' => 1
                    ];
                }
            }
            if (!empty($data_template_default)) {
                $popupTemplateModel = $this->_popupTemplateFactory->create();
                $popupTemplateModel->insertMultiple($data_template_default);
            }

            // add class = 'magenest-popup-step' to html_content of template default
            $templateDefault = $this->_popupTemplateFactory->create()->getCollection()
                                                           ->addFieldToFilter('status', ['eq' => [1]])->getData();
            $type_array = [];
            $key = [];
            $value = [];
            foreach ($popup_type_default as $type) {
                $key[] =  $type['class'];
                $value[] = $type['path'];
            }
            $type_array = array_combine($key, $value);
            foreach ($templateDefault as $template) {
                if (in_array($template['class'], $key)) {
                    $html_content = $this->_helperData->getTemplateDefault($type_array[$template['class']]);
                    $this->_popupTemplateFactory->create()->load($template['template_id'])
                                                ->setHtmlContent($html_content)->save();
                }
            }
        }
    }

    /**
     * @param $string
     * @return bool
     */
    public function isJSON($string)
    {
        return is_string($string) && is_array($this->_json->unserialize($string ?? 'null')) ? true : false;
    }
}

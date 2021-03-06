<?php

namespace Magenest\Popup\Observer;

use Magento\Framework\Event\Observer;
use Magenest\Popup\Model\PopupFactory as PopupFactory;

/**
 * Class SetHtmlContentPopup
 * @package Magenest\Popup\Observer
 */
class SetHtmlContentPopup implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magenest\Popup\Model\PopupFactory
     */
    protected $_popupFactory;

    /**
     * SetHtmlContentPopup constructor.
     * @param PopupFactory $popupFactory
     */
    public function __construct(PopupFactory $popupFactory)
    {
        $this->_popupFactory = $popupFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(Observer $observer)
    {
        $template = $observer->getData('template');
        $template_id = $template->getTemplateId();
        $html_content = $template->getHtmlContent();
        $popupCollection = $this->_popupFactory->create()->getCollection()
            ->addFieldToFilter('popup_template_id', $template_id);
        if(!empty($popupCollection)) {
            foreach ($popupCollection as $popup) {
                $popup->setHtmlContent($html_content);
                $popup->save();
            }
        }
    }
}
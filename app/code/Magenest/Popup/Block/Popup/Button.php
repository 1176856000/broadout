<?php

namespace Magenest\Popup\Block\Popup;

/**
 * Class Button
 * @package Magenest\Popup\Block\Popup
 */
class Button extends \Magento\Framework\View\Element\Template
{
    /** @var  \Magenest\Popup\Model\PopupFactory $_popupFactory */
    protected $_popupFactory;
    /**
     * @var
     */
    protected $_dateTime;
    /**
     * @var \Magenest\Popup\Block\Popup\Display
     */
    protected $_display;

    /**
     * Button constructor.
     * @param \Magenest\Popup\Model\PopupFactory $popupFactory
     * @param \Magenest\Popup\Block\Popup\Display $display
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magenest\Popup\Model\PopupFactory $popupFactory,
        \Magenest\Popup\Block\Popup\Display $display,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    )
    {
        $this->_popupFactory = $popupFactory;
        $this->_display = $display;
        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function checkEnableButton()
    {
        $popup_id = $this->_display->getPopup()->getId();
        $popup = $this->_popupFactory->create()->load($popup_id);
        if (!empty($popup->getPopupStatus()) && !empty($popup->getEnableFloatingButton())) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return string
     */
    public function setButtonId(){
        $popup_id = $this->_display->getPopup()->getId();
        $displayPopup = $this->_popupFactory->create()
            ->load($popup_id)->getFloatingButtonDisplayPopup();
        if ($displayPopup == 0) {
            $button_id = 'floating-button-before';
        } else {
            $button_id = 'floating-button-after';
        }
        return $button_id;
    }

    /**
     * @return string
     */
    public function setDisplayButton(){
        $popup_id = $this->_display->getPopup()->getId();
        $displayPopup = $this->_popupFactory->create()
            ->load($popup_id)->getFloatingButtonDisplayPopup();
        if($displayPopup == 0) {
            $display = "display: none;";
        } else {
            $display = "display: block;";
        }
        return $display;
    }

    /**
     * @return mixed
     */
    public function getButtonContent()
    {
        $popup_id = $this->_display->getPopup()->getId();
        $content = $this->_popupFactory->create()
            ->load($popup_id)->getFloatingButtonContent();
        return $content;
    }

    /**
     * @return mixed
     */
    public function getTextButtonColor()
    {
        $popup_id = $this->_display->getPopup()->getId();
        $textColor = $this->_popupFactory->create()
            ->load($popup_id)->getFloatingButtonTextColor();
        return $textColor;
    }

    /**
     * @return mixed
     */
    public function getTextButtonHoverColor()
    {
        $popup_id = $this->_display->getPopup()->getId();
        $hoverColor = $this->_popupFactory->create()
                                         ->load($popup_id)->getFloatingButtonTextHoverColor();
        return $hoverColor;
    }

    /**
     * @return mixed
     */
    public function getBackgroundButtonColor()
    {
        $popup_id = $this->_display->getPopup()->getId();
        $backgroundColor = $this->_popupFactory->create()
            ->load($popup_id)->getFloatingButtonBackgroundColor();
        return $backgroundColor;

    }

    /**
     * @return mixed
     */
    public function getHoverButtonColor()
    {
        $popup_id = $this->_display->getPopup()->getId();
        $hoverColor = $this->_popupFactory->create()
                                               ->load($popup_id)->getFloatingButtonHoverColor();
        return $hoverColor;

    }

    /**
     * @return string
     */
    public function getPositionButton()
    {
        $popup_id = $this->_display->getPopup()->getId();
        $position = $this->_popupFactory->create()
            ->load($popup_id)->getFloatingButtonPosition();
        if ($position == 0) {
            $positionStyle = 'right: 50%; bottom: 0; transform: translate(0, -85%); max-width: 100vw; left: unset;';
        } else if ($position == 1) {
            $positionStyle = 'right: unset; bottom: 0; transform: translate(0, -85%); max-width: 100vw; left: 0;';
        } else {
            $positionStyle = 'right: 0; bottom: 0; transform: translate(0, -85%); max-width: 100vw; left: unset;';
        }
        return $positionStyle;
    }

    /**
     * @return string
     */
    public function styleButton()
    {
        $color = 'color: ' . $this->getTextButtonColor() . ';';
        $background = 'background-color: ' . $this->getBackgroundButtonColor() . ';';
        $position = $this->getPositionButton();
        $displayButton = $this->setDisplayButton();
        $style = $color . $background . $position . $displayButton. 'position: fixed; z-index: 9;';

        return $style;
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * @return false|string[]
     */
    public function getVisibleStore()
    {

        $visibleStore = $this->_display->getPopup()->getVisibleStores();
        $storeIds = str_replace(',', ' ', $visibleStore);
        return explode(' ', $storeIds);

    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function checkStoreButton()
    {
        $storeId = $this->getStoreId();
        $visibleStore = $this->getVisibleStore();
        $result = in_array("0", $visibleStore) || in_array($storeId, $visibleStore) ? true : false;
        return $result;
    }
}
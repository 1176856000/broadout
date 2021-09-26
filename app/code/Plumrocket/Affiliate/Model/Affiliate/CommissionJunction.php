<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model\Affiliate;

class CommissionJunction extends AbstractModel
{
    const TYPE_ID = 7;
    const SITE_ID_PARAM = 'PlumrocketAffiliateLinkshareSiteId';

    /**
     * Storage Name
     * @var string
     */
    const STORAGE_NAME = 'CJEVENT';

    /**
     * Get merchant type
     * @return string
     */
    public function getMerchantType()
    {
        return $this->getAdditionalDataValue('merchant_type');
    }

    /**
     * Get container tag id
     * @return string
     */
    public function getContainerTagId()
    {
        return $this->getAdditionalDataValue('container_tag_id');
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeHtml($_section, $_includeon = null)
    {
        return $this->_getRenderedCode($_section, $_includeon);
    }

    /**
     * {@inheritdoc}
     */
    protected function _getRenderedCode($_section, $_includeon = null)
    {
        switch (true) {
            case $_section == parent::SECTION_BODYBEGIN && in_array('checkout_success', $_includeon):
                $order = $this->getLastOrder();
                if ($order && $order->getId()) {

                    $itemsStr = '';
                    $i = 0;
                    foreach ($order->getAllVisibleItems() as $item) {
                        $i++;
                        $itemsStr .= '&ITEM' . $i . '=' . urlencode($item->getSku()) . '&AMT' . $i . '=' . round($item->getPrice(), 2) . '&QTY' . $i . '=' . ((int)($item->getQtyOrdered()));
                    }

                    $currency = $this->_storeManager->getStore()->getCurrentCurrencyCode();
                    $discount = ((int)($order->getDiscountAmount())) ? abs($order->getDiscountAmount()) : 0;
                    $url = 'https://www.emjcd.com/tags/c?'.
                        'containerTagId='.urlencode($this->getContainerTagId()).
                        $itemsStr.
                        '&CID='.urlencode($this->getMerchantId()).
                        '&OID='.urlencode($order->getIncrementId()).
                        '&TYPE='.urlencode($this->getMerchantType()).
                        '&CURRENCY='.urldecode($currency).
                        '&COUPON='.urldecode($order->getCouponCode()).
                        '&DISCOUNT='.urlencode($discount).
                        '&CJEVENT='.urlencode($this->_cookieManager->getCookie(self::STORAGE_NAME));

                    return '<div style="width:1px; height:1px; overflow:hidden; position: absolute;">
                            <iframe height="1" width="1" frameborder="0" scrolling="no" src="'.$url.'" name="cj_conversion" ></iframe>
                        </div>';
                }
                break;
            default:
                return null;
        }
    }
}

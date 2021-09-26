<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model\Affiliate;

class PerformanceHorizon extends AbstractModel
{
    /**
     * Get campaign id
     * @return int
     */
    public function getCampaignId()
    {
        return $this->getAdditionalDataValue('campaign_id');
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeHtml($_section, $_includeon = null)
    {
        $html = null;
        $scheme = $this->_request->getScheme();

        if ($_section == parent::SECTION_BODYEND) {
            $order = $this->getLastOrder();
            if ($order && $order->getId()) {

                $itemsStr = '';
                foreach ($order->getAllVisibleItems() as $item) {

                    // Get the first category assigned to the item.  This is retailer specific.
                    $categoryIds = $this->_productFactory->create()->load($item->getProductId())->getCategoryIds();

                    $firstCategoryName = '';
                    if (count($categoryIds)) {
                        $firstCategoryName = $this->_categoryFactory->create()->load($categoryIds[0])->getName();
                    }

                    // Get the item price, minus any discounts
                    $itemPrice = number_format($item->getPriceInclTax() - abs($item->getDiscountAmount()), 2, '.', '');

                    $itemParams = [
                        'category:'.    urlencode($firstCategoryName),
                        'sku:'.         urlencode($item->getSku()),
                        'value:'.       $itemPrice,
                        'quantity:'.    round($item->getQtyOrdered()),
                    ];

                    $itemsStr .= '['. implode('/', $itemParams) .']';
                }

                $params = [
                    'campaign'      => $this->getCampaignId(),
                    'conversionref' => $order->getIncrementId(),
                    'currency'      => $this->getCurrencyCode($order),
                ];

                $paramsStr = '';
                foreach ($params as $key => $value) {
                    $paramsStr .= ( '/'. $key . ':'. urlencode($value) );
                }

                $paramsStr .= '/'. $itemsStr;

                $html = '<div style="width:1px; height:1px; overflow:hidden; position: absolute;">
                                <img src="https://prf.hn/conversion'. $paramsStr .'" width="1" height="1" />
                            </div>';
            }
        }

        return $html;
    }
}

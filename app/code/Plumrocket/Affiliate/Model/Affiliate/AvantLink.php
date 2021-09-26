<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model\Affiliate;

class AvantLink extends AbstractModel
{
    /**
     * Get site id
     * @return string
     */
    public function getSiteId()
    {
        return $this->getAdditionalDataValue('site_id');
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeHtml($_section, $_includeon = null)
    {
        if ($_section == parent::SECTION_BODYEND) {
            $code = '';
            if (in_array('checkout_success', $_includeon)) {
                $order = $this->getLastOrder();
                if ($order && $order->getId()) {
                    $orderId = $order->getIncrementId();
                    $billing = $order->getBillingAddress();

                    $orderData = [
                        'order_id' => $order->getIncrementId(),
                        'amount' => $order->getGrandTotal() - $order->getShippingAmount() - $order->getTaxAmount(),
                        'state' => $this->_regionFactory->create()->load($billing->getRegionId())->getCode(),
                        'country' => $billing->getCountryId(),
                        'new_customer' => $this->isNewCustomer($order) ? 'Y' : 'N',
                    ];

                    /**
                     * Exclusive Coupon Codes (ECC) is an optional parameter,
                     * so we add it only if the user have used a coupon
                     */
                    if ($couponCode = $order->getCouponCode()) {
                        $orderData['ecc'] = urlencode($couponCode);
                    }

                    $code .= '<script type="text/javascript">
                        var _AvantMetrics = _AvantMetrics || [];
                        _AvantMetrics.push([\'order\','.json_encode($orderData).']);
                    ';

                    $childSku = [];
                    foreach ($order->getAllItems() as $item) {
                        if ($piID = $item->getParentItemId()) {
                            $childSku[$piID] = $item->getSku();
                        }
                    }

                    foreach ($order->getAllVisibleItems() as $item) {
                        $product = $this->_productFactory->create()->load($item->getProductId());

                        if ($item->getProductType() == 'bundle') {
                            $parentSku = $product->getSku();
                            foreach ($item->getChildrenItems() as $cItem) {
                                $itemData = [
                                    'order_id' => $orderId,
                                    'parent_sku' => $parentSku,
                                    'variant_sku' => $cItem->getSku(),
                                    'price' => $cItem->getPrice(),
                                    'qty' => round($cItem->getQtyOrdered()),
                                ];

                                $code .= '_AvantMetrics.push([\'item\','.json_encode($itemData).']);
                                ';
                            }
                        } else {
                            if (isset($childSku[$item->getID()])) {
                                $parentSku = $product->getSku();
                                $variantSku = $item->getSku();
                            } else {
                                $parentSku = $variantSku = $product->getSku();
                            }
                            $itemData = [
                                'order_id' => $orderId,
                                'parent_sku' => $parentSku,
                                'variant_sku' => $variantSku,
                                'price' => $item->getPrice(),
                                'qty' => round($item->getQtyOrdered()),
                            ];

                            $code .= '_AvantMetrics.push([\'item\','.json_encode($itemData).']);';
                        }
                    }

                    $code .= '</script>';
                }
            }

            $code .= '<script type="text/javascript">
                (function() {
                    var avm = document.createElement(\'script\'); avm.type = \'text/javascript\'; avm.async = true;
                    avm.src = (\'https:\' == document.location.protocol ? \'https://\' : \'http://\') + \'cdn.avmws.com/'.$this->getSiteId().'/\';
                    var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(avm, s);
                })();
            </script>';

            return $code;
        }
    }
}

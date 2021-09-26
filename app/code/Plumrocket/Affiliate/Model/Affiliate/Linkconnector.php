<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model\Affiliate;

class Linkconnector extends AbstractModel
{
    /**
     * {@inheritdoc}
     */
    public function getCodeHtml($_section, $_includeon = null)
    {
        $html = null;

        if ($_section == parent::SECTION_BODYEND) {
            $order = $this->getLastOrder();
            if ($order && $order->getId()) {
                $totalAmount = round($order->getSubtotal() - abs($order->getDiscountAmount()), 2);

                $params = [
                    'lc'            =>  '00000000' . $this->getMerchantId(),
                    'oid'           =>  $order->getIncrementId(),
                    'amt'           =>  $totalAmount,
                    'lc_coupon'     =>  $order->getCouponCode(),
                    'lc_cur'        =>  $order->getOrderCurrencyCode(),
                    'lc_discount'   =>  $order->getDiscountAmount(),
                    'lc_pitem'      =>  $this->getProductData($order, 'sku'),
                    'lc_pname'      =>  $this->getProductData($order, 'name'),
                    'lc_pqty'       =>  $this->getProductData($order, 'qty_ordered'),
                    'lc_pamt'       =>  $this->getProductData($order, 'price'),
                    'lc_ptype'      =>  $this->getProductData($order, 'product_type'),
                    'lc_scw'        =>  $order->getCreatedAt(),
                    'lctid'         =>  ''
                ];

                $html .= '<script language="javascript" src="https://www.linkconnector.com/tmjs.php?' . http_build_query($params) . '"></script>';
            }
        }

        return $html;
    }

    private function getProductData($order, $code = '')
    {
        $orderItems = $order->getAllVisibleItems();

        return implode('|', array_reduce(
            $orderItems,
            function ($result, $item) use ($code) {
                $result[] = $item->getData($code);
                return $result;
            }
        ));
    }
}

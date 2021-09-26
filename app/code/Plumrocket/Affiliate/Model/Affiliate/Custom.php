<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model\Affiliate;

use Magento\Sales\Model\Order;

class Custom extends AbstractModel
{
    /**
     * get code html
     * @param  string $_section
     * @param  string $_includeon
     * @return string
     */
    public function getCodeHtml($_section, $_includeon = null)
    {
        $getSectionCode = 'getSection'.ucfirst($_section).'Code';
        if ($code = $this->$getSectionCode()) {

            $variables = [];

            if (isset($_includeon['checkout_success'])) { //checkout success page
                $order = $this->getLastOrder();
                /**
                 * @var $order Order
                 */
                if ($order && $order->getId()) {
                    $variables['order_subtotal']        = round($order->getSubtotal(), 2);
                    $variables['order_id']              = $order->getIncrementId();
                    $variables['order_date']            = $order->getCreatedAt();
                    $variables['order_coupon_code']     = $order->getCouponCode();
                    $variables['order_discount_amount'] = round($order->getDiscountAmount(), 2);
                    $variables['order_tax_amount']      = round($order->getTaxAmount(), 2);
                    $variables['order_currency_code']   = $order->getOrderCurrencyCode();

                    $variables['customer_email']    = $order->getCustomerEmail();
                    $variables['customer_name']     = addslashes($order->getCustomerName());

                    $shipping = $order->getShippingAddress();
                    if ($shipping && $shipping->getCountryId()) {
                        $variables['delivery_country'] = $shipping->getCountryId();
                    }
                }
            }

            foreach ($variables as $key => $value) {
                $code = str_replace('{{' . $key . '}}', $value, $code);
            }

            return $code."\n\r";
        }
        return null;
    }
}

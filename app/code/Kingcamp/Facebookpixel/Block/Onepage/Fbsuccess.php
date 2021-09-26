<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Kingcamp\Facebookpixel\Block\Onepage;

#use Magento\Customer\Model\Context;
#use Magento\Sales\Model\Order;

/**
 * One page checkout success page
 *
 * @api
 * @since 100.0.2
 */
class Fbsuccess extends \Magento\Checkout\Block\Onepage\Success
{
      

    /**
     * Prepares block data
     *
     * @return void
     */
    protected function prepareBlockData()
    {
        $order = $this->_checkoutSession->getLastRealOrder();
		
        $this->addData(
            [
                'is_order_visible' => $this->isVisible($order),
                'view_order_url' => $this->getUrl(
                    'sales/order/view/',
                    ['order_id' => $order->getEntityId()]
                ),
                'print_url' => $this->getUrl(
                    'sales/order/print',
                    ['order_id' => $order->getEntityId()]
                ),
                'can_print_order' => $this->isVisible($order),
                'can_view_order'  => $this->canViewOrder($order),
                'order_id'  => $order->getIncrementId(),
				'order_subtotal'  => $order->getSubtotal(),
				'currency_code'  => $order->getGlobalCurrencyCode()
            ]
        );
    }

     
}

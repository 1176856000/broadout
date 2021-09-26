<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Affiliate\Model\Affiliate\Pixel;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order;

/**
 * Provides order on checkout success page
 *
 * @since 2.8.0
 */
class OrderProvider
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @param \Magento\Framework\Registry     $registry
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->registry = $registry;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Get last order
     *
     * @return \Magento\Sales\Model\Order
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getLastOrder(): Order
    {
        if (! $this->registry->registry('last_real_order')) {
            $order = $this->checkoutSession->getLastRealOrder();
            if (! $order->getId()) {
                throw new NoSuchEntityException(
                    __('Last order is not found. Verify if it should be on the page where you try to get it')
                );
            }
            $this->registry->register('last_real_order', $this->checkoutSession->getLastRealOrder());
        }
        return $this->registry->registry('last_real_order');
    }
}

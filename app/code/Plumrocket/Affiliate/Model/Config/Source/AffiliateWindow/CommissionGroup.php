<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model\Config\Source\AffiliateWindow;

/**
 * Integration status options.
 */
class CommissionGroup implements \Magento\Framework\Option\ArrayInterface
{
    const GROUP_CLIENT = 'client';
    const GROUP_PRODUCT = 'product';

    /**
     * Retrieve parameters options array.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            0 => ['value' => '', 'label' => __('None')],
            1 => ['value' => self::GROUP_CLIENT, 'label' => __('Client')],
            2 => ['value' => self::GROUP_PRODUCT, 'label' => __('Product')],
        ];
    }
}

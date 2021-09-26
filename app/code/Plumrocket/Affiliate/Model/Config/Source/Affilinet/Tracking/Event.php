<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model\Config\Source\Affilinet\Tracking;

/**
 * Integration status options.
 */
class Event implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Retrieve parameters options array.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            0 => [
                'value' => '0',
                'name' => '',
                'label' => __('Disabled Tracking'),
                'rate_number' => 0,
                'required' => [],
            ],
            1 => [
                'value' => '1',
                'name' => 'Sale',
                'label' => __('Sale'),
                'rate_number' => 1,
                'required' => [
                    'order_id',
                    'net_price',
                    'rate_number',
                ],
            ],
            2 => [
                'value' => '2',
                'name' => 'Lead',
                'label' => __('Lead'),
                'rate_number' => 1,
                'required' => [
                    'order_id',
                    'rate_number',
                ],
            ],
            3 => [
                'value' => '3',
                'name' => 'Basket',
                'label' => __('Basket'),
                'rate_number' => 1,
                'required' => [
                    'order_id',
                    'basket_items',
                ],
            ],
        ];
    }

    public function getDataByCode($code)
    {
        $options = $this->toOptionArray();

        return (array_key_exists($code, $options) && is_array($options))
            ? $options[$code]
            : false;
    }
}

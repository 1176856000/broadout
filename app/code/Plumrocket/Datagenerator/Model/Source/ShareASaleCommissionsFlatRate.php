<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Datagenerator
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Datagenerator\Model\Source;

class ShareASaleCommissionsFlatRate extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var array
     */
    protected $options;

    public function getAllOptions()
    {
        if ($this->options === null) {
            $this->options[] = [
                'label' => __('Inherited From Parent Category'),
                'value' => 0,
            ];

            $this->options[] = [
                'label' => __('Percentage'),
                'value' => 1,
            ];

            $this->options[] = [
                'label' => __('Flat amount'),
                'value' => 2,
            ];
        }
        return $this->options;
    }
}

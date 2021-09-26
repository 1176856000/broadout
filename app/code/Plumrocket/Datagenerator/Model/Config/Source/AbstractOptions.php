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
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Datagenerator\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

abstract class AbstractOptions implements OptionSourceInterface
{
    /**
     * @return array
     */
    abstract public function toArray();

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $result = [];

        foreach ($this->toArray() as $key => $protocol) {
            $result[] = [
                'value' => $key,
                'label' => $protocol,
            ];
        }

        return $result;
    }
}

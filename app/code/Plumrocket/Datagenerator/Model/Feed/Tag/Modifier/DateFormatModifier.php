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

namespace Plumrocket\Datagenerator\Model\Feed\Tag\Modifier;

use Plumrocket\Datagenerator\Model\Feed\Tag\ModifierInterface;
use Plumrocket\Datagenerator\Model\Feed\TagInterface;

/**
 * Format data to specific format
 *
 * Format: "|date_format:{date and time format}"
 * For Example: "|date_format:Y-m-d/H:i:s"
 *
 * @since 2.3.0
 */
class DateFormatModifier implements ModifierInterface
{
    /**
     * @inheritDoc
     */
    public function modify(TagInterface $tag, $propertyValue, string $paramsString = '', $entityObject = null)
    {
        $time = is_numeric($propertyValue) ? $propertyValue : strtotime($propertyValue);
        return date($paramsString, $time);
    }
}

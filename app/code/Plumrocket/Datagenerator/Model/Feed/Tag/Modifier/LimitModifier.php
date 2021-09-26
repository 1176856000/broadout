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
 * Cut array if it's too long
 *
 * Format: "|limit:{length}"
 * For example: "|limit:10"
 *
 * @since 2.3.0
 */
class LimitModifier implements ModifierInterface
{
    /**
     * @inheritDoc
     */
    public function modify(TagInterface $tag, $propertyValue, string $paramsString = '', $entityObject = null)
    {
        if (is_array($propertyValue)) {
            return array_slice($propertyValue, 0, $paramsString);
        }
        return $propertyValue;
    }
}

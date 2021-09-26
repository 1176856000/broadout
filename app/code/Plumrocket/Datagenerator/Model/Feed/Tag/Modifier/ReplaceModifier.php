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
 * Replace specified content to replacement, replacement can be empty string
 *
 * Format: "|replace:{search}:{replace}"
 * For example:
 *  "|replace: > : /"
 *  "|replace:,:"
 *
 * @since 2.3.0
 */
class ReplaceModifier implements ModifierInterface
{
    /**
     * @inheritDoc
     */
    public function modify(TagInterface $tag, $propertyValue, string $paramsString = '', $entityObject = null)
    {
        $replace = explode(':', $paramsString, 2);
        if (! empty($replace[0])) {
            $propertyValue = str_replace($replace[0], ! empty($replace[1]) ? $replace[1] : '', $propertyValue);
        }

        return $propertyValue;
    }
}

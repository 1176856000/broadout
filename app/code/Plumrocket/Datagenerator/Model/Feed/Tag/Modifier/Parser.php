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

/**
 * Parse modifiers like size:150
 *
 * Modifiers received from tags {product.image|size:150}
 * Allowed format is modifierName:param[:param]
 *
 * @since 2.3.0
 */
class Parser
{
    /**
     * @param array $parts
     * @return array
     */
    public function parse(array $parts): array
    {
        $modifiers = [];
        foreach ($parts as $modifierString) {
            if (false !== strpos($modifierString, ':')) {
                list($modifierName, $modifierParams) = explode(':', $modifierString, 2);
                $modifierName = trim($modifierName);
                if (! $modifierName) {
                    continue;
                }

                $modifiers[$modifierName] = trim($modifierParams);
            }
        }
        return $modifiers;
    }
}

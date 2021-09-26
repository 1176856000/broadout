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

namespace Plumrocket\Datagenerator\Model\Feed\Tag;

use Plumrocket\Datagenerator\Model\Feed\TagInterface;

/**
 * Replaces tag with property value in feed template
 *
 * @since 2.3.0
 */
interface RendererInterface
{
    /**
     * @param \Plumrocket\Datagenerator\Model\Feed\TagInterface $tag
     * @param mixed                                             $propertyValue
     * @param string                                            $feedTemplatePart
     * @param string                                            $extension
     * @return string
     */
    public function render(TagInterface $tag, $propertyValue, string $feedTemplatePart, string $extension): string;
}

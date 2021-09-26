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

namespace Plumrocket\Datagenerator\Model\Feed;

/**
 * We call '{site.name}' or '{product.name|truncate:500}' a tag
 *
 * This class contains methods for work with them
 *
 * @since 2.3.0
 */
interface TagInterface
{
    const CODE = 'code';
    const ENTITY_TYPE = 'entity_type';
    const PROPERTY_NAME = 'property_name';
    const MODIFIERS = 'modifiers';
    const IS_PAIRED = 'is_paired';

    /**
     * Get tag without braces
     *
     * @return string
     */
    public function getCode(): string;

    /**
     * @return string
     */
    public function getEntityType(): string;

    /**
     * @return string
     */
    public function getPropertyName(): string;

    /**
     * Retrieve array of modifiers
     *
     * @return string[] format: ['modifierName' => 'modifierParamsString', ...]
     */
    public function getModifiers(): array;

    /**
     * @param string $name
     * @param string $paramsString
     * @return \Plumrocket\Datagenerator\Model\Feed\TagInterface
     */
    public function setModifier(string $name, string $paramsString): TagInterface;

    /**
     * Retrieve whether tag is singular {e.prop} or paired {e.prop}{/e.prop}
     *
     * @return bool
     */
    public function isPaired(): bool;

    /**
     * Retrieve whether tag is singular {e.prop} or paired {e.prop}{/e.prop}
     *
     * @return bool
     */
    public function isSingular(): bool;
}

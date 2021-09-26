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

namespace Plumrocket\Datagenerator\Model\Feed;

/**
 * @since 2.3.0
 */
class Tag implements TagInterface
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $entityType;

    /**
     * @var string
     */
    private $field;

    /**
     * @var array
     */
    private $modifiers;

    /**
     * @var bool
     */
    private $is_paired;

    /**
     * @param string $code
     * @param string $entity_type
     * @param string $property_name
     * @param array  $modifiers
     * @param bool   $is_paired
     */
    public function __construct(
        string $code,
        string $entity_type,
        string $property_name,
        array $modifiers,
        bool $is_paired
    ) {
        $this->code = $code;
        $this->entityType = $entity_type;
        $this->field = $property_name;
        $this->modifiers = $modifiers;
        $this->is_paired = $is_paired;
    }

    /**
     * @inheritDoc
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @inheritDoc
     */
    public function getEntityType(): string
    {
        return $this->entityType;
    }

    /**
     * @inheritDoc
     */
    public function getPropertyName(): string
    {
        return $this->field;
    }

    /**
     * @inheritDoc
     */
    public function getModifiers(): array
    {
        return $this->modifiers;
    }

    /**
     * @inheritDoc
     */
    public function setModifier(string $name, string $paramsString): TagInterface
    {
        $this->modifiers[$name] = $paramsString;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isPaired(): bool
    {
        return $this->is_paired;
    }

    /**
     * @inheritDoc
     */
    public function isSingular(): bool
    {
        return ! $this->isPaired();
    }
}

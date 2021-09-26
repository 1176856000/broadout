<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Api\Data;

/**
 * @since 3.0.0
 */
interface AffiliateProgramTypeInterface
{
    public const ID = 'id';
    public const NAME = 'name';
    public const KEY = 'key';
    public const META_KEYWORDS = 'meta_keywords';
    public const SORT_ORDER = 'order';
    public const IS_ALIVE = 'is_alive';
    public const MODEL = 'model_class_name';

    /**
     * Retrieve type id
     *
     * @return int
     */
    public function getId(): int;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return bool
     */
    public function isAlive(): bool;

    /**
     * @return string
     */
    public function getKey(): string;

    /**
     * @return array
     */
    public function getMetaKeywords(): array;

    /**
     * @return int
     */
    public function getSortOrder(): int;

    /**
     * @return string
     */
    public function getLogo(): string;

    /**
     * @return string
     */
    public function getModelClassName(): string;
}

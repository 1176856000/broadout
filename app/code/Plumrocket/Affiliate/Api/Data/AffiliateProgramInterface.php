<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Api\Data;

/**
 * @since 2.8.0
 */
interface AffiliateProgramInterface
{
    /**
     * Retrieve type id
     *
     * @return int
     */
    public function getTypeId(): int;

    /**
     * Generate pixels by page section and pixel types
     *
     * @param string $pageSection e.g. head, after body begin, before body end
     * @param array  $pageTypes old name was "include on"
     * @return string
     */
    public function getSectionPixelHtml(string $pageSection, array $pageTypes): string;

    /**
     * Get affiliate program config
     *
     * @param string $key
     * @param array|string|int|null $default
     * @return array|string|int|null
     */
    public function getConfig(string $key, $default = '');
}

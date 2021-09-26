<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Api;

use Plumrocket\Affiliate\Api\Data\PageTypeInterface;

/**
 * @since 3.0.0
 */
interface PageTypeProviderInterface
{
    /**
     * @param string $key
     * @return \Plumrocket\Affiliate\Api\Data\PageTypeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(string $key): PageTypeInterface;

    /**
     * @param int $pageTypeId
     * @return \Plumrocket\Affiliate\Api\Data\PageTypeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $pageTypeId): PageTypeInterface;

    /**
     * @return \Plumrocket\Affiliate\Api\Data\PageTypeInterface[]
     */
    public function getList(): array;
}

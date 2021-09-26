<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Api;

use Plumrocket\Affiliate\Api\Data\AffiliateProgramTypeInterface;

/**
 * @since 3.0.0
 */
interface AffiliateProgramTypeProviderInterface
{
    /**
     * @param int $typeId
     * @return \Plumrocket\Affiliate\Api\Data\AffiliateProgramTypeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $typeId): AffiliateProgramTypeInterface;

    /**
     * @return \Plumrocket\Affiliate\Api\Data\AffiliateProgramTypeInterface[]
     */
    public function getAliveTypes(): array;

    /**
     * @return \Plumrocket\Affiliate\Api\Data\AffiliateProgramTypeInterface[]
     */
    public function getList(): array;
}

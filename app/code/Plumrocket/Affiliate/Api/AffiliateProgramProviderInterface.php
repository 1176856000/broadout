<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Api;

/**
 * @since 2.8.0
 */
interface AffiliateProgramProviderInterface
{
    /**
     * Retrieve list of enabled affiliate programs
     *
     * @return \Plumrocket\Affiliate\Api\Data\AffiliateProgramInterface[]|\Plumrocket\Affiliate\Model\Affiliate[]
     */
    public function get(): array;
}

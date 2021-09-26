<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model\Config\Source;

use Plumrocket\Affiliate\Api\AffiliateProgramTypeProviderInterface;

/**
 * Integration status options.
 */
class Network implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Plumrocket\Affiliate\Api\AffiliateProgramTypeProviderInterface
     */
    private $affiliateProgramTypeProvider;

    /**
     * @param \Plumrocket\Affiliate\Api\AffiliateProgramTypeProviderInterface $affiliateProgramTypeProvider
     */
    public function __construct(AffiliateProgramTypeProviderInterface $affiliateProgramTypeProvider)
    {
        $this->affiliateProgramTypeProvider = $affiliateProgramTypeProvider;
    }

    /**
     * Retrieve status options array.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $result = [0 => ['value' => '', 'label' => '&nbsp;']];
        foreach ($this->affiliateProgramTypeProvider->getList() as $type) {
            $result[] = ['value' => $type->getId(), 'label' => $type->getName()];
        }
        return $result;
    }
}

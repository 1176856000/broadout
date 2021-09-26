<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model;

use Plumrocket\Affiliate\Api\AffiliateProgramProviderInterface;
use Plumrocket\Affiliate\Model\ResourceModel\Affiliate\CollectionFactory;

/**
 * @since 2.8.0
 */
class AffiliateProvider implements AffiliateProgramProviderInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Plumrocket\Affiliate\Model\ResourceModel\Affiliate\Collection
     */
    private $collectionFactory;

    /**
     * @var \Plumrocket\Affiliate\Api\Data\AffiliateProgramInterface[]
     */
    private $affiliates;

    /**
     * @param \Plumrocket\Affiliate\Model\ResourceModel\Affiliate\CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return \Plumrocket\Affiliate\Api\Data\AffiliateProgramInterface[]
     */
    public function get(): array
    {
        if (null === $this->affiliates) {
            $collection = $this->collectionFactory
                ->create()
                ->addEnabledStatusToFilter()
                ->addStoreToFilter();

            $this->affiliates = [];
            /** @var \Plumrocket\Affiliate\Model\Affiliate $item */
            foreach ($collection as $item) {
                $this->affiliates[] = $item->getTypedModel();
            }
        }

        return $this->affiliates;
    }
}

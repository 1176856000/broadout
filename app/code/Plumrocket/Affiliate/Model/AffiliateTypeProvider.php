<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Affiliate\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Plumrocket\Affiliate\Api\AffiliateProgramTypeProviderInterface;
use Plumrocket\Affiliate\Api\Data\AffiliateProgramTypeInterface as Type;
use Plumrocket\Affiliate\Api\Data\AffiliateProgramTypeInterfaceFactory;
use Plumrocket\Affiliate\Model\Affiliate\CommissionJunction;
use Plumrocket\Affiliate\Model\Affiliate\Everflow;
use Plumrocket\Affiliate\Model\Affiliate\Hasoffers;
use Plumrocket\Affiliate\Model\Affiliate\Pepperjam;
use Plumrocket\Affiliate\Model\Affiliate\Tradedoubler;

/**
 * @since 3.0.0
 */
class AffiliateTypeProvider implements AffiliateProgramTypeProviderInterface
{
    /**
     * @var Type[]
     */
    private $types;

    /**
     * @var \Plumrocket\Affiliate\Api\Data\AffiliateProgramTypeInterfaceFactory
     */
    private $affiliateProgramTypeFactory;

    /**
     * @param \Plumrocket\Affiliate\Api\Data\AffiliateProgramTypeInterfaceFactory $affiliateProgramTypeFactory
     */
    public function __construct(AffiliateProgramTypeInterfaceFactory $affiliateProgramTypeFactory)
    {
        $this->affiliateProgramTypeFactory = $affiliateProgramTypeFactory;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $typeId): Type
    {
        if (! isset($this->getList()[$typeId])) {
            throw NoSuchEntityException::singleField('id', $typeId);
        }
        return $this->getList()[$typeId];
    }

    /**
     * @inheritDoc
     */
    public function getAliveTypes(): array
    {
        $aliveTypes = array_filter($this->getList(), static function (Type $type) {
            return $type->isAlive();
        });

        usort($aliveTypes, static function (Type $type1, Type $type2) {
            return $type1->getSortOrder() <=> $type2->getSortOrder();
        });

        return $aliveTypes;
    }

    /**
     * @inheritDoc
     */
    public function getList(): array
    {
        if (null === $this->types) {
            $this->types = [];
            foreach ($this->getConfig() as $typeId => $typeData) {
                $this->types[$typeId] = $this->createTypeInstance($typeId, $typeData);
            }
        }
        return $this->types;
    }

    /**
     * Retrieve list of supported affiliate programs and their configuration
     *
     * @return array[]
     */
    public function getConfig(): array
    {
        return [
            1 => [
                Type::KEY        => 'custom',
                Type::NAME       => 'Custom',
                Type::SORT_ORDER => 500,
            ],
            2 => [
                Type::KEY        => 'avantLink',
                Type::NAME       => 'AvantLink',
                Type::SORT_ORDER => 50,
            ],
            Hasoffers::TYPE_ID => [
                Type::KEY           => 'hasoffers',
                Type::NAME          => 'Tune',
                Type::SORT_ORDER    => 40,
                Type::META_KEYWORDS => ['tune','hasoffers'],
            ],
            4 => [
                Type::KEY        => 'googleEcommerceTracking',
                Type::NAME       => 'Google Analytics Ecommerce Tracking',
                Type::SORT_ORDER => 80,
            ],
            5 => [
                Type::KEY        => 'shareasale',
                Type::NAME       => 'ShareASale',
                Type::SORT_ORDER => 20,
            ],
            6 => [
                Type::KEY           => 'linkshare',
                Type::NAME          => 'Rakuten',
                Type::SORT_ORDER    => 10,
                Type::META_KEYWORDS => ['rakuten', 'linkshare'],
            ],
            CommissionJunction::TYPE_ID => [
                Type::KEY           => 'commissionJunction',
                Type::NAME          => 'Commission Junction',
                Type::SORT_ORDER    => 30,
                Type::META_KEYWORDS => ['commission junction', 'affiliate by conversant'],
            ],
            9 => [
                Type::KEY        => 'shopzilla',
                Type::NAME       => 'Shopzilla',
                Type::SORT_ORDER => 60,
            ],
            11 => [
                Type::KEY           => 'affiliateWindow',
                Type::NAME          => 'AWIN',
                Type::SORT_ORDER    => 90,
                Type::META_KEYWORDS => ['awin', 'affiliate window', 'zanox'],
            ],
            Tradedoubler::TYPE_ID => [
                Type::KEY        => 'tradedoubler',
                Type::NAME       => 'Tradedoubler',
                Type::SORT_ORDER => 110,
            ],
            13 => [
                Type::KEY        => 'linkconnector',
                Type::NAME       => 'Linkconnector',
                Type::SORT_ORDER => 160,
            ],
            15 => [
                Type::KEY        => 'webGains',
                Type::NAME       => 'WebGains',
                Type::SORT_ORDER => 130,
            ],
            16 => [
                Type::KEY           => 'performanceHorizon',
                Type::NAME          => 'Partnerize',
                Type::SORT_ORDER    => 140,
                Type::META_KEYWORDS => ['partnerize', 'performance horizon'],
            ],
            17 => [
                Type::KEY           => 'impactRadius',
                Type::NAME          => 'Impact',
                Type::SORT_ORDER    => 150,
                Type::META_KEYWORDS => ['impact radius', 'impact'],
            ],
            18 => [
                Type::KEY        => 'sidecar',
                Type::NAME       => 'Sidecar',
                Type::SORT_ORDER => 160,
            ],
            19 => [
                Type::KEY        => 'flexoffers',
                Type::NAME       => 'FlexOffers',
                Type::SORT_ORDER => 170,
            ],
            Pepperjam::TYPE_ID => [
                Type::KEY        => 'pepperjam',
                Type::NAME       => 'Pepperjam',
                Type::SORT_ORDER => 180,
            ],
            21 => [
                Type::KEY        => 'criteo',
                Type::NAME       => 'Criteo',
                Type::SORT_ORDER => 190,
            ],
            24 => [
                Type::KEY        => 'affiliateFuture',
                Type::NAME       => 'Affiliate Future',
                Type::SORT_ORDER => 220,
            ],
            25 => [
                Type::KEY        => 'powerReviews',
                Type::NAME       => 'PowerReviews',
                Type::SORT_ORDER => 230,
            ],
            26 => [
                Type::KEY        => 'everflow',
                Type::NAME       => 'Everflow',
                Type::SORT_ORDER => 240,
                Type::MODEL      => Everflow::class,
            ],

            // Not alive
            8 => [
                Type::KEY        => 'chango',
                Type::NAME       => 'Chango',
                Type::SORT_ORDER => 70,
                Type::IS_ALIVE   => false,
            ],
            10 => [
                Type::KEY        => 'ebayEnterprise',
                Type::NAME       => 'eBay Enterprise',
                Type::SORT_ORDER => 100,
                Type::IS_ALIVE   => false,
            ],
            14 => [
                Type::KEY        => 'zanox',
                Type::NAME       => 'Zanox',
                Type::SORT_ORDER => 120,
                Type::IS_ALIVE   => false,
            ],
            22 => [
                Type::KEY        => 'roiTracker',
                Type::NAME       => 'eBay Commerce Network',
                Type::SORT_ORDER => 200,
                Type::IS_ALIVE   => false,
            ],
            23 => [
                Type::KEY        => 'affilinet',
                Type::NAME       => 'Affilinet',
                Type::SORT_ORDER => 210,
                Type::IS_ALIVE   => false,
            ],
        ];
    }

    /**
     * @param int   $typeId
     * @param array $typeData
     * @return \Plumrocket\Affiliate\Api\Data\AffiliateProgramTypeInterface
     */
    private function createTypeInstance(int $typeId, array $typeData): Type
    {
        $typeData[Type::ID] = $typeId;
        return $this->affiliateProgramTypeFactory->create(['data' => $typeData]);
    }
}

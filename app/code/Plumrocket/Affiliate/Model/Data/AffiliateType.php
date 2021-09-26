<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Affiliate\Model\Data;

use Magento\Framework\DataObject;
use Plumrocket\Affiliate\Api\Data\AffiliateProgramTypeInterface;

/**
 * @since 3.0.0
 */
class AffiliateType extends DataObject implements AffiliateProgramTypeInterface
{
    /**
     * @inheritDoc
     */
    public function getId(): int
    {
        return $this->_getData(self::ID);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->_getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function isAlive(): bool
    {
        return $this->_getData(self::IS_ALIVE) ?? true;
    }

    /**
     * @inheritDoc
     */
    public function getKey(): string
    {
        return $this->_getData(self::KEY);
    }

    /**
     * @inheritDoc
     */
    public function getMetaKeywords(): array
    {
        return $this->_getData(self::META_KEYWORDS) ?? [];
    }

    /**
     * @inheritDoc
     */
    public function getSortOrder(): int
    {
        return $this->_getData(self::SORT_ORDER) ?? 0;
    }

    /**
     * @inheritDoc
     */
    public function getLogo(): string
    {
        switch ($this->getKey()) {
            case 'tradedoubler': //Fix for chrome extension "Ad Block", change file name resolved problem
                return 'Plumrocket_Affiliate::images/tradedoublerlogo.png';
            case 'custom':
                return '';
            default:
                return 'Plumrocket_Affiliate::images/' . strtolower($this->getKey()) . '.png';
        }
    }

    /**
     * @inheritDoc
     */
    public function getModelClassName(): string
    {
        return $this->_getData(self::MODEL) ?? 'Plumrocket\Affiliate\Model\Affiliate\\' . ucfirst($this->getKey());
    }
}

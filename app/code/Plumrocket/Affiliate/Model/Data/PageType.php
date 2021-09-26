<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model\Data;

use Magento\Framework\DataObject;
use Plumrocket\Affiliate\Api\Data\PageTypeInterface;

/**
 * @since 3.0.0
 */
class PageType extends DataObject implements PageTypeInterface
{
    /**
     * @return int
     */
    public function getId(): int
    {
        return (int) $this->_getData('id');
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return (string) $this->_getData('key');
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return (string) $this->_getData('name');
    }
}

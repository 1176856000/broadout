<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Datagenerator
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Datagenerator\Model\Template;

use Plumrocket\Datagenerator\Model\ResourceModel\Template\CollectionFactory;

class Information
{
    /**
     * @var CollectionFactory
     */
    private $templateCollectionFactory;

    /**
     * @var int|null
     */
    private $googleShoppingId;

    /**
     * Information constructor.
     *
     * @param CollectionFactory $templateCollectionFactory
     */
    public function __construct(CollectionFactory $templateCollectionFactory)
    {
        $this->templateCollectionFactory = $templateCollectionFactory;
    }

    /**
     * @return int
     */
    public function getGoogleShoppingTemplateId(): int
    {
        if (null === $this->googleShoppingId) {
            $this->googleShoppingId = (int) $this->templateCollectionFactory->create()
                ->addFieldToFilter('template_id', 0)
                ->addFieldToFilter('type_entity', 0)
                ->addFieldToFilter('name', 'Google Shopping Feed')
                ->setPageSize(1)->getFirstItem()->getId();
        }

        return $this->googleShoppingId;
    }
}

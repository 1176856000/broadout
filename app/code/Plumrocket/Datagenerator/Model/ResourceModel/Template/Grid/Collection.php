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
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Datagenerator\Model\ResourceModel\Template\Grid;

use Plumrocket\Datagenerator\Model\ResourceModel\Template\Collection as TemplateCollection;

class Collection extends TemplateCollection
{

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        $this->addFieldToFilter('type_entity', \Plumrocket\Datagenerator\Model\Template::ENTITY_TYPE_FEED);
        return parent::_initSelect();
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        foreach ($this->getItems() as $item) {
            if ($item->getStoreId() && $item->getStoreId() != '0') {
                $item->setStoreId(explode(',', $item->getStoreId()));
            } else {
                $item->setStoreId(['0']);
            }
        }
    }
}

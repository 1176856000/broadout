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

namespace Plumrocket\Datagenerator\Model\ResourceModel;

class Template extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @inheritDoc
     */
    protected $_serializableFields = [
        'scheduled_days' => [null, null],
        'scheduled_time' => [null, null],
    ];

    /**
     * Initialize resource model
     * Get tablename from config
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('plumrocket_datagenerator_templates', 'id');
    }

    /**
     * @inheritDoc
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $password = $object->getData('password');

        if ($password && preg_match('/^\*+$/', $password)) {
            $object->setData('password', $object->getOrigData('password'));
        }

        return parent::_beforeSave($object);
    }
}

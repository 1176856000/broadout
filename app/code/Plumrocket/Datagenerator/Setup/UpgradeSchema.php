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

namespace Plumrocket\Datagenerator\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $connection = $setup->getConnection();

        $tableName = $setup->getTable('plumrocket_datagenerator_templates');

        if (version_compare($context->getVersion(), '2.1.0', '<')) {
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $connection->addColumn(
                    $tableName,
                    'conditions_serialized',
                    [
                        'type'      => Table::TYPE_TEXT,
                        'length'    => '2M',
                        'nullable'  => false,
                        'comment'   => 'Conditions Serialized'
                    ]
                );
            }
        }

        if (version_compare($context->getVersion(), '2.2.0', '<')) {
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $connection->addColumn(
                    $tableName,
                    'country',
                    [
                        'type'      => Table::TYPE_TEXT,
                        'length'    => 255,
                        'nullable'  => true,
                        'comment'   => 'Country'
                    ]
                );

                $connection->addColumn(
                    $tableName,
                    'language',
                    [
                        'type'      => Table::TYPE_TEXT,
                        'length'    => 255,
                        'nullable'  => true,
                        'comment'   => 'Language'
                    ]
                );

                $connection->addColumn(
                    $tableName,
                    'show_category_tab',
                    [
                        'type'      => Table::TYPE_SMALLINT,
                        'length'    => 2,
                        'default'   => 0,
                        'nullable'  => false,
                        'comment'   => 'Show Category Mapping Tab'
                    ]
                );

                $connection->addColumn(
                    $tableName,
                    'ftp_enabled',
                    [
                        'type'      => Table::TYPE_SMALLINT,
                        'length'    => 2,
                        'nullable'  => true,
                        'comment'   => 'FTP Enabled'
                    ]
                );

                $connection->addColumn(
                    $tableName,
                    'protocol',
                    [
                        'type'      => Table::TYPE_SMALLINT,
                        'length'    => 2,
                        'nullable'  => true,
                        'comment'   => 'Protocol'
                    ]
                );

                $connection->addColumn(
                    $tableName,
                    'host',
                    [
                        'type'      => Table::TYPE_TEXT,
                        'length'    => 255,
                        'nullable'  => true,
                        'comment'   => 'Host'
                    ]
                );

                $connection->addColumn(
                    $tableName,
                    'port',
                    [
                        'type'      => Table::TYPE_SMALLINT,
                        'length'    => 10,
                        'nullable'  => true,
                        'comment'   => 'Port'
                    ]
                );

                $connection->addColumn(
                    $tableName,
                    'username',
                    [
                        'type'      => Table::TYPE_TEXT,
                        'length'    => 255,
                        'nullable'  => true,
                        'comment'   => 'Username'
                    ]
                );

                $connection->addColumn(
                    $tableName,
                    'password',
                    [
                        'type'      => Table::TYPE_TEXT,
                        'length'    => false,
                        'nullable'  => true,
                        'comment'   => 'Password'
                    ]
                );

                $connection->addColumn(
                    $tableName,
                    'passive',
                    [
                        'type'      => Table::TYPE_SMALLINT,
                        'length'    => 2,
                        'default'   => 0,
                        'nullable'  => true,
                        'comment'   => 'Passive Mode'
                    ]
                );

                $connection->addColumn(
                    $tableName,
                    'path',
                    [
                        'type'      => Table::TYPE_TEXT,
                        'length'    => false,
                        'nullable'  => true,
                        'comment'   => 'Path'
                    ]
                );

                $connection->addColumn(
                    $tableName,
                    'scheduled_time',
                    [
                        'type'      => Table::TYPE_TEXT,
                        'length'    => false,
                        'nullable'  => true,
                        'comment'   => 'Scheduled Time'
                    ]
                );

                $connection->addColumn(
                    $tableName,
                    'scheduled_days',
                    [
                        'type'      => Table::TYPE_TEXT,
                        'length'    => 255,
                        'nullable'  => true,
                        'comment'   => 'Scheduled Days'
                    ]
                );
            }
        }

        $setup->endSetup();
    }
}

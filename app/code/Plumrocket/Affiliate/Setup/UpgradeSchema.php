<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup->startSetup();

        if (version_compare($context->getVersion(), '2.2.0', '<')) {
            /**
             * Create table `affiliate_order_info`
             */
            $table = $installer->getConnection()
                ->newTable($installer->getTable('affiliate_order_info'))
                ->addColumn('id', Table::TYPE_INTEGER, null, [
                    'identity'  => true,
                    'unsigned'  => true,
                    'nullable'  => false,
                    'primary'   => true,
                    ], 'Id')
                ->addColumn('order_id', Table::TYPE_INTEGER, null, [
                    'unsigned'  => true,
                    'nullable'  => false,
                    ], 'Order Id')
                ->addColumn('is_transaction', Table::TYPE_BOOLEAN, null, [
                    'nullable'  => false,
                    ], 'Is Transaction')
                ->addColumn('affiliate_id', Table::TYPE_INTEGER, null, [
                    'unsigned'  => true,
                    'nullable'  => false,
                    ], 'Affiliate Id')
                ->addColumn('comment', Table::TYPE_TEXT, null, [
                    'nullable'  => false,
                    ], 'Comment')
                ->setComment('Affiliate Order Info');
            $installer->getConnection()->createTable($table);
        }

        $setup->endSetup();
    }
}

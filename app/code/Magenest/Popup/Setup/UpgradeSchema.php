<?php

namespace Magenest\Popup\Setup;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class UpgradeSchema
 * @package Magenest\Popup\Setup
 */
class UpgradeSchema implements \Magento\Framework\Setup\UpgradeSchemaInterface
{
    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $_io;
    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $_directoryList;

    /**
     * UpgradeSchema constructor.
     * @param \Magento\Framework\Filesystem\Io\File $io
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     */
    public function __construct(
        File $io,
        DirectoryList $directoryList
    ) {
        $this->_io = $io;
        $this->_directoryList = $directoryList;
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.2.0') < 0) {
            $this->_io->mkdir($this->_directoryList->getPath('media') . '/magenest/popup', 0777);
            // Get module table
            $magenest_popup = $setup->getTable('magenest_popup');
            if ($setup->getConnection()->isTableExists($magenest_popup) == true) {
                $columns = [
                    'popup_positioninpage' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => true,
                        'comment' => 'Popup Position in page',
                    ],
                    'popup_link' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Add Link'
                    ],
                    'enable_floating_button' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => true,
                        'comment' => 'Enable Floating Button',
                    ],
                    'floating_button_content' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Floating Button Content'
                    ],
                    'floating_button_position' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => true,
                        'comment' => 'Floating Button Position',
                    ],
                    'floating_button_text_color' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Floating Button Text Color'
                    ],
                    'floating_button_text_hover_color' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Floating Button Text Hover Color'
                    ],
                    'floating_button_hover_color' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Floating Button Hover Color'
                    ],
                    'floating_button_background_color' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Floating Button Background Color'
                    ],
                    'floating_button_display_popup' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => true,
                        'comment' => 'Floating Button Display Popup',
                    ],
                    'enable_mailchimp' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => true,
                        'comment' => 'Enable Mail'
                    ],
                    'api_key' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => false,
                        'comment' => 'Api Key',
                    ],
                    'audience_id' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => false,
                        'comment' => 'Audience Id',
                    ],
                    'widget_instance' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => false,
                        'comment' => 'Widget Instance',
                    ],
                    'customer_group_ids' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => false,
                        'comment' => 'Customer Groups',
                    ],
                    'background_image' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Background Image'
                    ],
                ];

                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($magenest_popup, $name, $definition);
                }
            }

            $talbe = $setup->getConnection()->newTable(
                $setup->getTable('magenest_popup_layout')
            )->addColumn(
                'popup_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [],
                'popup id'
            )->addColumn(
                'layout_update_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [],
                'Layout update id '
            );

            $setup->getConnection()->createTable($talbe);

            $magenest_log = $setup->getTable('magenest_log');
            if ($setup->getConnection()->isTableExists($magenest_log) == true) {
                $columns = [
                    'popup_name' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => false,
                        'comment' => 'Popup Name',
                    ]
                ];
                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($magenest_log, $name, $definition);
                }
            }
            $magenest_templates = $setup->getTable('magenest_popup_templates');
            if ($setup->getConnection()->isTableExists($magenest_templates) == true) {
                $columns = [
                    'status' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => false,
                        'comment' => 'Status',
                    ]
                ];
                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($magenest_templates, $name, $definition);
                }
            }
            $setup->endSetup();
        }
    }
}

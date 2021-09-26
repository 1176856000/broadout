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

namespace Plumrocket\Datagenerator\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

/**
 * phpcs:ignoreFile
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * Temaplate factory
     * @var \Plumrocket\Datagenerator\Model\TemplateFactory
     */
    private $_feedTemplateFactory;

    /**
     * AttributeFactory
     * @var \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory
     */
    private $attributeFactory;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * UpgradeData constructor.
     *
     * @param \Plumrocket\Datagenerator\Model\TemplateFactory $feedTemplateFactory
     * @param \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attributeFactory
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory $attributeGroupCollectionFactory
     */
    public function __construct(
        \Plumrocket\Datagenerator\Model\TemplateFactory $feedTemplateFactory,
        \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attributeFactory,
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
    ) {
        $this->_feedTemplateFactory = $feedTemplateFactory;
        $this->attributeFactory = $attributeFactory;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            // Update ShareASale (shareasale.com)
            $codeHeader = '"SKU","Name","URL to product","Price","Retail Price","URL to image","URL to thumbnail image","Commission","Category","SubCategory","Description","SearchTerms","Status","Your MerchantID","Custom 1","Custom 2","Custom 3","Custom 4","Custom 5","Manufacturer","PartNumber","MerchantCategory","MerchantSubcategory","ShortDescription","ISBN","UPC","CrossSell","MerchantGroup","MerchantSubgroup","CompatibleWith","CompareTo","QuantityDiscount","Bestseller","AddToCartURL","ReviewsRSSURL","Option1","Option2","Option3","Option4","Option5","customCommissions","customCommissionIsFlatRate","customCommissionNewCustomerMultiplier","mobileURL","mobileImage","mobileThumbnail","ReservedForFutureUse","ReservedForFutureUse","ReservedForFutureUse","ReservedForFutureUse"';
            $codeItem = '{product.sku},{product.name},{product.url},{product.special_price},{product.price},{product.image_url},{product.thumbnail_url},,,,{product.description},,"instock",YOUR_MERCHANT_ID,,,,,,{product.manufacturer},,{category.name},,{product.short_description},,,,,,,,,,,,,,,,,,,,,,,,,,';

            $templates = $this->_feedTemplateFactory
                ->create()
                ->getCollection()
                ->addFieldToFilter('type_entity', 0)
                ->addFieldToFilter('url_key', 'shareasale.csv');

            foreach ($templates as $template) {
                $template
                    ->setData('code_header', $codeHeader)
                    ->setData('code_item', $codeItem)
                    ->save();

                // Update feeds.
                $feeds = $this->_feedTemplateFactory
                    ->create()
                    ->getCollection()
                    ->addFieldToFilter('type_entity', 1)
                    ->addFieldToFilter('template_id', $template->getId());

                foreach ($feeds as $feed) {
                    $feed
                        ->setData('code_header', $codeHeader)
                        ->setData('code_item', $codeItem)
                        ->save();
                }
            }
        }

        if (version_compare($context->getVersion(), '2.1.0', '<')) {
            // Update Condition
            $attributes = ['status', 'visibility', 'quantity_and_stock_status'];
            $model = $this->attributeFactory->create();
            foreach ($attributes as $attribute_code) {
                $attr = $model->loadByCode(\Magento\Catalog\Model\Product::ENTITY, $attribute_code);
                if ($attr->getId()) {
                    $attr
                        ->setIsUsedForPromoRules(1)
                        ->save();
                }
            }

            $items = $this->_feedTemplateFactory
                ->create()
                ->getCollection()
                ->addFieldToFilter('type_feed', 1);

            foreach ($items as $item) {
                $item
                    ->setDefaultConditions(\Plumrocket\Datagenerator\Helper\Data::DEFAULT_CONDITION)
                    ->save();
            }
        }

        if (version_compare($context->getVersion(), '2.1.1', '<')) {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'custom_commissions_flat_rate',
                [
                    'type'                    => 'int',
                    'backend'                 => '',
                    'frontend'                => '',
                    'label'                   => 'Commission',
                    'input'                   => 'select',
                    'class'                   => '',
                    'source'                  => 'Plumrocket\Datagenerator\Model\Source\ShareASaleCommissionsFlatRate',
                    'global'                  => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                    'visible'                 => true,
                    'required'                => false,
                    'user_defined'            => 0,
                    'default'                 => 0,
                    'searchable'              => false,
                    'filterable'              => false,
                    'comparable'              => false,
                    'visible_on_front'        => false,
                    'used_in_product_listing' => false,
                    'unique'                  => false,
                    'apply_to'                => '',
                    'position'                => 10
                ]
            );

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'custom_commission',
                [
                    'type'                    => 'text',
                    'backend'                 => '',
                    'frontend'                => '',
                    'label'                   => 'Commission',
                    'input'                   => 'text',
                    'class'                   => '',
                    'global'                  => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                    'visible'                 => true,
                    'required'                => false,
                    'user_defined'            => 0,
                    'default'                 => 0,
                    'searchable'              => false,
                    'filterable'              => false,
                    'comparable'              => false,
                    'visible_on_front'        => false,
                    'used_in_product_listing' => false,
                    'unique'                  => false,
                    'apply_to'                => '',
                    'position'                => 20
                ]
            );

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'share_a_sale_subcategory',
                [
                    'type'                    => 'text',
                    'backend'                 => '',
                    'frontend'                => '',
                    'label'                   => 'Category',
                    'input'                   => 'select',
                    'class'                   => '',
                    'source'                  => 'Plumrocket\Datagenerator\Model\Source\ShareASaleCategory',
                    'global'                  => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                    'visible'                 => true,
                    'required'                => false,
                    'user_defined'            => 0,
                    'default'                 => 0,
                    'searchable'              => false,
                    'filterable'              => false,
                    'comparable'              => false,
                    'visible_on_front'        => false,
                    'used_in_product_listing' => false,
                    'unique'                  => false,
                    'apply_to'                => '',
                    'position'                => 30
                ]
            );

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'custom_commissions_flat_rate',
                [
                    'type'                    => 'int',
                    'backend'                 => '',
                    'frontend'                => '',
                    'label'                   => 'Commission Type',
                    'input'                   => 'select',
                    'group'                   => 'Data Feed Generator (ShareASale)',
                    'class'                   => '',
                    'source'                  => 'Plumrocket\Datagenerator\Model\Source\ShareASaleCommissionsFlatRate',
                    'global'                  => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                    'visible'                 => true,
                    'required'                => false,
                    'user_defined'            => 0,
                    'default'                 => 0,
                    'searchable'              => false,
                    'filterable'              => false,
                    'comparable'              => false,
                    'visible_on_front'        => false,
                    'used_in_product_listing' => false,
                    'unique'                  => false,
                    'apply_to'                => '',
                    'position'                => 10
                ]
            );

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'custom_commission',
                [
                    'type'                    => 'text',
                    'backend'                 => '',
                    'frontend'                => '',
                    'label'                   => 'Commission',
                    'input'                   => 'text',
                    'group'                   => 'Data Feed Generator (ShareASale)',
                    'class'                   => '',
                    'global'                  => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                    'visible'                 => true,
                    'required'                => false,
                    'user_defined'            => 0,
                    'default'                 => 0,
                    'searchable'              => false,
                    'filterable'              => false,
                    'comparable'              => false,
                    'visible_on_front'        => false,
                    'used_in_product_listing' => false,
                    'unique'                  => false,
                    'apply_to'                => '',
                    'position'                => 20
                ]
            );

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'share_a_sale_subcategory',
                [
                    'type'                    => 'text',
                    'backend'                 => '',
                    'frontend'                => '',
                    'label'                   => 'Category',
                    'input'                   => 'select',
                    'group'                   => 'Data Feed Generator (ShareASale)',
                    'class'                   => '',
                    'source'                  => 'Plumrocket\Datagenerator\Model\Source\ShareASaleCategory',
                    'global'                  => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                    'visible'                 => true,
                    'required'                => false,
                    'user_defined'            => 0,
                    'default'                 => 0,
                    'searchable'              => false,
                    'filterable'              => false,
                    'comparable'              => false,
                    'visible_on_front'        => false,
                    'used_in_product_listing' => false,
                    'unique'                  => false,
                    'apply_to'                => '',
                    'position'                => 30
                ]
            );

            $setup->endSetup();
        }

        /**
         * Version 2.1.3
         */
        if (version_compare($context->getVersion(), '2.1.3', '<')) {
            $setup->getConnection()->update(
                $setup->getTable('plumrocket_datagenerator_templates'),
                ['name' => 'Awin (awin.com)', 'url_key' => 'awin.csv'],
                $setup->getConnection()->quoteInto('name = ?', 'Affiliate Window (affiliatewindow.com)')
            );
        }

        /**
         * Version 2.1.6
         */
        if (version_compare($context->getVersion(), '2.1.6', '<')) {
            $setup->getConnection()->update(
                $setup->getTable('plumrocket_datagenerator_templates'),
                [
                    'code_header' => '"ID", "Title", "Description", "Link", "Image Link", "Availability", "Price"',
                    'code_item' => '{product.entity_id},{product.name},{no_br_html}{product.short_description}{/no_br_html},{product.url},{product.image_url},{product.stock_status},{product.price}'
                ],
                $setup->getConnection()->quoteInto('name = ?', 'Commission Junction (cj.com)')
            );
        }

        /**
         * Version 2.2.0
         */
        if (version_compare($context->getVersion(), '2.2.0', '<')) {
            $setup->getConnection()->update(
                $setup->getTable('eav_attribute_group'),
                [
                    'attribute_group_name' => 'Data Feed Generator',
                    'attribute_group_code' => 'data-feed-generator',
                ],
                $setup->getConnection()->quoteInto(
                    'attribute_group_code = ?',
                    'data-feed-generator-shareasale'
                )
            );

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'google_product_category',
                [
                    'type'                    => 'text',
                    'backend'                 => '',
                    'label'                   => 'Google Product Category Mapping',
                    'input'                   => 'text',
                    'class'                   => '',
                    'global'                  => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                    'visible'                 => true,
                    'frontend'                => '',
                    'required'                => false,
                    'user_defined'            => 0,
                    'default'                 => '',
                    'searchable'              => false,
                    'filterable'              => false,
                    'comparable'              => false,
                    'visible_on_front'        => false,
                    'used_in_product_listing' => false,
                    'unique'                  => false,
                    'apply_to'                => '',
                    'position'                => 40
                ]
            );

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'google_product_category',
                [
                    'type'                    => 'text',
                    'backend'                 => '',
                    'label'                   => 'Google Product Category Mapping',
                    'input'                   => 'text',
                    'class'                   => '',
                    'global'                  => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                    'group'                   => 'Data Feed Generator',
                    'visible'                 => true,
                    'frontend'                => '',
                    'required'                => false,
                    'user_defined'            => 0,
                    'default'                 => '',
                    'searchable'              => false,
                    'filterable'              => false,
                    'comparable'              => false,
                    'visible_on_front'        => false,
                    'used_in_product_listing' => false,
                    'unique'                  => false,
                    'apply_to'                => '',
                    'position'                => 40
                ]
            );

            $googleShoppingTemplate = $this->_feedTemplateFactory->create();
            $googleShoppingTemplate->setData([
                'type_entity' => '0',
                'type_feed' => '1',
                'name' => 'Google Shopping Feed',
                'url_key' => 'googlemerchantfeed.xml',
                'count' => '0',
                'enabled' => '1',
                'store_id' => '0',
                'cache_time' => '86400',
                'template_id' => '0',
                'code_header' => "<feed>\r\n <title>{store.name}</title>\r\n  <link rel=\"self\" href=\"{store.url}\"/>\r\n  <updated>{store.now}</updated>",
                'code_item' => "<entry>\r\n  <g:id>{product.sku}</g:id>\r\n  <g:title>{product.title}</g:title>\r\n  <g:description>{product.description}</g:description>\r\n  <g:link>{product.url}</g:link>\r\n  <g:image_link>{product.image_url}</g:image_link>\r\n  <g:availability>{product.in_stock}</g:availability>\r\n  <g:price>{product.price}</g:price>\r\n </entry>",
                'code_footer' => '</feed>',
                'created_at' => '0000-00-00 00:00:00',
                'updated_at' => '0000-00-00 00:00:00',
                'show_category_tab' => 1
            ])->save();
        }

        /**
         * Version 2.2.1
         */
        if (version_compare($context->getVersion(), '2.2.1', '<')) {
            $header = <<<XML
<rss version='2.0' xmlns:g='http://base.google.com/ns/1.0'>
  <channel>
        <title>{site.name}</title>
        <link rel="self" href="{site.url}"/>
        <updated>{site.now}</updated>
XML;
            $codeItem = <<<XML
        <entry>
            <g:id>{product.sku}</g:id>
            <g:title>{product.name}</g:title>
            <g:description>{product.description}</g:description>
            <g:link>{product.url}</g:link>
            <g:image_link>{product.image_url}</g:image_link>
            <g:availability>{product.stock_status}</g:availability>
            <g:price>{product.price_with_currency}</g:price>
        </entry>
XML;
            $footer = <<<XML
  </channel>
</rss>
XML;
            $setup->getConnection()->update(
                $setup->getTable('plumrocket_datagenerator_templates'),
                [
                    'code_header' => $header,
                    'code_item' => $codeItem,
                    'code_footer' => $footer
                ],
                'name = \'Google Shopping Feed\' AND type_entity = 0'
            );
        }

        /**
         * Version 2.2.2
         */
        if (version_compare($context->getVersion(), '2.2.2', '<')) {
            $eavSetup->updateAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'google_product_category',
                ['frontend_input' => 'text']
            );
            $eavSetup->updateAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'google_product_category',
                ['frontend_input' => 'text']
            );
        }

        if (version_compare($context->getVersion(), '2.3.0', '<')) {
            $codeItem = <<<XML
        <entry>
            <g:id>{product.sku}</g:id>
            <g:title>{product.name}</g:title>
            <g:description>{product.description}</g:description>
            <g:link>{product.url}</g:link>
            <g:image_link>{product.image_url|image_id:product_page_image_medium}</g:image_link>
            <g:availability>{product.stock_status}</g:availability>
            <g:price>{product.price_with_currency}</g:price>
            {product.media_gallery|repeat:gallery_image|limit:10}
                <g:additional_image_link>{gallery_image.url}</g:additional_image_link>
            {/product.media_gallery}
        </entry>
XML;
            $setup->getConnection()->update(
                $setup->getTable('plumrocket_datagenerator_templates'),
                ['code_item' => $codeItem],
                'name = \'Google Shopping Feed\' AND type_entity = 0'
            );
        }
    }
}

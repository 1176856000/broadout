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

namespace Plumrocket\Datagenerator\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Plumrocket\Datagenerator\Model\TemplateFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * phpcs:ignoreFile
 */
class InstallData implements InstallDataInterface
{
    /**
     * Eav setup factory
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $_eavSetupFactory;

    /**
     * Temaplate factory
     * @var \Plumrocket\Affiliate\Model\TypeFactory
     */
    private $_feedTemplate;

    /**
     * @param EavSetupFactory $eavSetupFactory
     * @param TemplateFactory $feedTemplate
     * @param \Magento\Framework\App\State $state
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        TemplateFactory $feedTemplate,
        \Magento\Framework\App\State $state
    ) {
        try {
            $state->setAreaCode('adminhtml');
        } catch (\Exception $e) {

        }
        $this->_eavSetupFactory = $eavSetupFactory;
        $this->_feedTemplate = $feedTemplate;
    }

    /**
     * Install Data
     * @param  ModuleDataSetupInterface $setup
     * @param  ModuleContextInterface   $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'trdoubler_cat_id',
            [
                'type' => 'text',
                'backend' => '',
                'frontend' => '',
                'label' => 'Tradedoubler Category ID',
                'input' => 'text',
                'class' => '',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => 0,
                'default' => 0,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => '',
                'position' => 250
            ]
        );

        $templates = [
              [
                'type_entity' => 'template',
                'type_feed' => '1',
                'name' => 'Yipit Daily Deal Aggregator (yipit.com)',
                'url_key' => 'yipit.xml',
                'count' => '0',
                'enabled' => '1',
                'store_id' => '0',
                'cache_time' => '86400',
                'template_id' => '0',
                'code_header' => '<deals>',
                'code_item' => "<deal>\r\n  <id>{product.entity_id}</id>\r\n  <title>{product.name}</title>\r\n  <deal_url>{product.url}</deal_url>\r\n  <img_url>{product.image_url}</img_url>\r\n  <deal_price>{product.special_price}</deal_price>\r\n  <deal_value>{product.price}</deal_value>\r\n  <items_sold>{product.sold}</items_sold>\r\n  <items_available>{product.qty}</items_available>\r\n  <business_name>{site.name}</business_name>\r\n  <business_address>{site.address}</business_address>\r\n  <business_phone>{site.phone}</business_phone>\r\n</deal>",
                'code_footer' => '</deals>',
                'created_at' => '0000-00-00 00:00:00',
                'updated_at' => '0000-00-00 00:00:00',
                'replace_from' => null,
                'replace_to' => null
              ],
              [
                'type_entity' => 'template',
                'type_feed' => '1',
                'name' => 'Post Affiliate Pro (postaffiliatepro.com)',
                'url_key' => 'postaffiliatepro.rss',
                'count' => '0',
                'enabled' => '1',
                'store_id' => '0',
                'cache_time' => '86400',
                'template_id' => '0',
                'code_header' => "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n  <rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\" xmlns:ecommerce=\"http://shopping.discovery.com/erss/\" xmlns:media=\"http://search.yahoo.com/mrss\" >\r\n  <channel>\r\n  <title>{site.name}</title>\r\n  <link>{site.url}</link>\r\n  <description></description>\r\n  <language>en</language>\r\n  <lastBuildDate>{site.now}</lastBuildDate>",
                'code_item' => "<item>\r\n  <title>{product.name}</title>\r\n  <category>{category.name}</category>\r\n  <link>{product.url}</link>\r\n  <description>{product.description}</description>\r\n</item>",
                'code_footer' => "</channel>\r\n</rss>",
                'created_at' => '0000-00-00 00:00:00',
                'updated_at' => '0000-00-00 00:00:00',
                'replace_from' => null,
                'replace_to' => null
              ],
              [
                'type_entity' => 'template',
                'type_feed' => '1',
                'name' => 'HasOffers (hasoffers.com)',
                'url_key' => 'hasoffers.xml',
                'count' => '0',
                'enabled' => '1',
                'store_id' => '0',
                'cache_time' => '86400',
                'template_id' => '0',
                'code_header' => "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n  <ProductFeed ProcessDate=\"{site.now}\">\r\n  <Products>",
                'code_item' => "<Product ProductID=\"{product.entity_id}\">\r\n  <SKU>{product.sku}</SKU>\r\n  <Name>{product.name}</Name>\r\n  <Description>{product.description}</Description>\r\n  <URL>{product.url}</URL>\r\n  <Price>{product.special_price}</Price>\r\n  <Currency>USD</Currency>\r\n  <SmallImage>{product.small_image_url}</SmallImage>\r\n  <MediumImage>{product.thumbnail_url}</MediumImage>\r\n  <LargeImage>{product.image_url}</LargeImage>\r\n  <Merchant>{category.brand_name}</Merchant>\r\n  <Categories>\r\n  <Category Id=\"{category.entity_id}\">\r\n  <Name>{category.name}</Name>\r\n  <Path>{category.url_path}</Path>\r\n</Category>\r\n</Categories>\r\n</Product>",
                'code_footer' => "</Products>\r\n</ProductFeed>",
                'created_at' => '0000-00-00 00:00:00',
                'updated_at' => '0000-00-00 00:00:00',
                'replace_from' => null,
                'replace_to' => null
              ],
              [
                'type_entity' => 'template',
                'type_feed' => '1',
                'name' => 'CSV Data Feed',
                'url_key' => 'feed.csv',
                'count' => '0',
                'enabled' => '1',
                'store_id' => '0',
                'cache_time' => '86400',
                'template_id' => '0',
                'code_header' => '"cost","country_of_manufacture","created_at","custom_design","custom_design_from","custom_design_to","custom_layout_update","description","enable_googlecheckout","estimated_delivery_date","estimated_delivery_enable","estimated_delivery_text","estimated_shipping_date_from","estimated_shipping_date_to","estimated_shipping_enable","estimated_shipping_text","estimated_splitter","gallery","gift_message_available","has_options","image","image_label","is_recurring","links_exist","links_purchased_separately","links_title","manufacturer","media_gallery","meta_description","meta_keyword","meta_title","minimal_price","msrp","msrp_display_actual_price_type","msrp_enabled","name","news_from_date","news_to_date","old_id","options_container","page_layout","price","price_type","price_view","recurring_profile","required_options","samples_title","shipment_type","short_description","size","sku","sku_type","small_image","small_image_label","special_from_date","special_price","special_to_date","status","tax_class_id","thumbnail","thumbnail_label","tier_price","updated_at","url_key","url_path","visibility","weight","weight_type","url","open_url","image_url","small_image_url","thumbnail_url","sold","special_price","category_name","no_html_description","qty","category_additional_image","category_all_children","category_available_sort_by","category_brand","category_children","category_children_count","category_custom_apply_to_products","category_custom_design","category_custom_design_from","category_custom_design_to","category_custom_layout_update","category_custom_use_parent_settings","category_default_sort_by","category_description","category_display_mode","category_estimated_delivery_date","category_estimated_delivery_enable","category_estimated_delivery_text","category_estimated_shipping_date_from","category_estimated_shipping_date_to","category_estimated_shipping_enable","category_estimated_shipping_text","category_filter_price_range","category_image","category_include_in_menu","category_is_active","category_is_anchor","category_is_top","category_landing_page","category_level","category_meta_description","category_meta_keywords","category_meta_title","category_name","category_page_layout","category_path","category_path_in_store","category_position","category_thumbnail","category_url_key","category_url_path","category_url","category_open_url","category_image_url","category_thumbnail_url"',
                'code_item' => '{product.cost},{product.country_of_manufacture},{product.created_at},{product.custom_design},{product.custom_design_from},{product.custom_design_to},{product.custom_layout_update},{product.description},{product.enable_googlecheckout},{product.estimated_delivery_date},{product.estimated_delivery_enable},{product.estimated_delivery_text},{product.estimated_shipping_date_from},{product.estimated_shipping_date_to},{product.estimated_shipping_enable},{product.estimated_shipping_text},{product.estimated_splitter},{product.gallery},{product.gift_message_available},{product.has_options},{product.image},{product.image_label},{product.is_recurring},{product.links_exist},{product.links_purchased_separately},{product.links_title},{product.manufacturer},{product.media_gallery},{product.meta_description},{product.meta_keyword},{product.meta_title},{product.minimal_price},{product.msrp},{product.msrp_display_actual_price_type},{product.msrp_enabled},{product.name},{product.news_from_date},{product.news_to_date},{product.old_id},{product.options_container},{product.page_layout},{product.price},{product.price_type},{product.price_view},{product.recurring_profile},{product.required_options},{product.samples_title},{product.shipment_type},{product.short_description},{product.size},{product.sku},{product.sku_type},{product.small_image},{product.small_image_label},{product.special_from_date},{product.special_price},{product.special_to_date},{product.status},{product.tax_class_id},{product.thumbnail},{product.thumbnail_label},{product.tier_price},{product.updated_at},{product.url_key},{product.url_path},{product.visibility},{product.weight},{product.weight_type},{product.url},{product.open_url},{product.image_url},{product.small_image_url},{product.thumbnail_url},{product.sold},{product.special_price},{product.category_name},{product.no_html_description},{product.qty},{category.additional_image},{category.all_children},{category.available_sort_by},{category.brand},{category.children},{category.children_count},{category.custom_apply_to_products},{category.custom_design},{category.custom_design_from},{category.custom_design_to},{category.custom_layout_update},{category.custom_use_parent_settings},{category.default_sort_by},{category.description},{category.display_mode},{category.estimated_delivery_date},{category.estimated_delivery_enable},{category.estimated_delivery_text},{category.estimated_shipping_date_from},{category.estimated_shipping_date_to},{category.estimated_shipping_enable},{category.estimated_shipping_text},{category.filter_price_range},{category.image},{category.include_in_menu},{category.is_active},{category.is_anchor},{category.is_top},{category.landing_page},{category.level},{category.meta_description},{category.meta_keywords},{category.meta_title},{category.name},{category.page_layout},{category.path},{category.path_in_store},{category.position},{category.thumbnail},{category.url_key},{category.url_path},{category.url},{category.open_url},{category.image_url},{category.thumbnail_url}',
                'code_footer' => '',
                'created_at' => '0000-00-00 00:00:00',
                'updated_at' => '0000-00-00 00:00:00',
                'replace_from' => null,
                'replace_to' => null
              ],
              [
                'type_entity' => 'template',
                'type_feed' => '1',
                'name' => 'RSS Datafeed',
                'url_key' => 'feed.rss',
                'count' => '0',
                'enabled' => '1',
                'store_id' => '0',
                'cache_time' => '86400',
                'template_id' => '0',
                'code_header' => "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n  <rss version=\"2.0\">\r\n  <channel>\r\n  <title>RSS {site.name}</title>\r\n  <description>{site.name}</description>\r\n  <link>{site.url}</link>\r\n  <lastBuildDate>{site.now}</lastBuildDate>\r\n  <pubDate>{site.now}</pubDate>\r\n  <ttl>1800</ttl>",
                'code_item' => "<item>\r\n  <title>{product.name}</title>\r\n  <description>{product.description}</description>\r\n  <link>{product.url}</link>\r\n  <category>{category.name}</category>\r\n  <image>{product.thumbnail_url}</image>\r\n  <guid>{product.sku}</guid>\r\n  <pubDate>{product.created_at}</pubDate>\r\n</item>",
                'code_footer' => "</channel>\r\n</rss>",
                'created_at' => '0000-00-00 00:00:00',
                'updated_at' => '0000-00-00 00:00:00',
                'replace_from' => null,
                'replace_to' => null
              ],
              [
                'type_entity' => 'template',
                'type_feed' => '1',
                'name' => 'AvantLink Affiliate Network (avantlink.com)',
                'url_key' => 'avantlink.csv',
                'count' => '0',
                'enabled' => '1',
                'store_id' => '0',
                'cache_time' => '86400',
                'template_id' => '0',
                'code_header' => '"SKU","Brand Name","Product Name","Long Description","Category","Image URL","Buy Link","Retail Price","Sale Price","Brand Logo Image","Variants XML"',
                'code_item' => '{product.sku},{category.brand_name},{product.name},{product.description},{category.name},{product.image_url},{product.url},{product.price},{product.special_price},{category.brand_image},"{no_quotes}{product.child_items}<variants>{product.child}<variant><sku>{child.sku}</sku><color>{child.color}</color><size>{child.size}</size><detail_url>{product.url}</detail_url><action_url>{product.open_url}</action_url></variant>{/product.child}</variants>{/product.child_items}{/no_quotes}"',
                'code_footer' => '',
                'created_at' => '0000-00-00 00:00:00',
                'updated_at' => '0000-00-00 00:00:00',
                'replace_from' => null,
                'replace_to' => null
              ],
              [
                'type_entity' => 'template',
                'type_feed' => '1',
                'name' => 'XML Data Feed',
                'url_key' => 'feed.xml',
                'count' => '0',
                'enabled' => '1',
                'store_id' => '0',
                'cache_time' => '86400',
                'template_id' => '0',
                'code_header' => "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<items>",
                'code_item' => "<item>\r\n  <category_ids>{product.category_ids}</category_ids>\r\n  <color>{product.color}</color>\r\n  <cost>{product.cost}</cost>\r\n  <country_of_manufacture>{product.country_of_manufacture}</country_of_manufacture>\r\n  <created_at>{product.created_at}</created_at>\r\n  <custom_design>{product.custom_design}</custom_design>\r\n  <custom_design_from>{product.custom_design_from}</custom_design_from>\r\n  <custom_design_to>{product.custom_design_to}</custom_design_to>\r\n  <custom_layout_update>{product.custom_layout_update}</custom_layout_update>\r\n  <description>{product.description}</description>\r\n  <enable_googlecheckout>{product.enable_googlecheckout}</enable_googlecheckout>\r\n  <estimated_delivery_date>{product.estimated_delivery_date}</estimated_delivery_date>\r\n  <estimated_delivery_enable>{product.estimated_delivery_enable}</estimated_delivery_enable>\r\n  <estimated_delivery_text>{product.estimated_delivery_text}</estimated_delivery_text>\r\n  <estimated_shipping_date_from>{product.estimated_shipping_date_from}</estimated_shipping_date_from>\r\n  <estimated_shipping_date_to>{product.estimated_shipping_date_to}</estimated_shipping_date_to>\r\n  <estimated_shipping_enable>{product.estimated_shipping_enable}</estimated_shipping_enable>\r\n  <estimated_shipping_text>{product.estimated_shipping_text}</estimated_shipping_text>\r\n  <gallery>{product.gallery}</gallery>\r\n  <gift_message_available>{product.gift_message_available}</gift_message_available>\r\n  <has_options>{product.has_options}</has_options>\r\n  <image>{product.image}</image>\r\n  <image_label>{product.image_label}</image_label>\r\n  <is_recurring>{product.is_recurring}</is_recurring>\r\n  <links_exist>{product.links_exist}</links_exist>\r\n  <links_purchased_separately>{product.links_purchased_separately}</links_purchased_separately>\r\n  <links_title>{product.links_title}</links_title>\r\n  <manufacturer>{product.manufacturer}</manufacturer>\r\n  <media_gallery>{product.media_gallery}</media_gallery>\r\n  <meta_description>{product.meta_description}</meta_description>\r\n  <meta_keyword>{product.meta_keyword}</meta_keyword>\r\n  <meta_title>{product.meta_title}</meta_title>\r\n  <minimal_price>{product.minimal_price}</minimal_price>\r\n  <msrp>{product.msrp}</msrp>\r\n  <msrp_display_actual_price_type>{product.msrp_display_actual_price_type}</msrp_display_actual_price_type>\r\n  <msrp_enabled>{product.msrp_enabled}</msrp_enabled>\r\n  <name>{product.name}</name>\r\n  <news_from_date>{product.news_from_date}</news_from_date>\r\n  <news_to_date>{product.news_to_date}</news_to_date>\r\n  <old_id>{product.old_id}</old_id>\r\n  <options_container>{product.options_container}</options_container>\r\n  <page_layout>{product.page_layout}</page_layout>\r\n  <price>{product.price}</price>\r\n  <price_type>{product.price_type}</price_type>\r\n  <price_view>{product.price_view}</price_view>\r\n  <recurring_profile>{product.recurring_profile}</recurring_profile>\r\n  <required_options>{product.required_options}</required_options>\r\n  <samples_title>{product.samples_title}</samples_title>\r\n  <shipment_type>{product.shipment_type}</shipment_type>\r\n  <short_description>{product.short_description}</short_description>\r\n  <size>{product.size}</size>\r\n  <sku>{product.sku}</sku>\r\n  <sku_type>{product.sku_type}</sku_type>\r\n  <small_image>{product.small_image}</small_image>\r\n  <small_image_label>{product.small_image_label}</small_image_label>\r\n  <special_from_date>{product.special_from_date}</special_from_date>\r\n  <special_price>{product.special_price}</special_price>\r\n  <special_to_date>{product.special_to_date}</special_to_date>\r\n  <status>{product.status}</status>\r\n  <tax_class_id>{product.tax_class_id}</tax_class_id>\r\n  <thumbnail>{product.thumbnail}</thumbnail>\r\n  <thumbnail_label>{product.thumbnail_label}</thumbnail_label>\r\n  <tier_price>{product.tier_price}</tier_price>\r\n  <updated_at>{product.updated_at}</updated_at>\r\n  <url_key>{product.url_key}</url_key>\r\n  <url_path>{product.url_path}</url_path>\r\n  <visibility>{product.visibility}</visibility>\r\n  <weight>{product.weight}</weight>\r\n  <weight_type>{product.weight_type}</weight_type>\r\n  <url>{product.url}</url>\r\n  <open_url>{product.open_url}</open_url>\r\n  <image_url>{product.image_url}</image_url>\r\n  <small_image_url>{product.small_image_url}</small_image_url>\r\n  <thumbnail_url>{product.thumbnail_url}</thumbnail_url>\r\n  <sold>{product.sold}</sold>\r\n  <special_price>{product.special_price}</special_price>\r\n  <qty>{product.qty}</qty>\r\n  <category_additional_image>{category.additional_image}</category_additional_image>\r\n  <category_all_children>{category.all_children}</category_all_children>\r\n  <category_available_sort_by>{category.available_sort_by}</category_available_sort_by>\r\n  <category_brand>{category.brand}</category_brand>\r\n  <category_children>{category.children}</category_children>\r\n  <category_children_count>{category.children_count}</category_children_count>\r\n  <category_custom_apply_to_products>{category.custom_apply_to_products}</category_custom_apply_to_products>\r\n  <category_custom_design>{category.custom_design}</category_custom_design>\r\n  <category_custom_design_from>{category.custom_design_from}</category_custom_design_from>\r\n  <category_custom_design_to>{category.custom_design_to}</category_custom_design_to>\r\n  <category_custom_layout_update>{category.custom_layout_update}</category_custom_layout_update>\r\n  <category_custom_use_parent_settings>{category.custom_use_parent_settings}</category_custom_use_parent_settings>\r\n  <category_default_sort_by>{category.default_sort_by}</category_default_sort_by>\r\n  <category_description>{category.description}</category_description>\r\n  <category_display_mode>{category.display_mode}</category_display_mode>\r\n  <category_estimated_delivery_date>{category.estimated_delivery_date}</category_estimated_delivery_date>\r\n  <category_estimated_delivery_enable>{category.estimated_delivery_enable}</category_estimated_delivery_enable>\r\n  <category_estimated_delivery_text>{category.estimated_delivery_text}</category_estimated_delivery_text>\r\n  <category_estimated_shipping_date_from>{category.estimated_shipping_date_from}</category_estimated_shipping_date_from>\r\n  <category_estimated_shipping_date_to>{category.estimated_shipping_date_to}</category_estimated_shipping_date_to>\r\n  <category_estimated_shipping_enable>{category.estimated_shipping_enable}</category_estimated_shipping_enable>\r\n  <category_estimated_shipping_text>{category.estimated_shipping_text}</category_estimated_shipping_text>\r\n  <category_filter_price_range>{category.filter_price_range}</category_filter_price_range>\r\n  <category_image>{category.image}</category_image>\r\n  <category_include_in_menu>{category.include_in_menu}</category_include_in_menu>\r\n  <category_is_active>{category.is_active}</category_is_active>\r\n  <category_is_anchor>{category.is_anchor}</category_is_anchor>\r\n  <category_is_top>{category.is_top}</category_is_top>\r\n  <category_landing_page>{category.landing_page}</category_landing_page>\r\n  <category_level>{category.level}</category_level>\r\n  <category_meta_description>{category.meta_description}</category_meta_description>\r\n  <category_meta_keywords>{category.meta_keywords}</category_meta_keywords>\r\n  <category_meta_title>{category.meta_title}</category_meta_title>\r\n  <category_name>{category.name}</category_name>\r\n  <category_page_layout>{category.page_layout}</category_page_layout>\r\n  <category_path>{category.path}</category_path>\r\n  <category_path_in_store>{category.path_in_store}</category_path_in_store>\r\n  <category_position>{category.position}</category_position>\r\n  <category_thumbnail>{category.thumbnail}</category_thumbnail>\r\n  <category_url_key>{category.url_key}</category_url_key>\r\n  <category_url_path>{category.url_path}</category_url_path>\r\n  <category_url>{category.url}</category_url>\r\n  <category_open_url>{category.open_url}</category_open_url>\r\n  <category_image_url>{category.image_url}</category_image_url>\r\n  <category_thumbnail_url>{category.thumbnail_url}</category_thumbnail_url>\r\n</item>",
                'code_footer' => '</items>',
                'created_at' => '0000-00-00 00:00:00',
                'updated_at' => '0000-00-00 00:00:00',
                'replace_from' => null,
                'replace_to' => null
              ],
              [
                'type_entity' => 'template',
                'type_feed' => '1',
                'name' => 'Commission Junction (cj.com)',
                'url_key' => 'cj.csv',
                'count' => '0',
                'enabled' => '1',
                'store_id' => '0',
                'cache_time' => '86400',
                'template_id' => '0',
                'code_header' => "&CID=3456789\r\n&SUBID=123\r\n&PROCESSTYPE=OVERWRITE\r\n&AID=9876543\r\n&PARAMETERS=NAME|KEYWORDS|DESCRIPTION|SKU|BUYURL|AVAILABLE|IMAGEURL|PRICE|RETAILPRICE|SALEPRICE|CURRENCY|UPC|PROMOTIONALTEXT|ADVERTISERCATEGORY|MANUFACTURER|MANUFACTURERID|ISBN|AUTHOR|ARTIST|PUBLISHER|TITLE|LABEL|FORMAT|SPECIAL|GIFT|THIRDPARTYID|THIRDPARTYCATEGORY|OFFLINE|ONLINE|INSTOCK|CONDITION|WARRANTY|STANDARDSHIPPINGCOST",
                'code_item' => '{product.name},{product.meta_keyword},{no_br_html}{product.description}{/no_br_html},{product.sku},{product.url},YES,{product.image_url},{product.price},{product.price},{product.special_price},USD,,{no_br_html}{product.short_description}{/no_br_html},{category.name},{product.manufacturer},,,,,,,,,NO,,{category.url_key},{category.name},NO,YES,YES,New,,',
                'code_footer' => '',
                'created_at' => '0000-00-00 00:00:00',
                'updated_at' => '0000-00-00 00:00:00',
                'replace_from' => '',
                'replace_to' => ''
              ],
              [
                'type_entity' => 'template',
                'type_feed' => '1',
                'name' => 'ShareASale (shareasale.com)',
                'url_key' => 'shareasale.csv',
                'count' => '0',
                'enabled' => '1',
                'store_id' => '0',
                'cache_time' => '86400',
                'template_id' => '0',
                'code_header' => '"ProductID","Name","MerchantID","Link","Thumbnail","BigImage","Price","RetailPrice","Category","Subcategory","Description","Lastupdated","Status","Manufacturer","ShortDescription","SKU"',
                'code_item' => '{product.id},{product.name},,{product.url},{product.thumbnail_url},{product.image_url},{product.special_price},{product.price},{category.name},"",{product.description},{product.updated_at},"instock",{product.manufacturer},{product.short_description},{product.sku}',
                'code_footer' => '',
                'created_at' => '0000-00-00 00:00:00',
                'updated_at' => '0000-00-00 00:00:00',
                'replace_from' => '',
                'replace_to' => ''
              ],
              [
                'type_entity' => 'template',
                'type_feed' => '1',
                'name' => 'LinkShare (linkshare.com)',
                'url_key' => 'linkshare.txt',
                'count' => '0',
                'enabled' => '1',
                'store_id' => '0',
                'cache_time' => '86400',
                'template_id' => '0',
                'code_header' => 'HDR|__PLEASE_PROVICE_MERCHANT_ID__|{site.name}|{site.now|date_format:Y-m-d/H:i:s}',
                'code_item' => '{product.id}|{product.name}|{product.sku}|{category.name}||{product.url}|{product.image_url}||{no_br_html}{product.short_description}{/no_br_html}|{no_br_html}{product.description}{/no_br_html}||||{product.price}|||||N|{product.meta_keyword}|N||||In Stock|||Y|Y|Y|USD||',
                'code_footer' => 'TRL|{site.count}',
                'created_at' => '0000-00-00 00:00:00',
                'updated_at' => '0000-00-00 00:00:00',
                'replace_from' => '|',
                'replace_to' => null
              ],
              [
                'type_entity' => 'template',
                'type_feed' => '1',
                'name' => 'eBay Enterprise Display & Retargeting (formerly Fetchback, fetchback.com)',
                'url_key' => 'fetchback.csv',
                'count' => '0',
                'enabled' => '1',
                'store_id' => '0',
                'cache_time' => '86400',
                'template_id' => '0',
                'code_header' => '"Id","Name","Description","Price","Image_URL","Click_URL","Category"',
                'code_item' => '{product.sku},{product.name},{product.short_description},{product.price},{product.image_url|size:150},{product.url},{category.breadcrumb_path}',
                'code_footer' => '',
                'created_at' => '0000-00-00 00:00:00',
                'updated_at' => '0000-00-00 00:00:00',
                'replace_from' => null,
                'replace_to' => null
              ],
              [
                'type_entity' => 'template',
                'type_feed' => '1',
                'name' => 'eBay Enterprise Affiliate Network (formerly PepperJam Exchange, pepperjamnetwork.com)',
                'url_key' => 'pepperjamnetwork.txt',
                'count' => '0',
                'enabled' => '1',
                'store_id' => '0',
                'cache_time' => '86400',
                'template_id' => '0',
                'code_header' => "name\tsku\tbuy_url\timage_url\timage_thumb_url\tdescription_short\tdescription_long\tprice\tkeywords\tcategory_program\tweight",
                'code_item' => "{product.name|truncate:128}\t{product.sku}\t{product.url}\t{product.image_url}\t{product.thumbnail_url}\t{product.short_description|truncate:512}\t{product.description|truncate:2000}\t{product.price}\t{product.meta_keyword|replace:,:}\t{category.breadcrumb_path|truncate:256}\t{product.weight}",
                'code_footer' => '',
                'created_at' => '0000-00-00 00:00:00',
                'updated_at' => '0000-00-00 00:00:00',
                'replace_from' => null,
                'replace_to' => null
              ],
              [
                'type_entity' => 'template',
                'type_feed' => '1',
                'name' => 'Affiliate Window (affiliatewindow.com)',
                'url_key' => 'affiliatewindow.csv',
                'count' => '0',
                'enabled' => '1',
                'store_id' => '0',
                'cache_time' => '86400',
                'template_id' => '0',
                'code_header' => '"product_id","product_name","price","deep_link","description","image_url","thumb_url","keywords","last_updated"',
                'code_item' => '{product.sku},{product.name|truncate:255},{product.price_with_tax},{product.url},{product.short_description},{product.image_url|size:200},{product.thumbnail_url|size:70},{product.meta_keyword},{product.updated_at}',
                'code_footer' => '',
                'created_at' => '0000-00-00 00:00:00',
                'updated_at' => '0000-00-00 00:00:00',
                'replace_from' => null,
                'replace_to' => null
              ],
              [
                'type_entity' => 'template',
                'type_feed' => '1',
                'name' => 'Zanox (zanox.com)',
                'url_key' => 'zanox.csv',
                'count' => '0',
                'enabled' => '1',
                'store_id' => '0',
                'cache_time' => '86400',
                'template_id' => '0',
                'code_header' => '"ID","Product name","Price","Product URL","Category Path","Description","Long Description","Small image URL","Medium image URL","Large image URL"',
                'code_item' => '{product.sku},{product.name|truncate:150},{product.price_with_tax},{product.url},{category.breadcrumb_path|replace: > : / },{product.short_description|truncate:512},{product.description|truncate:4096},{product.small_image_url|size:100},{product.image_url|size:400},{product.image_url}',
                'code_footer' => '',
                'created_at' => '0000-00-00 00:00:00',
                'updated_at' => '0000-00-00 00:00:00',
                'replace_from' => null,
                'replace_to' => null
              ],
              [
                'type_entity' => 'template',
                'type_feed' => '1',
                'name' => 'Linkconnector (linkconnector.com)',
                'url_key' => 'linkconnector.csv',
                'count' => '0',
                'enabled' => '1',
                'store_id' => '0',
                'cache_time' => '86400',
                'template_id' => '0',
                'code_header' => '"ProductID","Categories","Title","Description","Price","URL","ThumbURL","ImageURL","Quantity","Keywords"',
                'code_item' => '{product.sku},{category.breadcrumb_path|truncate:60},{product.name|truncate:80},{product.short_description},{product.price},{product.url},{product.thumbnail_url},{product.image_url},{product.qty},{product.meta_keyword}',
                'code_footer' => '',
                'created_at' => '0000-00-00 00:00:00',
                'updated_at' => '0000-00-00 00:00:00',
                'replace_from' => null,
                'replace_to' => null
              ],
              [
                'type_entity' => 'template',
                'type_feed' => '1',
                'name' => 'Webgains (webgains.com)',
                'url_key' => 'webgains.csv',
                'count' => '0',
                'enabled' => '1',
                'store_id' => '0',
                'cache_time' => '86400',
                'template_id' => '0',
                'code_header' => '"product_name","product_id","description","deeplink","price","image_url","category_name","delivery_period","delivery_cost"',
                'code_item' => '{product.name},{product.sku},{product.short_description},{product.url},{product.price},{product.image_url},{category.breadcrumb_path},"",""',
                'code_footer' => '',
                'created_at' => '0000-00-00 00:00:00',
                'updated_at' => '0000-00-00 00:00:00',
                'replace_from' => null,
                'replace_to' => null
              ],
              [
                'type_entity' => 'template',
                'type_feed' => '1',
                'name' => 'PerfomanceHorizon (performancehorizon.com)',
                'url_key' => 'performancehorizon.csv',
                'count' => '0',
                'enabled' => '1',
                'store_id' => '0',
                'cache_time' => '86400',
                'template_id' => '0',
                'code_header' => '"ID","Product name","Price","Product URL","Category Path","Description","Image URL","Thumbnail URL"',
                'code_item' => '{product.sku},{product.name},{product.price_with_tax},{product.url},{category.breadcrumb_path},{product.short_description},{product.image_url},{product.thumbnail_url}',
                'code_footer' => '',
                'created_at' => '0000-00-00 00:00:00',
                'updated_at' => '0000-00-00 00:00:00',
                'replace_from' => null,
                'replace_to' => null
              ],
              [
                'type_entity' => 'template',
                'type_feed' => '1',
                'name' => 'ImpactRadius (impactradius.com)',
                'url_key' => 'impactradius.csv',
                'count' => '0',
                'enabled' => '1',
                'store_id' => '0',
                'cache_time' => '86400',
                'template_id' => '0',
                'code_header' => '"ID","Product name","Price","Product URL","Category Path","Description","Image URL","Thumbnail URL"',
                'code_item' => '{product.sku},{product.name},{product.price_with_tax},{product.url},{category.breadcrumb_path},{product.short_description},{product.image_url},{product.thumbnail_url}',
                'code_footer' => '',
                'created_at' => '0000-00-00 00:00:00',
                'updated_at' => '0000-00-00 00:00:00',
                'replace_from' => null,
                'replace_to' => null
              ],
              [
                'type_entity' => 'template',
                'type_feed' => '1',
                'name' => 'Tradedoubler (tradedoubler.com)',
                'url_key' => 'tradedoubler.xml',
                'count' => '0',
                'enabled' => '1',
                'store_id' => '0',
                'cache_time' => '86400',
                'template_id' => '0',
                'code_header' => "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<products xmlns=\"urn:com:tradedoubler:pf:model:xml:input\" xmlns:cm=\"urn:com:tradedoubler:pf:model:xml:common\" version=\"3.0\">",
                'code_item' => "<product sourceProductId=\"{product.entity_id}\">\r\n  <cm:name>{product.name}</cm:name>\r\n  <cm:description>{product.description}</cm:description>\r\n  <cm:productUrl>{product.url}</cm:productUrl>\r\n  <cm:productImage>{product.image_url}</cm:productImage>\r\n  <cm:price>{product.price}</cm:price>\r\n  <cm:categories>\r\n    <cm:category name=\"{category.name|attrib:yes}\" id=\"{category.trdoubler_cat_id|attrib:yes}\" ></cm:category>\r\n  </cm:categories>\r\n  <cm:shippingCost>{product.shipment_type}</cm:shippingCost>\r\n  <cm:shortDescription>{product.short_description}.</cm:shortDescription>\r\n  <cm:inStock>{product.qty}</cm:inStock>\r\n  <cm:weight>{product.weight}</cm:weight>\r\n  <cm:size>{product.size}</cm:size>\r\n  <cm:manufacturer>{product.manufacturer}</cm:manufacturer>\r\n  <cm:sku>{product.sku}</cm:sku>\r\n</product>",
                'code_footer' => '</products>',
                'created_at' => '0000-00-00 00:00:00',
                'updated_at' => '0000-00-00 00:00:00',
                'replace_from' => null,
                'replace_to' => null
              ]
        ];

        foreach ($templates as $template) {
            $this->_feedTemplate->create()->setData($template)->save();
        }
    }
}

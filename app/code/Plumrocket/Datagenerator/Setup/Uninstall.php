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

/* Uninstall Data Feed Generator */
class Uninstall extends \Plumrocket\Base\Setup\AbstractUninstall
{

    /**
     * {@inheritdoc}
     */
    protected $_configSectionId = 'prdatagenerator';

    /**
     * {@inheritdoc}
     */
    protected $_tables = ['plumrocket_datagenerator_templates'];

    /**
     * {@inheritdoc}
     */
    protected $_attributes = [
        \Magento\Catalog\Model\Category::ENTITY => [
            'trdoubler_cat_id',
            'custom_commission',
            'custom_commissions_flat_rate',
            'share_a_sale_subcategory',
        ],
        \Magento\Catalog\Model\Product::ENTITY => [
            'custom_commission',
            'custom_commissions_flat_rate',
            'share_a_sale_subcategory',
        ],
    ];

    /**
     * {@inheritdoc}
     */
    protected $_pathes = ['/app/code/Plumrocket/Datagenerator'];
}

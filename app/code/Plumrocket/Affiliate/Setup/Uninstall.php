<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Setup;

/* Uninstall Affiliate Programs */
class Uninstall extends \Plumrocket\Base\Setup\AbstractUninstall
{
    protected $_configSectionId = 'praffiliate';
    protected $_tables = [
        'plumrocket_affiliate_affiliate',
        'plumrocket_affiliate_includeon',
        'plumrocket_affiliate_type',
    ];
    protected $_attributes = [\Magento\Catalog\Model\Product::ENTITY => ['affiliate_tradedoubler_groupid']];
    protected $_pathes = ['/app/code/Plumrocket/Affiliate'];
}

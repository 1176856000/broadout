<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Helper;

class Data extends \Plumrocket\Base\Helper\ConfigUtils
{
    /**
     * Is module enabled
     * @param  int $store
     * @return boolean
     */
    public function moduleEnabled($store = null)
    {
        return (bool) $this->getConfig('praffiliate/general/enabled');
    }
}

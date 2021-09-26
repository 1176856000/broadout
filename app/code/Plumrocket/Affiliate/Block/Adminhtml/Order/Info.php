<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Block\Adminhtml\Order;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Plumrocket\Affiliate\Model\AffiliateFactory;
use Plumrocket\Affiliate\Model\Order\InfoFactory as OrderInfoFactory;

class Info extends Template
{
    protected $_info;
    protected $_affiliateNetworks;
    protected $_orderInfoFactory;
    protected $_affiliateFactory;
    protected $_registry;

    public function __construct(
        Context $context,
        OrderInfoFactory $orderInfoFactory,
        AffiliateFactory $affiliateFactory,
        Registry $registry,
        array $data = []
    ) {
        $this->_orderInfoFactory = $orderInfoFactory;
        $this->_affiliateFactory = $affiliateFactory;
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }

    public function getOrder()
    {
        return $this->_registry->registry('current_order');
    }

    public function getInfo()
    {
        if ($this->_info === null) {
            $oder = $this->getOrder();
            $this->_info = $this->_orderInfoFactory
                ->create()
                ->load(
                    $this->getOrder()->getId(),
                    'order_id'
                );
        }

        return $this->_info;
    }

    public function getAffiliateNetworks()
    {
        if ($this->_affiliateNetworks === null) {
            $this->_affiliateNetworks = $this->_affiliateFactory
                ->create()
                ->getCollection()
                ->addStoreToFilter($this->getOrder()->getStoreId());
        }
        return $this->_affiliateNetworks;
    }
}

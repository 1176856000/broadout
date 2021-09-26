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
 * @package     Plumrocket Affiliate v2.x.x
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Plugin\Sales\Model\Order;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Sales\Model\Order\CustomerManagement as OriginOrderIdentity;

class CustomerManagement
{
    /**
     * @var \Plumrocket\Affiliate\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Plumrocket\Affiliate\Helper\Data $dataHelper
     * @param \Magento\Customer\Model\Session   $customerSession
     */
    public function __construct(
        \Plumrocket\Affiliate\Helper\Data $dataHelper,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_dataHelper = $dataHelper;
        $this->_customerSession = $customerSession;
    }

    /**
     * @param \Magento\Sales\Model\Order\CustomerManagement $subject
     * @param                                               $result
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function afterCreate(OriginOrderIdentity $subject, $result)
    {
        if ($this->_dataHelper->moduleEnabled()
            && $result instanceof CustomerInterface
            && $result->getId()
        ) {
            $this->_customerSession->setPlumrocketAffiliateRegisterSuccess(true);
        }

        return $result;
    }
}

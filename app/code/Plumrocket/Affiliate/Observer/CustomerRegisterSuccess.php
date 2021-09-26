<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Observer;

use Magento\Framework\Event\ObserverInterface;

class CustomerRegisterSuccess implements ObserverInterface
{
    /**
     * @var \Plumrocket\Affiliate\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_session;

    /**
     * @param \Magento\Customer\Model\Session   $customerSession
     * @param \Plumrocket\Affiliate\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Plumrocket\Affiliate\Helper\Data $dataHelper
    ) {
        $this->_dataHelper = $dataHelper;
        $this->_session = $customerSession;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (! $this->_dataHelper->moduleEnabled()) {
            return;
        }

        $this->_session->setPlumrocketAffiliateRegisterSuccess(true);
    }
}

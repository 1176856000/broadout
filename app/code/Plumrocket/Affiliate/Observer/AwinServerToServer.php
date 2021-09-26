<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Observer;

class AwinServerToServer implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $cookieMetadataFactory;

    /**
     * @var \Plumrocket\Affiliate\Helper\Data
     */
    protected $dataHelper;

    /**
     * @param \Magento\Framework\App\RequestInterface
     * @param \Magento\Framework\Stdlib\CookieManagerInterface
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     * @param \Plumrocket\Affiliate\Helper\Data
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Plumrocket\Affiliate\Helper\Data $dataHelper
    ) {
        $this->request = $request;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->dataHelper = $dataHelper;
    }

    /**
     * Capturing and saving cookie awc parameter for Awin affiliate
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $awc = $this->request->getParam('awc');
        if (!$this->dataHelper->moduleEnabled() || !$awc) {
            return;
        }

        $publicCookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata();
        $publicCookieMetadata->setDurationOneYear();
        $publicCookieMetadata->setPath('/');
        $publicCookieMetadata->setHttpOnly(true);
        $publicCookieMetadata->setSecure(true);

        $this->cookieManager->setPublicCookie(
            'awc',
            $awc,
            $publicCookieMetadata
        );
    }
}

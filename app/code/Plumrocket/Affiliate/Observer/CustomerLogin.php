<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Observer;

use Magento\Framework\Encryption\Encryptor;
use Magento\Framework\Event\ObserverInterface;

class CustomerLogin implements ObserverInterface
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
     * @var \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    protected $_phpCookieManager;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\PublicCookieMetadata Factory
     */
    protected $_publicCookieMetadataFactory;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    private $encryptor;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $_session;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Plumrocket\Affiliate\Helper\Data $dataHelper
     * @param \Magento\Framework\Stdlib\Cookie\PhpCookieManager $phpCookieManager
     * @param \Magento\Framework\Stdlib\Cookie\PublicCookieMetadataFactory $publicCookieMetadataFactory
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Plumrocket\Affiliate\Helper\Data $dataHelper,
        \Magento\Framework\Stdlib\Cookie\PhpCookieManager $phpCookieManager,
        \Magento\Framework\Stdlib\Cookie\PublicCookieMetadataFactory $publicCookieMetadataFactory,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor
    ) {
        $this->_dataHelper = $dataHelper;
        $this->_session = $customerSession;
        $this->_phpCookieManager = $phpCookieManager;
        $this->_publicCookieMetadataFactory = $publicCookieMetadataFactory;
        $this->encryptor = $encryptor;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (! $this->_dataHelper->moduleEnabled()) {
            return;
        }

        $this->_setLoginSuccessMarker();
    }

    /**
     * Mark that customer has logged
     */
    protected function _setLoginSuccessMarker()
    {
        $this->_session->setPlumrocketAffiliateLoginSuccess(true);

        if ($customer = $this->_session->getCustomer()) {
            if ($email = $customer->getEmail()) {
                $email = strtolower(trim($email));
                $emailHash = $this->encryptor->hash($email, Encryptor::HASH_VERSION_MD5);
            } else {
                $emailHash = '';
            }

            $this->_phpCookieManager->setPublicCookie(
                'cutomer_email_hash',
                $emailHash,
                $this->_publicCookieMetadataFactory
                    ->create()
                    ->setHttpOnly(false)
                    ->setPath('/')
            );
        }
    }
}

<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Controller\Adminhtml\Affiliate;

use Plumrocket\Affiliate\Api\AffiliateProgramTypeProviderInterface;

class OrderPost extends \Plumrocket\Affiliate\Controller\Adminhtml\Affiliate
{
    /**
     * Order Info Factory
     * @var \Plumrocket\Affiliate\Model\Order\InfoFactory
     */
    protected $infoFactory;

    /**
     * @param \Magento\Backend\App\Action\Context                             $context
     * @param \Plumrocket\Affiliate\Model\AffiliateManager                    $affiliateManager
     * @param \Plumrocket\Affiliate\Api\AffiliateProgramTypeProviderInterface $affiliateProgramTypeProvider
     * @param \Plumrocket\Affiliate\Model\Order\InfoFactory                   $infoFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Plumrocket\Affiliate\Model\AffiliateManager $affiliateManager,
        AffiliateProgramTypeProviderInterface $affiliateProgramTypeProvider,
        \Plumrocket\Affiliate\Model\Order\InfoFactory $infoFactory
    ) {
        parent::__construct($context, $affiliateManager, $affiliateProgramTypeProvider);
        $this->infoFactory = $infoFactory;
    }

    /**
     * Save affiliate data in order
     */
    public function execute()
    {
        $_data = $this->getRequest()->getPost('affiliate');
        $data = [];
        foreach (['order_id', 'affiliate_id', 'comment'] as $key) {
            if (! isset($_data[$key])) {
                $this->messageManager->addError(__('%1 is missing.', $key));
                $this->_redirect($this->_redirect->getRefererUrl());
                return;
            }
            $data[$key] = $_data[$key];
        }

        $info = $this->infoFactory->create()
            ->load($data['order_id'], 'order_id')
            ->addData($data)
            ->save();

        $this->messageManager->addSuccess(__('Data saved successfully.'));
        $this->_redirect($this->_redirect->getRefererUrl());
    }
}

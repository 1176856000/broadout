<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab\Template;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\View\Element\Template;
use Plumrocket\Affiliate\Api\Data\PageTypeInterface;
use Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab\Script;

/**
 * @method Script|null getTab()
 * @method $this setAffiliate($affiliate)
 * @method $this setTab(Script $tab)
 * @since 1.0.0
 */
abstract class AbstractNetwork extends Template
{
    /**
     * @var \Plumrocket\Affiliate\Api\PageTypeProviderInterface
     */
    protected $pageTypeProvider;

    /**
     * @param \Magento\Backend\Block\Template\Context             $context
     * @param \Plumrocket\Affiliate\Api\PageTypeProviderInterface $pageTypeProvider
     * @param array                                               $data
     */
    public function __construct(
        Context $context,
        \Plumrocket\Affiliate\Api\PageTypeProviderInterface $pageTypeProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->pageTypeProvider = $pageTypeProvider;
    }

    /**
     * Get page type by key
     *
     * @param string $key
     * @return \Plumrocket\Affiliate\Api\Data\PageTypeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @deplacated since 3.0.0
     * @see        getPageType()
     */
    public function getIncludeonByKey($key = PageTypeInterface::ANY)
    {
        return $this->getPageType($key);
    }

    /**
     * Get page type by key
     *
     * @param string $key
     * @return \Plumrocket\Affiliate\Api\Data\PageTypeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPageType($key = PageTypeInterface::ANY): PageTypeInterface
    {
        return $this->pageTypeProvider->get($key);
    }
}

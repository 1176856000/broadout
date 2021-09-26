<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Block;

use Magento\Framework\Exception\NoSuchEntityException;
use Plumrocket\Affiliate\Api\AffiliateProgramProviderInterface;
use Plumrocket\Affiliate\Api\Data\AffiliateProgramInterface;
use Plumrocket\Affiliate\Api\PageTypeProviderInterface;
use Plumrocket\Affiliate\Model\Affiliate\AbstractModel;

class Section extends \Magento\Framework\View\Element\Template
{
    public const INCLUDE_ON_PAGES_RKEY = 'affiliate_script_includeon';

    /**
     * @var string
     */
    protected $_section;

    /**
     * @var \Plumrocket\Affiliate\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Plumrocket\Affiliate\Api\AffiliateProgramProviderInterface
     */
    private $affiliateProgramProvider;

    /**
     * @var \Plumrocket\Affiliate\Api\PageTypeProviderInterface
     */
    private $pageTypeProvider;

    /**
     * @param \Plumrocket\Affiliate\Helper\Data                           $dataHelper
     * @param \Magento\Customer\Model\Session                             $customerSession
     * @param \Magento\Framework\View\Element\Template\Context            $context
     * @param \Magento\Framework\Registry                                 $registry
     * @param \Plumrocket\Affiliate\Api\AffiliateProgramProviderInterface $affiliateProgramProvider
     * @param array                                                       $data
     */
    public function __construct(
        \Plumrocket\Affiliate\Helper\Data $dataHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        AffiliateProgramProviderInterface $affiliateProgramProvider,
        PageTypeProviderInterface $pageTypeProvider,
        array $data = []
    ) {
        $this->_dataHelper = $dataHelper;
        $this->_customerSession = $customerSession;
        $this->_registry = $registry;
        $this->affiliateProgramProvider = $affiliateProgramProvider;
        $this->pageTypeProvider = $pageTypeProvider;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        if (! $this->_dataHelper->moduleEnabled()) {
            return '';
        }

        $_section = $this->getSection();

        if ($this->_customerSession->getPlumrocketAffiliateRegisterSuccess()) {
            $this->addPageType('registration_success_pages');
            if ($_section === AbstractModel::SECTION_BODYEND) {
                $this->_customerSession->setPlumrocketAffiliateRegisterSuccess(false);
            }
        }
        if ($this->_customerSession->getPlumrocketAffiliateLoginSuccess()) {
            $this->addPageType('login_success_pages');
            if ($_section === AbstractModel::SECTION_BODYEND) {
                $this->_customerSession->setPlumrocketAffiliateLoginSuccess(false);
            }
        }

        $pageTypes = $this->getPageTypes();
        $html = '';
        foreach ($this->affiliateProgramProvider->get() as $affiliate) {
            if ($affiliate instanceof AffiliateProgramInterface) {
                $html .= $affiliate->getSectionPixelHtml($_section, $pageTypes);
            } else {
                // old logic, will be removed in future
                $getSectionPageTypeId = 'getSection' . ucfirst($_section) . 'IncludeonId';
                try {
                    $pageType = $this->pageTypeProvider->getById($affiliate->$getSectionPageTypeId());
                } catch (NoSuchEntityException $e) {
                    continue;
                }

                if (! $this->isMatchCurrentPageType($pageType->getKey())) {
                    continue;
                }

                $html .= $affiliate->getLibraryHtml($_section);
                $html .= $affiliate->getCodeHtml($_section, $pageTypes);
            }
        }

        return $html;
    }

    /**
     * Set page section
     * @param string $section
     * @return  $this
     */
    public function setSection($section)
    {
        $this->_section = $section;
        return $this;
    }

    /**
     * Get page section
     * @return string
     */
    public function getSection()
    {
        return $this->_section;
    }

    /**
     * Add include on
     *
     * @param string $section
     * @return $this
     *
     * @see addPageType
     * @deprecated since 2.8.0
     */
    public function addIncludeon($section)
    {
        return $this->addPageType($section);
    }

    /**
     * Add page to include on
     *
     * @param string $pageType
     * @return $this
     */
    public function addPageType(string $pageType): self
    {
        $pageTypes = $this->getPageTypes();
        if (! isset($pageTypes[$pageType])) {
            $pageTypes[$pageType] = $pageType;
            $this->setPageTypes($pageTypes);
        }
        return $this;
    }

    /**
     * Set types of current page
     *
     * @param array $pageTypes
     * @return $this
     */
    public function setPageTypes(array $pageTypes)
    {
        $this->_registry->unregister(self::INCLUDE_ON_PAGES_RKEY);
        $this->_registry->register(self::INCLUDE_ON_PAGES_RKEY, $pageTypes);
        return $this;
    }

    /**
     * @param string $pageTypeKey
     * @return bool
     * @since 3.0.0
     */
    public function isMatchCurrentPageType(string $pageTypeKey): bool
    {
        return in_array($pageTypeKey, $this->getPageTypes(), true);
    }

    /**
     * Get include on pages
     *
     * @return array
     */
    public function getPageTypes(): array
    {
        $includeOnPages = $this->_registry->registry(self::INCLUDE_ON_PAGES_RKEY);
        if (!$includeOnPages) {
            $includeOnPages = ['all' => 'all'];
        }
        return $includeOnPages;
    }
}

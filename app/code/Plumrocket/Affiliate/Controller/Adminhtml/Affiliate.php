<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Controller\Adminhtml;

use Plumrocket\Affiliate\Api\AffiliateProgramTypeProviderInterface;

class Affiliate extends \Plumrocket\Base\Controller\Adminhtml\Actions
{
    const ADMIN_RESOURCE = 'Plumrocket_Affiliate::praffiliate';

    /**
     * Form session key
     * @var string
     */
    protected $_formSessionKey  = 'affiliate_form_data';

    /**
     * Model of main class
     * @var string
     */
    protected $_modelClass      = 'Plumrocket\Affiliate\Model\Affiliate';

    /**
     * Active menu
     * @var string
     */
    protected $_activeMenu     = 'Plumrocket_Affiliate::praffiliate';

    /**
     * Object Title
     * @var string
     */
    protected $_objectTitle     = 'Affiliate Program';

    /**
     * Object titles
     * @var string
     */
    protected $_objectTitles    = 'Affiliate Programs';

    /**
     * Status field
     * @var string
     */
    protected $_statusField     = 'status';

    /**
     * Affiliate Manager
     * @var \Plumrocket\Affiliate\Model\AffiliateManager
     */
    protected $affiliateManager;

    /**
     * @var \Plumrocket\Affiliate\Api\AffiliateProgramTypeProviderInterface
     */
    private $affiliateProgramTypeProvider;

    /**
     * @param \Magento\Backend\App\Action\Context                             $context
     * @param \Plumrocket\Affiliate\Model\AffiliateManager                    $affiliateManager
     * @param \Plumrocket\Affiliate\Api\AffiliateProgramTypeProviderInterface $affiliateProgramTypeProvider
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Plumrocket\Affiliate\Model\AffiliateManager $affiliateManager,
        AffiliateProgramTypeProviderInterface $affiliateProgramTypeProvider
    ) {
        parent::__construct($context);
        $this->affiliateManager = $affiliateManager;
        $this->affiliateProgramTypeProvider = $affiliateProgramTypeProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($this->getRequest()->getActionName() === 'delete'
            || $this->getRequest()->getActionName() === 'massStatus'
        ) {
            parent::execute();
            return;
        }

        $typeId = null;
        if ($this->getRequest()->getParam('type_id')) {
            $typeId = $this->getRequest()->getParam('type_id');
        } elseif ($affiliateId = $this->getRequest()->getParam('id')) {
            $affiliate = $this->affiliateManager->createAffiliateByParam($this->_modelClass)->load($affiliateId);
            if ($affiliate->getTypeId()) {
                $typeId = $affiliate->getTypeId();
            }
        } else {
            if ($this->getRequest()->getActionName() === 'edit') {
                $this->_redirect('praffiliate/affiliate/new');
            }
        }

        if ($typeId) {
            $type = $this->affiliateProgramTypeProvider->getById((int) $typeId);
            if (class_exists($type->getModelClassName())) {
                $this->_modelClass = $type->getModelClassName();
            }
        }

        parent::execute();
    }
}

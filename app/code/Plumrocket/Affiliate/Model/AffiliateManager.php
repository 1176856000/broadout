<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Plumrocket\Affiliate\Api\AffiliateProgramTypeProviderInterface;

class AffiliateManager
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Plumrocket\Affiliate\Api\AffiliateProgramTypeProviderInterface
     */
    private $affiliateProgramTypeProvider;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        AffiliateProgramTypeProviderInterface $affiliateProgramTypeProvider
    ) {
        $this->objectManager = $objectManager;
        $this->affiliateProgramTypeProvider = $affiliateProgramTypeProvider;
    }

    /**
     * Create affiliate model
     * @param  \Plumrocket\Affiliate\Model\Affiliate $model
     * @param  string $typeId
     * @return \Plumrocket\Affiliate\Model\Affiliate
     */
    public function createAffiliate($model, $typeId = null)
    {
        $typeId = $typeId ?: $model->getTypeId();
        try {
            $type = $this->affiliateProgramTypeProvider->getById((int) $typeId);
            $typeModel = $this->createAffiliateByParam($type->getModelClassName());
            if ($typeModel) {
                return $typeModel->simulateLoad($model);
            }
            return $model;
        } catch (NoSuchEntityException $e) {
            return $model;
        }
    }

    /**
     * Create affiliate model by model class or type
     *
     * @param null | string                                                       $modelClass
     * @param null | \Plumrocket\Affiliate\Api\Data\AffiliateProgramTypeInterface $type
     * @return \Plumrocket\Affiliate\Model\Affiliate
     */
    public function createAffiliateByParam($modelClass = null, $type = null)
    {
        $modelClassName = $modelClass ?: 'Plumrocket\Affiliate\Model\Affiliate\\' . ucfirst($type->getKey());
        return $this->objectManager->create($modelClassName);
    }

    /**
     * Get affiliate template block
     * @param  \Plumrocket\Affiliate\Api\Data\AffiliateProgramTypeInterface $type
     * @return \Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab\Template\AbstractNetwork
     */
    public function getAffiliateTemplateBlock($type)
    {
        return $this->objectManager->get(
            'Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab\Template\\' . ucfirst($type->getKey())
        );
    }
}

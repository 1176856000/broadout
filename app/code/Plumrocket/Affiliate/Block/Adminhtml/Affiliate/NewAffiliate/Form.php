<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Block\Adminhtml\Affiliate\NewAffiliate;

use Magento\Framework\Serialize\SerializerInterface;
use Plumrocket\Affiliate\Api\AffiliateProgramTypeProviderInterface;
use Plumrocket\Affiliate\Model\ResourceModel\Type\CollectionFactory;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @var \Plumrocket\Affiliate\Api\AffiliateProgramTypeProviderInterface
     */
    private $affiliateProgramTypeProvider;

    /**
     * @param \Magento\Backend\Block\Template\Context                         $context
     * @param \Magento\Framework\Registry                                     $registry
     * @param \Magento\Framework\Data\FormFactory                             $formFactory
     * @param \Magento\Framework\Serialize\SerializerInterface                $serializer
     * @param \Plumrocket\Affiliate\Api\AffiliateProgramTypeProviderInterface $affiliateProgramTypeProvider
     * @param array                                                           $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        SerializerInterface $serializer,
        AffiliateProgramTypeProviderInterface $affiliateProgramTypeProvider,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->serializer = $serializer;
        $this->affiliateProgramTypeProvider = $affiliateProgramTypeProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate()
    {
        return parent::getTemplate() ?: 'Plumrocket_Affiliate::affiliate.phtml';
    }

    /**
     * @return \Plumrocket\Affiliate\Api\Data\AffiliateProgramTypeInterface[]
     */
    public function getAliveTypes(): array
    {
        return $this->affiliateProgramTypeProvider->getAliveTypes();
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'new_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * @return string
     */
    public function getSearchJsonConfig(): string
    {
        $config = [];
        foreach ($this->getAliveTypes() as $type) {
            $config[] = [
                'id' => $type->getId(),
                'name' => $type->getName(),
                'metaKeywords' => $type->getMetaKeywords(),
            ];
        }
        return $this->serializer->serialize($config);
    }
}

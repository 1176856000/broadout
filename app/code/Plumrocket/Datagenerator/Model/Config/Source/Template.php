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
 * @package     Plumrocket_Datagenerator
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Datagenerator\Model\Config\Source;

class Template implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Plumrocket\Affiliate\Model\TypeFactory
     */
    protected $_templateFactory;

    /**
     * @param \Plumrocket\Affiliate\Model\TypeFactory $typeFactory
     */
    public function __construct(
        \Plumrocket\Datagenerator\Model\TemplateFactory $templateFactory
    ) {
        $this->_templateFactory = $templateFactory;
    }

    /**
     * Retrieve status options array.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $collection = $this->_templateFactory->create()->getCollection();
        $collection->addFieldToFilter('type_entity', \Plumrocket\Datagenerator\Model\Template::ENTITY_TYPE_TEMPLATE)
            ->setOrder('name', 'ASC');

        $result = [ 0 => ['value' => 0, 'label' => __('Blank document')]];

        foreach ($collection as $item) {
            $result[] = ['value' => $item->getId(), 'label' => $item->getName()];
        }

        return $result;
    }
}

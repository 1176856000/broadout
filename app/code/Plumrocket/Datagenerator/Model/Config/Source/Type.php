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

use Plumrocket\Datagenerator\Model\Template as ModelTemplate;

class Type implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Plumrocket\Datagenerator\Model\TemplateFactory
     */
    protected $_templateFactory;

    /**
     * @param \Plumrocket\Datagenerator\Model\TemplateFactory $templateFactory
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
        return [
            ['value' => ModelTemplate::ENTITY_FEED_TYPE_PRODUCT, 'label' => __('Products')],
            ['value' => ModelTemplate::ENTITY_FEED_TYPE_CATEGORY, 'label' => __('Categories')]
        ];
    }
}

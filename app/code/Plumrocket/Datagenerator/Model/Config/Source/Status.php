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

class Status implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Template model
     * @var \Plumrocket\Affiliate\Model\TypeFactory
     */
    protected $_template;

    /**
     * Construct
     * @param \Plumrocket\Datagenerator\Model\Template $template
     */
    public function __construct(\Plumrocket\Datagenerator\Model\Template $template)
    {
        $this->_template = $template;
    }

    /**
     * Retrieve status options array.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_template->getAvailableStatuses();
    }
}

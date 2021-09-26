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
 * @package     Plumrocket_SizeChart
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Grid\Column;

use Plumrocket\Affiliate\Api\AffiliateProgramTypeProviderInterface;

class Network extends \Magento\Backend\Block\Widget\Grid\Column
{
    /**
     * @var \Plumrocket\Affiliate\Api\AffiliateProgramTypeProviderInterface
     */
    private $affiliateProgramTypeProvider;

    /**
     * @param \Magento\Backend\Block\Template\Context                         $context
     * @param \Plumrocket\Affiliate\Api\AffiliateProgramTypeProviderInterface $affiliateProgramTypeProvider
     * @param array                                                           $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        AffiliateProgramTypeProviderInterface $affiliateProgramTypeProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->affiliateProgramTypeProvider = $affiliateProgramTypeProvider;
    }

    /**
     * Add to column decorated status
     *
     * @return array
     */
    public function getFrameCallback()
    {
        return [$this, 'decorateNetwork'];
    }

    /**
     * Decorate status column values
     *
     * @param string $value
     * @param  \Magento\Framework\Model\AbstractModel $row
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @param bool $isExport
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function decorateNetwork($value, $row, $column, $isExport)
    {
        $type =  $this->affiliateProgramTypeProvider->getById((int) $row->getTypeId());

        if ($logo = $type->getLogo()) {
            $cell = '<div align="center"><img src="' . $this->getViewFileUrl($logo) . '" /></div>';
        } else {
            $cell = '<div align="center"><strong>' . strtoupper($type->getName()) . '</strong></div>';
        }
        return $cell;
    }
}

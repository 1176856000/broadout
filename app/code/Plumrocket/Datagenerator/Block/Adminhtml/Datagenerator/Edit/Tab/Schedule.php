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
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Datagenerator\Block\Adminhtml\Datagenerator\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Plumrocket\Datagenerator\Model\Config\Source\ScheduleDays;
use Plumrocket\Datagenerator\Model\Config\Source\ScheduleTime;

class Schedule extends Generic implements TabInterface
{
    /**
     * @var ScheduleDays
     */
    private $scheduleDaysOptions;

    /**
     * @var ScheduleTime
     */
    private $scheduleTimeOptions;

    /**
     * Schedule constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param ScheduleDays $scheduleDaysOptions
     * @param ScheduleTime $scheduleTimeOptions
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        ScheduleDays $scheduleDaysOptions,
        ScheduleTime $scheduleTimeOptions,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);

        $this->scheduleDaysOptions = $scheduleDaysOptions;
        $this->scheduleTimeOptions = $scheduleTimeOptions;
    }

    /**
     * @inheritDoc
     */
    public function getTabLabel()
    {
        return __('Schedule Automatic Updates');
    }

    /**
     * @inheritDoc
     */
    public function getTabTitle()
    {
        return __('Schedule Automatic Updates');
    }

    /**
     * @inheritDoc
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_model');
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset(
            'schedule_fieldset',
            ['legend' => __('Schedule Automatic Updates')]
        );

        $fieldset->addField(
            'scheduled_days',
            'multiselect',
            [
                'name' => 'scheduled_days',
                'label' => __('Days of the Week'),
                'title' => __('Days of the Week'),
                'values' => $this->scheduleDaysOptions->toOptionArray(),
                'note' => __('Select days when this data feed should be recreated'),
            ]
        );

        $fieldset->addField(
            'scheduled_time',
            'multiselect',
            [
                'name' => 'scheduled_time',
                'label' => __('Time of the Day'),
                'title' => __('Time of the Day'),
                'values' => $this->scheduleTimeOptions->toOptionArray(),
                'note' => __('Select time when this data feed should be recreated'),
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}

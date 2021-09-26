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

namespace Plumrocket\Datagenerator\Cron;

class FormCache
{
    /**
     * Mod
     * @var string
     */
    protected $mod = 'url';

    /**
     * Tempalte factory
     * @var \Plumrocket\Datagenerator\Model\TemplateFactory
     */
    protected $_templateFactory;

    /**
     * Render model
     * @var \Plumrocket\Datagenerator\Model\Render
     */
    protected $_renderModel;

    /**
     * Store manager
     * @var \Magento\Store\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * @var \Plumrocket\Datagenerator\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Magento\Framework\Url
     */
    protected $url;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Plumrocket\Datagenerator\Model\Config\Source\ScheduleTime
     */
    protected $scheduleTime;

    /**
     * @var \Plumrocket\Datagenerator\Model\Config\Source\ScheduleDays
     */
    protected $scheduleDays;

    /**
     * FormCache constructor.
     *
     * @param \Plumrocket\Datagenerator\Model\Render $renderModel
     * @param \Plumrocket\Datagenerator\Helper\Data $dataHelper
     * @param \Magento\Store\Model\StoreManager $storeManager
     * @param \Plumrocket\Datagenerator\Model\TemplateFactory $templateFactory
     * @param \Magento\Framework\Url $url
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Plumrocket\Datagenerator\Model\Config\Source\ScheduleTime $scheduleTime
     * @param \Plumrocket\Datagenerator\Model\Config\Source\ScheduleDays $scheduleDays
     */
    public function __construct(
        \Plumrocket\Datagenerator\Model\Render $renderModel,
        \Plumrocket\Datagenerator\Helper\Data $dataHelper,
        \Magento\Store\Model\StoreManager $storeManager,
        \Plumrocket\Datagenerator\Model\TemplateFactory $templateFactory,
        \Magento\Framework\Url $url,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Plumrocket\Datagenerator\Model\Config\Source\ScheduleTime $scheduleTime,
        \Plumrocket\Datagenerator\Model\Config\Source\ScheduleDays $scheduleDays
    ) {
        $this->_renderModel = $renderModel;
        $this->_dataHelper = $dataHelper;
        $this->_templateFactory = $templateFactory;
        $this->_storeManager = $storeManager;
        $this->url = $url;
        $this->timezone = $timezone;
        $this->scheduleTime = $scheduleTime;
        $this->scheduleDays = $scheduleDays;
    }

    /**
     * Caching all data feeds
     * @return void
     */
    public function execute() // phpcs:disable
    {
        if ($this->_dataHelper->moduleEnabled()) {
            $templates = $this->_templateFactory->create()
                ->getCollection()
                ->addFieldToFilter('enabled', \Plumrocket\Datagenerator\Model\Template::STATUS_ENABLED)
                ->addFieldToFilter('type_entity', \Plumrocket\Datagenerator\Model\Template::ENTITY_TYPE_FEED);

            /** @var \Plumrocket\Datagenerator\Model\Template $template */
            foreach ($templates as $template) {
                $render = $this->_renderModel->setTemplate($template);
                $process = false;

                if ($render->isRunning()) {
                    break;
                }

                $scheduledDays = array_intersect_key(
                    $this->scheduleDays->toArray(),
                    $template->getScheduledDays()
                );
                $scheduledTime = array_intersect_key(
                    $this->scheduleTime->toArray(),
                    array_flip($template->getScheduledTime())
                );
                $currentDay = $this->timezone->date()->format('l');
                $currentTime = $this->timezone->date()->getTimestamp();

                foreach ($scheduledDays as $day) {
                    if ($currentDay === (string) $day) {
                        foreach ($scheduledTime as $time) {
                            $time = strtotime($time);
                            if (($currentTime > $time && $currentTime < $time + 2 * 60)
                                || ($currentTime < $time && $currentTime > $time - 2 * 60)
                            ) {
                                $process = true;
                                break 2;
                            }
                        }
                    }
                }

                if (! $process) {
                    continue;
                }

                if ($render->getTextCache()) {
                    continue;
                } else {
                    set_time_limit(0);

                    if ($this->mod !== 'url') {
                        $this->_storeManager->setCurrentStore($template->getStoreId());
                        $render->getText();
                    } else {
                        $ch = curl_init();
                        $url = $this->url->getUrl(
                            'datagenerator/index/index',
                            ['address' => $template->getUrlKey(), 'no_output' => 'yes']
                        );
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_exec($ch);
                        curl_close($ch);
                    }
                    break;
                }
            }
        }
    } // phpcs:enable
}

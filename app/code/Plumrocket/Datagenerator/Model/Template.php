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
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Datagenerator\Model;

class Template extends \Magento\Rule\Model\AbstractModel
{
    const ENTITY_TYPE_FEED = 1;
    const ENTITY_TYPE_TEMPLATE = 0;

    const ENTITY_FEED_TYPE_PRODUCT = 1;
    const ENTITY_FEED_TYPE_CATEGORY = 0;

    const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;

    /**
     * Extension of file
     * @var string
     */
    protected $ext;

    /**
     * For getActionsInstance
     * @var \Magento\Rule\Model\Action\CollectionFactory
     */
    protected $actionCollectionFactory;

    /**
     * For getConditionsInstance
     * @var \Plumrocket\Datagenerator\Model\Datagenerator\Condition\CombineFactory
     */
    protected $combineFactory;

    /**
     * @var Template\Information
     */
    private $templateInformation;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Serialize
     */
    private $phpSerializer;

    /**
     * Constructor
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param Template\Condition\CombineFactory $combineFactory
     * @param \Magento\Rule\Model\Action\CollectionFactory $actionCollectionFactory
     * @param Template\Information $templateInformation
     * @param \Magento\Framework\Serialize\SerializerInterface $phpSerializer,
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Plumrocket\Datagenerator\Model\Template\Condition\CombineFactory $combineFactory,
        \Magento\Rule\Model\Action\CollectionFactory $actionCollectionFactory,
        \Plumrocket\Datagenerator\Model\Template\Information $templateInformation,
        \Magento\Framework\Serialize\SerializerInterface $phpSerializer,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->combineFactory = $combineFactory;
        $this->actionCollectionFactory = $actionCollectionFactory;
        $this->phpSerializer = $phpSerializer;
        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);
        $this->templateInformation = $templateInformation;
        $this->phpSerializer = $phpSerializer;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Template::class);
    }

    /**
     * Return whether it product feed or category
     *
     * @return int
     */
    public function getType(): int
    {
        return (int) $this->_getData('type_feed');
    }

    /**
     * Retrieve max count on items in feed
     *
     * @return int
     */
    public function getMaxItemsCount(): int
    {
        return (int) $this->_getData('count');
    }

    /**
     * @return string
     */
    public function getHeaderTemplate(): string
    {
        return (string) $this->_getData('code_header');
    }

    /**
     * @return string
     */
    public function getItemTemplate(): string
    {
        return (string) $this->_getData('code_item');
    }

    /**
     * @return string
     */
    public function getFooterTemplate(): string
    {
        return (string) $this->_getData('code_footer');
    }

    /**
     * @return array
     */
    public static function getAvailableStatuses()
    {
        return [self::STATUS_DISABLED => __('Disabled'), self::STATUS_ENABLED => __('Enabled')];
    }

    /**
     * Set default conditions
     *
     * @param array $conditions
     * @return self
     */
    public function setDefaultConditions(array $conditions)
    {
        if (!$this->getId()) {
            if (isset($this->serializer)) {
                $this->setData('conditions_serialized', $this->serializer->serialize($conditions));
            } else {
                $this->setData('conditions_serialized', $this->phpSerializer->serialize($conditions));
            }
        }

        return $this;
    }

    /**
     * Retrieve Serialized Conditions
     * Deprecated and Need to be removed in future releases
     * added for compatibility with 2.1.x and 2.2.x
     * @return string
     */
    public function getConditionsSerialized()
    {
        $value = $this->getData('conditions_serialized');

        if (isset($this->serializer)) {
            try {
                $uv = $this->phpSerializer->unserialize($value);
                $value = $this->serializer->serialize($uv);
            } catch (\Exception $e) { // phpcs:ignore

            }
        }

        return $value;
    }

    /**
     * Retrieve Serialized Actions
     * Deprecated and Need to be removed in future releases
     * added for compatibility with 2.1.x and 2.2.x
     * @return string
     */
    public function getActionsSerialized()
    {
        $value = $this->getData('actions_serialized');

        if (isset($this->serializer)) {
            try {
                $uv = $this->phpSerializer->unserialize($value);
                $value = $this->serializer->serialize($uv);
            } catch (\Exception $e) { // phpcs:ignore

            }
        }

        return $value;
    }

    /**
     * Retrieve extension
     * @return string
     */
    public function getExt()
    {
        if ($this->ext == null) {
            $ext = trim(strrchr($this->getData('url_key'), '.'));
            if ($ext == '.csv') {
                $ext = 'csv';
            } elseif (($ext == '.xml') || ($ext == '.rss') || ($ext == '.atom')) {
                $ext = 'xml';
            } else {
                $ext = substr($ext, 1);
            }
            $this->ext = $ext;
        }
        return $this->ext;
    }

    /**
     * Clean cache
     * @return $this
     */
    public function cleanCache()
    {
        $this->_cacheManager->remove('datafeed_' . $this->getId());
        return $this;
    }

    public function getConditionsInstance()
    {
        return $this->combineFactory->create();
    }

    public function getActionsInstance()
    {
        return $this->actionCollectionFactory->create();
    }

    /**
     * @return array
     */
    public function getScheduledDays(): array
    {
        return (array) $this->getData('scheduled_days');
    }

    /**
     * @return array
     */
    public function getScheduledTime(): array
    {
        return (array) $this->getData('scheduled_time');
    }

    /**
     * @return array
     */
    public function getFtpData(): array
    {
        return [
            'host' => $this->getHost(),
            'port' => $this->getPort(),
            'user' => $this->getUsername(),
            'username' => $this->getUsername(),
            'password' => $this->getPassword(),
            'path' => $this->getPath(),
            'passive' => (bool) $this->getPassive(),
        ];
    }

    /**
     * @return int
     */
    public function getGoogleShoppingId()
    {
        return $this->templateInformation->getGoogleShoppingTemplateId();
    }
}

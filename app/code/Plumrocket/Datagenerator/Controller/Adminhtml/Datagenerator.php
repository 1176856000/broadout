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

namespace Plumrocket\Datagenerator\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Filesystem\Io\Ftp;
use Magento\Framework\Filesystem\Io\Sftp;
use Magento\Store\Model\StoreManagerInterface;
use Plumrocket\Datagenerator\Model\Config\Source\TransferProtocol;
use Plumrocket\Datagenerator\Model\TemplateFactory;

class Datagenerator extends \Plumrocket\Base\Controller\Adminhtml\Actions
{
    const ADMIN_RESOURCE = 'Plumrocket_Datagenerator::prdatagenerator';

    /**
     * {@inheritdoc}
     */
    protected $_formSessionKey  = 'prdatagenerator_form_data';

    /**
     * {@inheritdoc}
     */
    protected $_modelClass      = \Plumrocket\Datagenerator\Model\Template::class;

    /**
     * {@inheritdoc}
     */
    protected $_activeMenu     = 'Plumrocket_Datagenerator::prdatagenerator';

    /**
     * {@inheritdoc}
     */
    protected $_objectTitle     = 'Data Feed';

    /**
     * {@inheritdoc}
     */
    protected $_objectTitles    = 'Data Feeds';

    /**
     * {@inheritdoc}
     */
    protected $_statusField     = 'enabled';

    /**
     * {@inheritdoc}
     */
    protected $_idKey           = 'id';

    /**
     * @var TemplateFactory
     */
    protected $templateFactory;

    /**
     * Datagenerator constructor.
     *
     * @param Context $context
     * @param TemplateFactory $templateFactory
     */
    public function __construct(
        Context $context,
        TemplateFactory $templateFactory
    ) {
        $this->templateFactory = $templateFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeSave($model, $request)
    {
        $data = $model->getData();
        $data = $this->_filterPostData($data);
        $model->setData($data);
        $model->loadPost($data);
        $model->cleanCache();

        return $this;
    }

    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array $data
     * @return array
     */
    protected function _filterPostData($data)
    {
        $entityId = isset($data[$this->_idKey]) ? (int)$data[$this->_idKey] : 0;

        while (($item = $this->_getTemplateByUrlKey($data['url_key'], $entityId))
            && ($item->getId() > 0)
        ) {
            if ($ext = strrchr($data['url_key'], '.')) {
                $data['url_key'] = str_replace($ext, '_re' . $ext, $data['url_key']);
            } else {
                $data['url_key'] .= '_re';
            }
        }

        if (!empty($data['rule']['conditions'])) {
            $data['conditions'] = $data['rule']['conditions'];
        }

        $data['type_entity'] = \Plumrocket\Datagenerator\Model\Template::ENTITY_TYPE_FEED;

        return $data;
    }

    /**
     * @return void
     */
    public function _editAction()
    {
        $model = $this->_getModel();
        $model->setDefaultConditions(\Plumrocket\Datagenerator\Helper\Data::DEFAULT_CONDITION);
        parent::_editAction();
    }

    /**
     * @return \Magento\Framework\Filesystem\Io\IoInterface
     * @throws NotFoundException
     */
    protected function getTransportHandler($protocol)
    {
        switch ($protocol) {
            case TransferProtocol::FTP:
                return $this->_objectManager->create(Ftp::class);
            case TransferProtocol::SFTP:
                return $this->_objectManager->create(Sftp::class);
            default:
                throw new NotFoundException(
                    __('Protocol is not supported')
                );
        }
    }

    /**
     * Load template by url key
     * @param  string $url_key
     * @param  int $entityId
     * @return \Plumrocket\Datagenerator\Model\Template
     */
    private function _getTemplateByUrlKey($url_key, $entityId)
    {
        return $this->templateFactory->create()
            ->getCollection()
            ->addFieldToFilter('type_entity', \Plumrocket\Datagenerator\Model\Template::ENTITY_TYPE_FEED)
            ->addFieldToFilter('url_key', $url_key)
            ->addFieldToFilter($this->_idKey, ['neq' => $entityId])
            ->getFirstItem();
    }
}

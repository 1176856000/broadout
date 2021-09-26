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

use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Plumrocket\Datagenerator\Model\Config\Source\TransferProtocol;

class Upload extends Generic implements TabInterface
{
    /**
     * @var TransferProtocol
     */
    protected $protocolOptions;

    /**
     * @var Yesno
     */
    private $yesNoOptions;

    /**
     * Upload constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Yesno $yesNoOptions
     * @param TransferProtocol $protocolOptions
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Yesno $yesNoOptions,
        TransferProtocol $protocolOptions,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->yesNoOptions = $yesNoOptions;
        $this->protocolOptions = $protocolOptions;
    }

    /**
     * @inheritDoc
     */
    public function getTabLabel()
    {
        return __('FTP Upload');
    }

    /**
     * @inheritDoc
     */
    public function getTabTitle()
    {
        return __('FTP Upload');
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
     * @inheritDoc
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_model');
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset(
            'upload_fieldset',
            ['legend' => __('FTP Upload')]
        );

        $enableField = $fieldset->addField(
            'ftp_enabled',
            'select',
            [
                'name' => 'ftp_enabled',
                'label' => __('Enabled'),
                'title' => __('Enabled'),
                'values' => $this->yesNoOptions->toOptionArray(),
                'note' => __('If enabled, data feed will be automatically uploaded to the FTP server after each manual 
                    or scheduled data feed rebuild.'),
            ]
        );

        $protocolField = $fieldset->addField(
            'protocol',
            'select',
            [
                'name' => 'protocol',
                'label' => __('Protocol'),
                'title' => __('Protocol'),
                'values' => $this->protocolOptions->toOptionArray(),
            ]
        );

        $hostField = $fieldset->addField(
            'host',
            'text',
            [
                'name' => 'host',
                'label' => __('Host'),
                'title' => __('Host'),
                'required' => true
            ]
        );

        $portField = $fieldset->addField(
            'port',
            'text',
            [
                'name' => 'port',
                'label' => __('Port'),
                'title' => __('Port'),
                'required' => true
            ]
        );

        $userField = $fieldset->addField(
            'username',
            'text',
            [
                'name' => 'username',
                'label' => __('User'),
                'title' => __('User'),
                'required' => true
            ]
        );

        $passwordField = $fieldset->addField(
            'password',
            'obscure',
            [
                'name' => 'password',
                'label' => __('Password'),
                'title' => __('Password'),
                'required' => true
            ]
        );

        $transferModeField = $fieldset->addField(
            'passive',
            'select',
            [
                'name' => 'passive',
                'label' => __('Passive Mode'),
                'title' => __('Passive Mode'),
                'default' => 0,
                'values' => $this->yesNoOptions->toOptionArray(),
            ]
        );

        $pathField = $fieldset->addField(
            'path',
            'text',
            [
                'name' => 'path',
                'label' => __('Path'),
                'title' => __('Path'),
            ]
        );

        $testConnectionButton = $fieldset->addField(
            'test_connection',
            'button',
            [
                'name' => 'test_connection',
                'class' => 'action-primary',
                'title' => __('Test Connection'),
                'value' => __('Test Connection'),
                'onclick' => "
                    require(['Plumrocket_Datagenerator/js/form/test-connection'], function (testConnection) {
                        testConnection.connect('"
                        . $this->getUrl('prdatagenerator/datagenerator/testConnection', ['id' => $model->getId()])
                        . "');
                    });",
                'note' => '<div class="message-container"></div>',
            ]
        );

        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(\Magento\Backend\Block\Widget\Form\Element\Dependence::class)
                ->addFieldMap($enableField->getHtmlId(), $enableField->getName())
                ->addFieldMap($hostField->getHtmlId(), $hostField->getName())
                ->addFieldDependence(
                    $hostField->getName(),
                    $enableField->getName(),
                    1
                )
                ->addFieldMap($portField->getHtmlId(), $portField->getName())
                ->addFieldDependence(
                    $portField->getName(),
                    $enableField->getName(),
                    1
                )
                ->addFieldMap($protocolField->getHtmlId(), $protocolField->getName())
                ->addFieldDependence(
                    $protocolField->getName(),
                    $enableField->getName(),
                    1
                )
                ->addFieldMap($userField->getHtmlId(), $userField->getName())
                ->addFieldDependence(
                    $userField->getName(),
                    $enableField->getName(),
                    1
                )
                ->addFieldMap($passwordField->getHtmlId(), $passwordField->getName())
                ->addFieldDependence(
                    $passwordField->getName(),
                    $enableField->getName(),
                    1
                )
                ->addFieldMap($pathField->getHtmlId(), $pathField->getName())
                ->addFieldDependence(
                    $pathField->getName(),
                    $enableField->getName(),
                    1
                )
                ->addFieldMap($transferModeField->getHtmlId(), $transferModeField->getName())
                ->addFieldDependence(
                    $transferModeField->getName(),
                    $enableField->getName(),
                    1
                )
                ->addFieldMap($testConnectionButton->getHtmlId(), $testConnectionButton->getName())
                ->addFieldDependence(
                    $testConnectionButton->getName(),
                    $enableField->getName(),
                    1
                )
                ->addFieldDependence(
                    $transferModeField->getName(),
                    $protocolField->getName(),
                    1
                )
        );

        $form->setValues($model->getData());
        $this->setForm($form);
        $testConnectionButton->setValue(__('Test Connection'));

        return parent::_prepareForm();
    }
}

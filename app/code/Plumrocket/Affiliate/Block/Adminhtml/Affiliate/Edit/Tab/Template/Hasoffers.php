<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab\Template;

class Hasoffers extends AbstractNetwork
{
    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    private $sourceYesno;

    /**
     * @param \Magento\Backend\Block\Template\Context             $context
     * @param \Plumrocket\Affiliate\Api\PageTypeProviderInterface $pageTypeProvider
     * @param \Magento\Config\Model\Config\Source\Yesno           $sourceYesno
     * @param array                                               $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Plumrocket\Affiliate\Api\PageTypeProviderInterface $pageTypeProvider,
        \Magento\Config\Model\Config\Source\Yesno $sourceYesno,
        array $data = []
    ) {
        parent::__construct($context, $pageTypeProvider, $data);
        $this->sourceYesno = $sourceYesno;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareForm($form)
    {
        $affiliate  = $this->getAffiliate();

        $additionalData = $affiliate->getAdditionalDataArray();

        $fieldset = $form->addFieldset(
            'postback',
            [
                'legend' => __('Postback Script - Pay Per Sale (PPS) or Cost Per Sale (CPS) Program'),
                'class' => 'fieldset-wide'
            ]
        );

        $values = ['-- None --'];
        foreach ($this->pageTypeProvider->getList() as $item) {
            $values[$item->getId()] = $item->getName();
        }

        $fieldset->addField(
            'additional_data_delivery_cost_inclusive',
            'select',
            [
                'name'      => 'additional_data[postback_params][delivery_cost_inclusive]',
                'label'     => __('Include Delivery Cost'),
                'value'     => $additionalData['postback_params']['delivery_cost_inclusive']['value'],
                'values'    => $this->sourceYesno->toOptionArray(),
                'note'      => __('Include delivery cost in total amount.'),
            ]
        );

        $fieldset->addField(
            'additional_data_taxes_inclusive',
            'select',
            [
                'name'      => 'additional_data[postback_params][taxes_inclusive]',
                'label'     => __('Include Taxes'),
                'value'     => $additionalData['postback_params']['taxes_inclusive']['value'],
                'values'    => $this->sourceYesno->toOptionArray(),
                'note'      => __('Include taxes in total amount.'),
            ]
        );

        $fieldset->addField(
            'section_bodyend_includeon_id',
            'select',
            [
                'name'      => 'section_bodyend_includeon_id',
                'label'     => __('Execute Postback On'),
                'class'     => 'required-entry',
                'required'  => true,
                'value'     => $affiliate->getSectionBodyendIncludeonId(),
                'values'    => $values,

            ]
        );

        $fieldset->addField(
            'section_bodyend_code',
            'textarea',
            [
                'name'      => 'section_bodyend_code',
                'label'     => __('Postback Script'),
                'class'     => 'required-entry',
                'required'  => true,
                'value'     => $affiliate->getSectionBodyendCode(),
                'after_element_html' => '
                    Example 1: <span style="display:inline-block; padding:3px; border: 1px dotted grey; margin-bottom: 3px; width: 80%;">'.
                        htmlspecialchars('<iframe src="http://demo.go2jump.org/aff_goal?a=l&goal_id={goal_id}" scrolling="no" frameborder="0" width="1" height="1"></iframe>').
                    '</span><br/>
                    Example 2: <span style="display:inline-block; padding:3px; border: 1px dotted grey; margin-bottom: 3px; width: 80%;">'.
                        htmlspecialchars('<img src="http://demo.go2jump.org/aff_i?offer_id={offer_id}&aff_id={aff_id}" width="1" height="1" />').
                    '</span><br/>
                    Example 3: <span style="display:inline-block; padding:3px; border: 1px dotted grey; margin-bottom: 3px; width: 80%;">
                        http://tracking.your-domain.com/aff_goal?a=lsr&goal_id={goal_id}&amount={amount}&transaction_id={transaction_id}
                    </span>',

            ]
        );

        $fieldset = $form->addFieldset('postback_params', ['legend' => __('Postback URL Parameters'), 'class' => 'fieldset-wide']);

        $fieldset->addField(
            'postback_param_',
            'note',
            [
                'name'      => 'postback_param[]',
                'label'     => 'Parameter',
                'style'     => '',
                'text'      => '<span style="width: 200px !important; margin-right: 19px; display: inline-block; font-weight:700; font-size:14px;">'.__('Affiliate URL Variable').'</span>',
                'after_element_html' => '<span style="font-weight:700; font-size:14px;">Description</span>',
            ]
        );

        if (isset($additionalData['postback_params'])) {
            unset($additionalData['postback_params']['delivery_cost_inclusive']);
            unset($additionalData['postback_params']['taxes_inclusive']);

            foreach ($additionalData['postback_params'] as $key => $param) {

                if ($param['is_editable']) {
                    $fieldset->addField(
                        'postback_param_'.$key,
                        'text',
                        [
                            'name'      => 'additional_data[postback_params]['.$param['key'].']',
                            'label'     => '{'.$param['key'].'}',
                            'style'     => 'width: 200px !important; margin-right: 11px;',
                            'value'     => $param['value'],
                            'after_element_html' => '<span style="color:#eb5e00">'.__($param['description']).'</span>',
                        ]
                    );
                } else {
                    $fieldset->addField(
                        'postback_param_'.$key,
                        'note',
                        [
                            'label'     => '{'.$param['key'].'}',
                            'style'     => '',
                            'text'      => '<span style="width: 200px !important; margin-right: 19px; display: inline-block;">'.__('Auto-generated Value').'</span>',
                            'after_element_html' => '<span style="color:#eb5e00">'.__($param['description']).'</span>',
                        ]
                    );
                }
            }
        }

        return $this;
    }
}

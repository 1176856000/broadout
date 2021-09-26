<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab\Template;

class Tradedoubler extends AbstractNetwork
{
    /**
     * @var \Plumrocket\Affiliate\Model\Values\TradedoublerPixel
     */
    protected $tradedoublerPixel;

    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $sourceYesno;

    /**
     * @param \Magento\Backend\Block\Template\Context              $context
     * @param \Plumrocket\Affiliate\Api\PageTypeProviderInterface  $pageTypeProvider
     * @param \Plumrocket\Affiliate\Model\Values\TradedoublerPixel $tradedoublerPixel
     * @param \Magento\Config\Model\Config\Source\Yesno            $sourceYesno
     * @param array                                                $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Plumrocket\Affiliate\Api\PageTypeProviderInterface $pageTypeProvider,
        \Plumrocket\Affiliate\Model\Values\TradedoublerPixel $tradedoublerPixel,
        \Magento\Config\Model\Config\Source\Yesno $sourceYesno,
        array $data = []
    ) {
        parent::__construct($context, $pageTypeProvider, $data);
        $this->tradedoublerPixel = $tradedoublerPixel;
        $this->sourceYesno = $sourceYesno;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareForm($form)
    {
        $affiliate  = $this->getAffiliate();

        $fieldset = $form->addFieldset(
            'section_bodybegin',
            ['legend' => __('Pixels Settings'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField(
            'additional_data_organization_id',
            'text',
            [
                'name'      => 'additional_data[organization_id]',
                'label'     => 'Organization ID',
                'required'  => true,
                'class'     => 'validate-digits',
                'value'     => $affiliate->getOrganizationId(),
                'note'      => 'Your organization ID as provided by Tradedoubler.',
            ]
        );

        $fieldset->addField(
            'additional_data_checksum_code',
            'text',
            [
                'name'      => 'additional_data[checksum_code]',
                'label'     => 'Checksum Code',
                'class'     => 'validate-alphanum',
                'value'     => $affiliate->getChecksumCode(),
                'note'      => 'This is part of Tradedoubler\'s fraud protection measures and we highly recommend ' .
                    'you to implement it. Your Tradedoubler contact will explain how it should be configured.',
            ]
        );

        $fieldset->addField(
            'additional_data_cps_enable',
            'select',
            [
                'name'      => 'additional_data[cps_enable]',
                'label'     => __('Pay-Per-Sale Program'),
                'value'     => $affiliate->getCpsEnable(),
                'values'    => $this->tradedoublerPixel->toOptionArray(),
                'note'      => 'Pay Per Sale (PPS) or Cost Per Sale (CPS). Merchant site pays a percentage of the sale when the affiliate sends them a customer who purchases something. Merchant only pays its affiliates when it gets a desired result.',
                'onchange'  => 'document.getElementById(\'affiliate_additional_data_sale_event_id\').parentNode.parentNode.parentNode.style.display = (this.value > 0? null : \'none\');',
            ]
        );

        $saleEventId = $fieldset->addField(
            'additional_data_sale_event_id',
            'text',
            [
                'name'      => 'additional_data[sale_event_id]',
                'label'     => 'Sale Event ID',
                'class'     => 'validate-digits',
                'value'     => $affiliate->getSaleEventId(),
                'note'      => 'The event ID for the sale taking place as provided by Tradedoubbler.',
            ]
        );

        if (!$affiliate->getCpsEnable()) {
            $saleEventId->setAfterElementHtml(
                '<script>//
                    < ![CDATA
                    document.getElementById(\'affiliate_additional_data_sale_event_id\').parentNode.parentNode.parentNode.style.display = \'none\';
                    //]]>
                </script>'
            );
        }

        $fieldset->addField(
            'additional_data_cpl_enable',
            'select',
            [
                'name'      => 'additional_data[cpl_enable]',
                'label'     => __('Pay-Per-Lead Program'),
                'value'     => $affiliate->getCplEnable(),
                'values'    => $this->sourceYesno->toOptionArray(),
                'note'      => 'Pay Per Lead (PPL) or Cost Per Lead (CPL). Merchant site pays a fixed amount for each visitor referred by affiliate who sign up as lead (registers an account on Merchant\'s site). PPL campaigns are suitable for building a newsletter list, member acquisition program or reward program.',
                'onchange'  => 'document.getElementById(\'affiliate_additional_data_lead_event_id\').parentNode.parentNode.parentNode.style.display = (this.value == 1? null : \'none\');',
            ]
        );

        $leadEventId = $fieldset->addField(
            'additional_data_lead_event_id',
            'text',
            [
                'name'      => 'additional_data[lead_event_id]',
                'label'     => 'Lead Event ID',
                'class'     => 'validate-digits',
                'value'     => $affiliate->getLeadEventId(),
                'note'      => 'The event ID for the lead taking place ' .
                    'as provided by Tradedoubbler. Leave empty to disable.',
            ]
        );

        if (!$affiliate->getCplEnable()) {
            $leadEventId->setAfterElementHtml(
                '<script>
                    //< ![CDATA
                    document.getElementById(\'affiliate_additional_data_lead_event_id\').parentNode.parentNode.parentNode.style.display = \'none\';
                    //]]>
                </script>'
            );
        }

        $fieldset->addField(
            'section_bodybegin_includeon_id',
            'hidden',
            [
                'name'      => 'section_bodybegin_includeon_id',
                'value'     => $this->getIncludeonByKey('checkout_success')->getId(),
            ]
        );

        $fieldset->addField(
            'section_bodyend_includeon_id',
            'hidden',
            [
                'name'      => 'section_bodyend_includeon_id',
                'value'     => $this->getIncludeonByKey('all')->getId(),
            ]
        );

        /* Retargeting */
        $retargeting = $form->addFieldset(
            'section_retargeting',
            ['legend' => __('Retargeting TAGs'), 'class' => 'fieldset-wide']
        );

        $retargetingEnable = $retargeting->addField(
            'additional_data_retargeting_enable',
            'select',
            [
                'name'      => 'additional_data[retargeting_enable]',
                'label'     => __('Enable Retargeting'),
                'value'     => $affiliate->getRetargetingEnable(),
                'values'    => $this->sourceYesno->toOptionArray(),
                'note'      => 'Retargeting affiliates are companies working with merchants through ' .
                    'the affiliate network as a retargeting service provider. Retargeting companies ' .
                    'are running display ad campaigns targeted at visitors who have recently visited ' .
                    'a merchant\'s site.',
            ]
        );

        $retargetingHomepage = $retargeting->addField(
            'additional_data_retargeting_homepage_tagid',
            'text',
            [
                'name'      => 'additional_data[retargeting_homepage_tagid]',
                'label'     => 'Homepage Tag Id',
                'class'     => 'validate-digits',
                'value'     => $affiliate->getRetargetingTagId('homepage'),
            ]
        );

        $retargetingCategory = $retargeting->addField(
            'additional_data_retargeting_category_tagid',
            'text',
            [
                'name'      => 'additional_data[retargeting_category_tagid]',
                'label'     => 'Product Listings Tag Id',
                'class'     => 'validate-digits',
                'value'     => $affiliate->getRetargetingTagId('category'),
            ]
        );

        $retargetingProduct = $retargeting->addField(
            'additional_data_retargeting_product_tagid',
            'text',
            [
                'name'      => 'additional_data[retargeting_product_tagid]',
                'label'     => 'Product Pages Tag Id',
                'class'     => 'validate-digits',
                'value'     => $affiliate->getRetargetingTagId('product'),
            ]
        );

        $retargetingBasket = $retargeting->addField(
            'additional_data_retargeting_basket_tagid',
            'text',
            [
                'name'      => 'additional_data[retargeting_basket_tagid]',
                'label'     => 'Basket Page Tag Id',
                'class'     => 'validate-digits',
                'value'     => $affiliate->getRetargetingTagId('basket'),
            ]
        );

        $retargetingRegistration = $retargeting->addField(
            'additional_data_retargeting_registration_tagid',
            'text',
            [
                'name'      => 'additional_data[retargeting_registration_tagid]',
                'label'     => 'Registration Tag Id',
                'class'     => 'validate-digits',
                'value'     => $affiliate->getRetargetingTagId('registration'),
            ]
        );

        $retargetingCheckout = $retargeting->addField(
            'additional_data_retargeting_checkout_tagid',
            'text',
            [
                'name'      => 'additional_data[retargeting_checkout_tagid]',
                'label'     => 'Check-out Page Tag Id',
                'class'     => 'validate-digits',
                'value'     => $affiliate->getRetargetingTagId('checkout'),
                'note'      => 'ContainerTagId can be found in TradeDoubler\'s system or ask your contact ' .
                    'at TradeDoubler. For every of the 6 cases described above there is a unique id.',
            ]
        );

        return $this;
    }
}

<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab\Template;

class EbayEnterprise extends AbstractNetwork
{
    /**
     * {@inheritdoc}
     */
    public function prepareForm($form)
    {
        $affiliate  = $this->getAffiliate();

        // PepperJam.
        $fieldsetPJ = $form->addFieldset(
            'section_bodybegin_pj',
            [
                'legend' => __('eBay Enterprise Affiliate Network (formerly PepperJam Exchange)'),
                'class' => 'fieldset-wide'
            ]
        );

        $fieldsetPJ->addField(
            'additional_data_pj_program_id',
            'text',
            [
                'name'      => 'additional_data[pj_program_id]',
                'label'     => 'Program ID',
                'class'     => 'validate-digits input-text-short',
                'value'     => $affiliate->getPjProgramId(),
                'note'      => 'A static numeric program ID constant provided to you by PepperJam.',
            ]
        )
        ->setAfterElementHtml('<img src="'. $this->getViewFileUrl('Plumrocket_Affiliate::images/eBayEnterprisePepperJam.png') . '" style="position: absolute; margin-left: 40px; top: 0;" />');

        $fieldsetPJ->addField(
            'additional_data_pj_cps_enable',
            'select',
            [
                'name'      => 'additional_data[pj_cps_enable]',
                'label'     => __('Pay-Per-Sale Program'),
                'value'     => $affiliate->getPjCpsEnabled(),
                'values'    => [
                    0 => __('Disabled'),
                    1 => __('Enabled'),
                ],
                'note' => 'Pay Per Sale (PPS) or Cost Per Sale (CPS). Merchant site pays a percentage of the sale when the affiliate sends them a customer who purchases something. Merchant only pays its affiliates when it gets a desired result.',
            ]
        );

        $fieldsetPJ->addField(
            'additional_data_pj_cpl_enable',
            'select',
            [
                'name'      => 'additional_data[pj_cpl_enable]',
                'label'     => __('Pay-Per-Lead Program'),
                'value'     => $affiliate->getPjCplEnabled(),
                'values'    => [
                    0 => __('Disabled'),
                    1 => __('Enabled'),
                ],
                'note' => 'Pay Per Lead (PPL) or Cost Per Lead (CPL). Merchant site pays a fixed amount for each visitor referred by affiliate who sign up as lead (registers an account on Merchant\'s site). PPL campaigns are suitable for building a newsletter list, member acquisition program or reward program.',
            ]
        );

        $fieldsetPJ->addField(
            'section_bodybegin_includeon_id',
            'hidden',
            [
                'name'      => 'section_bodybegin_includeon_id',
                'value'     => $this->getIncludeonByKey('all')->getId(),
            ]
        );

        // Fetchback.
        $fieldsetFB = $form->addFieldset(
            'section_bodybegin_fb',
            ['legend' => __('eBay Enterprise Display & Retargeting (formerly Fetchback)'), 'class' => 'fieldset-wide']
        );

        $fieldsetFB->addField(
            'additional_data_fb_site_id',
            'text',
            [
                'name'      => 'additional_data[fb_site_id]',
                'label'     => 'Site ID',
                'class'     => 'validate-digits input-text-short ',
                'value'     => $affiliate->getFbSiteId(),
                'note'      => 'Site ID number assigned to your account.',
            ]
        )
        ->setAfterElementHtml('<img src="'. $this->getViewFileUrl('Plumrocket_Affiliate::images/eBayEnterpriseFetchback.png') .'" style="position: absolute; margin-left: 40px; top: 0;" />');

        $fieldsetFB->addField(
            'additional_data_fb_cps_enable',
            'select',
            [
                'name'      => 'additional_data[fb_cps_enable]',
                'label'     => __('Pay-Per-Sale Program'),
                'value'     => $affiliate->getFbCpsEnabled(),
                'values'    => [
                    0 => __('Disabled'),
                    1 => __('Enabled'),
                ],
                'note' => 'Pay Per Sale (PPS) or Cost Per Sale (CPS). Merchant site pays a percentage of the sale when the affiliate sends them a customer who purchases something. Merchant only pays its affiliates when it gets a desired result.',
            ]
        );

        $fieldsetFB->addField(
            'section_bodyend_includeon_id',
            'hidden',
            [
                'name'      => 'section_bodyend_includeon_id',
                'value'     => $this->getIncludeonByKey('all')->getId(),
            ]
        );

        return $this;
    }
}

<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab\Template;

class Zanox extends AbstractNetwork
{
    /**
     * {@inheritdoc}
     */
    public function prepareForm($form)
    {
        $affiliate  = $this->getAffiliate();

        $fieldsetCps = $form->addFieldset(
            'section_cps',
            ['legend' => __('Affiliate Pay-Per-Sale Script'), 'class' => 'fieldset-wide']
        );

        $fieldsetCps->addField(
            'additional_data_cps_enable',
            'select',
            [
                'name'      => 'additional_data[cps_enable]',
                'label'     => __('Pay-Per-Sale Program'),
                'value'     => $affiliate->getCpsEnabled(),
                'values'    => [
                    0 => __('Disabled'),
                    1 => __('Enabled'),
                ],
                'note' => 'Pay Per Sale (PPS) or Cost Per Sale (CPS). Merchant site pays a percentage of the sale when the affiliate sends them a customer who purchases something. Merchant only pays its affiliates when it gets a desired result.
                <style>
                    .admin__scope-old .form-inline .fieldset>.field>.label span {word-break: normal;}
                </style>
                ',
            ]
        );

        $fieldsetCps->addField(
            'additional_data_cps_program_code_aid',
            'text',
            [
                'name'      => 'additional_data[cps_program_code_aid]',
                'label'     => 'Program Code',
                'class'     => 'validate-alphanum',
                'value'     => $affiliate->getApplicationId('cps_program_code'),
                'note'      => 'Program identifier on Zanox platform',
            ]
        );

        $fieldsetCps->addField(
            'additional_data_home_aid',
            'text',
            [
                'name'      => 'additional_data[home_page_aid]',
                'label'     => 'Home Mediaslot ID',
                'class'     => 'validate-alphanum',
                'value'     => $affiliate->getApplicationId('home_page'),
                'note'      => 'Your actual application ID for home page. You can find your application ID on the tab "zanox keys".',
            ]
        );

        $fieldsetCps->addField(
            'additional_data_category_aid',
            'text',
            [
                'name'      => 'additional_data[category_page_aid]',
                'label'     => 'Category Page Mediaslot ID',
                'class'     => 'validate-alphanum',
                'value'     => $affiliate->getApplicationId('category_page_aid'),
                'note'      => 'Your actual application ID for category page. You can find your application ID on the tab "zanox keys".',
            ]
        );

        $fieldsetCps->addField(
            'additional_data_product_aid',
            'text',
            [
                'name'      => 'additional_data[product_page_aid]',
                'label'     => 'Product Page Mediaslot ID',
                'class'     => 'validate-alphanum',
                'value'     => $affiliate->getApplicationId('product_page'),
                'note'      => 'Your actual application ID for product page. You can find your application ID on the tab "zanox keys".',
            ]
        );

        $fieldsetCps->addField(
            'additional_data_cart_aid',
            'text',
            [
                'name'      => 'additional_data[cart_page_aid]',
                'label'     => 'Basket Page Mediaslot ID',
                'class'     => 'validate-alphanum',
                'value'     => $affiliate->getApplicationId('cart_page'),
                'note'      => 'Your actual application ID for shopping cart page. You can find your application ID on the tab "zanox keys".',
            ]
        );

        $fieldsetCps->addField(
            'additional_data_checkout_success_aid',
            'text',
            [
                'name'      => 'additional_data[checkout_success_aid]',
                'label'     => 'Checkout Page Mediaslot ID',
                'class'     => 'validate-alphanum',
                'value'     => $affiliate->getApplicationId('checkout_success'),
                'note'      => 'Your actual application ID for order success page. You can find your application ID on the tab "zanox keys".',
            ]
        );

        $fieldsetCps->addField(
            'additional_data_generic_aid',
            'text',
            [
                'name'      => 'additional_data[all_aid]',
                'label'     => 'Generic Page Mediaslot ID',
                'class'     => 'validate-alphanum',
                'value'     => $affiliate->getApplicationId('all'),
                'note'      => 'Your actual application ID for any other page of your website. You can find your application ID on the tab "zanox keys".',
            ]
        );

        $fieldsetCpl = $form->addFieldset('section_bodyend',
            ['legend' => __('Affiliate Pay-Per-Lead Script'), 'class' => 'fieldset-wide']
        );

        $fieldsetCpl->addField(
            'additional_data_cpl_enable',
            'select',
            [
                'name'      => 'additional_data[cpl_enable]',
                'label'     => __('Pay-Per-Lead Program'),
                'value'     => $affiliate->getCplEnabled(),
                'values'    => [
                    0 => __('Disabled'),
                    1 => __('Enabled'),
                ],
                'note' => 'Pay Per Lead (PPL) or Cost Per Lead (CPL). Merchant site pays a fixed amount for each visitor referred by affiliate who sign up as lead (registers an account on Merchant\'s site). PPL campaigns are suitable for building a newsletter list, member acquisition program or reward program.',
            ]
        );

        $fieldsetCpl->addField(
            'additional_data_cpl_program_code_aid',
            'text',
            [
                'name'      => 'additional_data[cpl_program_code_aid]',
                'label'     => 'Program Code',
                'class'     => 'validate-alphanum',
                'value'     => $affiliate->getApplicationId('cpl_program_code'),
                'note'      => 'Program identifier on Zanox platform',
            ]
        );

        $fieldsetCpl->addField(
            'additional_data_registration_success_aid',
            'text',
            [
                'name'      => 'additional_data[registration_success_pages_aid]',
                'label'     => 'Registration Page Mediaslot ID',
                'class'     => 'validate-alphanum',
                'value'     => $affiliate->getApplicationId('registration_success_pages'),
                'note'      => 'Your actual application ID for registration success pages of your website (when a customer registered successfully). You can find your application ID on the tab "zanox keys".',
            ]
        );

        $fieldsetCpl->addField(
            'section_bodybegin_includeon_id',
            'hidden',
            [
                'name'      => 'section_bodybegin_includeon_id',
                'value'     => $this->getIncludeonByKey('all')->getId(),
            ]
        );

        $fieldsetCpl->addField(
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

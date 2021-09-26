<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab\Template;

use Magento\Framework\Data\Form;
use Plumrocket\Affiliate\Model\Affiliate\Everflow as EverflowModel;

class Everflow extends AbstractNetwork
{
    /**
     * @param \Magento\Framework\Data\Form $form
     * @return $this
     */
    public function prepareForm(Form $form)
    {
        /**
         * @var EverflowModel $affiliate
         */
        $affiliate  = $this->getAffiliate();

        $conversionTrackingFieldset = $form->addFieldset(
            'conversion_tracking',
            [
                'legend' => __('Conversion Tracking'),
                'class' => 'fieldset-wide'
            ]
        );

        $conversionTrackingFieldset->addField(
            'additional_data_advertiser_id',
            'text',
            [
                'name'      => 'additional_data[advertiser_id]',
                'label'     => 'Advertiser ID',
                'required'  => true,
                'class'     => 'validate-digits',
                'value'     => $affiliate->getAdvertiserId() ?: '',
                'note'     => __('Place the applicable Everflow Advertiser ID here. ' .
                    'Consult your account contact or assigned integrator if in any doubt.'),
            ]
        );

        $conversionTrackingFieldset->addField(
            'additional_data_conversion_tracking_domain',
            'text',
            [
                'name'     => 'additional_data[conversion_tracking_domain]',
                'label'    => 'Conversion Domain',
                'required' => true,
                'value'    => $affiliate->getConversionTrackingDomain(),
                'note'     => __('Navigate to Everflow Control Center - Configuration > Domains to see the ' .
                                 'conversion domain in your account. For example: https://www.example.com'),
            ]
        );

        $conversionTrackingFieldset->addField(
            'additional_data_integration_type',
            'select',
            [
                'name'      => 'additional_data[integration_type]',
                'label'     => __('Integration Type'),
                'title'     => __('Integration Type'),
                'required'  => false,
                'value'     => $affiliate->getIntegrationType(),
                'values'    => [
                    EverflowModel::INTEGRATION_TYPE_ORDER => __('Register each order as a Conversion'),
                    EverflowModel::INTEGRATION_TYPE_SKU => __('Register each SKU purchase as a Conversion')
                ],
            ]
        );

        $clickTrackingFieldset = $form->addFieldset(
            'section_bodybegin',
            [
                'legend' => __('Click Tracking'),
                'class' => 'fieldset-wide',
            ]
        );

        $directLinkField = $clickTrackingFieldset->addField(
            'additional_data_direct_linking',
            'select',
            [
                'name'      => 'additional_data[direct_linking]',
                'label'     => __('Direct Linking'),
                'title'     => __('Direct Linking'),
                'required'  => false,
                'value'     => (int) $affiliate->isDirectLinkingEnabled(),
                'values'    => [
                    0 => __('No'),
                    1 => __('Yes')
                ],
            ]
        );

        $trackingDomainField = $clickTrackingFieldset->addField(
            'additional_data_click_tracking_domain',
            'text',
            [
                'name'     => 'additional_data[click_tracking_domain]',
                'label'    => 'Tracking Domain',
                'required' => true,
                'value'    => $affiliate->getClickTrackingDomain(),
                'note'     => __('Navigate to Everflow Control Center - Configuration > Domains to see the tracking ' .
                    'domain in your account. For example: https://www.example.com'),
            ]
        );

        $clickParamsMapping = $affiliate->getClickParamsMapping();

        $offerIdField = $clickTrackingFieldset->addField(
            'additional_data_mapping_offer_id',
            'text',
            [
                'name'     => 'additional_data[click_params_mapping][offer_id]',
                'label'    => 'Offer Id',
                'required' => true,
                'value'    => $clickParamsMapping['offer_id'],
            ]
        );

        $affiliateIdField = $clickTrackingFieldset->addField(
            'additional_data_mapping_affiliate_id',
            'text',
            [
                'name'     => 'additional_data[click_params_mapping][affiliate_id]',
                'label'    => 'Affiliate Id',
                'required' => true,
                'value'    => $clickParamsMapping['affiliate_id'],
            ]
        );

        $sub1Field = $clickTrackingFieldset->addField(
            'additional_data_mapping_sub1',
            'text',
            [
                'name'  => 'additional_data[click_params_mapping][sub1]',
                'label' => 'Sub 1',
                'value' => $clickParamsMapping['sub1'],
                'note'  => 'Send back to Everflow additional data received from the destination URL GET parameters.',
            ]
        );

        $sub2Field = $clickTrackingFieldset->addField(
            'additional_data_mapping_sub2',
            'text',
            [
                'name'  => 'additional_data[click_params_mapping][sub2]',
                'label' => 'Sub 2',
                'value' => $clickParamsMapping['sub2'],
            ]
        );

        $sub3Field = $clickTrackingFieldset->addField(
            'additional_data_mapping_sub3',
            'text',
            [
                'name'  => 'additional_data[click_params_mapping][sub3]',
                'label' => 'Sub 3',
                'value' => $clickParamsMapping['sub3'],
            ]
        );

        $sub4Field = $clickTrackingFieldset->addField(
            'additional_data_mapping_sub4',
            'text',
            [
                'name'  => 'additional_data[click_params_mapping][sub4]',
                'label' => 'Sub 4',
                'value' => $clickParamsMapping['sub4'],
            ]
        );

        $sub5Field = $clickTrackingFieldset->addField(
            'additional_data_mapping_sub5',
            'text',
            [
                'name'  => 'additional_data[click_params_mapping][sub5]',
                'label' => 'Sub 5',
                'value' => $clickParamsMapping['sub5'],
            ]
        );

        $uidField = $clickTrackingFieldset->addField(
            'additional_data_mapping_uid',
            'text',
            [
                'name'  => 'additional_data[click_params_mapping][uid]',
                'label' => 'UID',
                'value' => $clickParamsMapping['uid'],
            ]
        );

        $sourceIdField = $clickTrackingFieldset->addField(
            'additional_data_mapping_source_id',
            'text',
            [
                'name'  => 'additional_data[click_params_mapping][source_id]',
                'label' => 'Source Id',
                'value' => $clickParamsMapping['source_id'],
            ]
        );

        $transactionIdField = $clickTrackingFieldset->addField(
            'additional_data_mapping_transaction_id',
            'text',
            [
                'name'  => 'additional_data[click_params_mapping][transaction_id]',
                'label' => 'Transaction Id',
                'value' => $clickParamsMapping['transaction_id'],
            ]
        );

        $couponCodeField = $clickTrackingFieldset->addField(
            'additional_data_mapping_coupon_code',
            'text',
            [
                'name'  => 'additional_data[click_params_mapping][coupon_code]',
                'label' => 'Coupon Code',
                'value' => $clickParamsMapping['coupon_code'],
            ]
        );

        $this->getTab()->setChild(
            'form_after',
            $this->getLayout()->createBlock(\Magento\Backend\Block\Widget\Form\Element\Dependence::class)
                 ->addFieldMap($directLinkField->getHtmlId(), $directLinkField->getName())
                 ->addFieldMap($trackingDomainField->getHtmlId(), $trackingDomainField->getName())
                 ->addFieldMap($offerIdField->getHtmlId(), $offerIdField->getName())
                 ->addFieldMap($affiliateIdField->getHtmlId(), $affiliateIdField->getName())
                 ->addFieldMap($sub1Field->getHtmlId(), $sub1Field->getName())
                 ->addFieldMap($sub2Field->getHtmlId(), $sub2Field->getName())
                 ->addFieldMap($sub3Field->getHtmlId(), $sub3Field->getName())
                 ->addFieldMap($sub4Field->getHtmlId(), $sub4Field->getName())
                 ->addFieldMap($sub5Field->getHtmlId(), $sub5Field->getName())
                 ->addFieldMap($uidField->getHtmlId(), $uidField->getName())
                 ->addFieldMap($sourceIdField->getHtmlId(), $sourceIdField->getName())
                 ->addFieldMap($transactionIdField->getHtmlId(), $transactionIdField->getName())
                 ->addFieldMap($couponCodeField->getHtmlId(), $couponCodeField->getName())
                 ->addFieldDependence(
                     $trackingDomainField->getName(),
                     $directLinkField->getName(),
                     1
                 )
                 ->addFieldDependence(
                     $offerIdField->getName(),
                     $directLinkField->getName(),
                     1
                 )
                 ->addFieldDependence(
                     $affiliateIdField->getName(),
                     $directLinkField->getName(),
                     1
                 )
                 ->addFieldDependence(
                     $sub1Field->getName(),
                     $directLinkField->getName(),
                     1
                 )
                 ->addFieldDependence(
                     $sub2Field->getName(),
                     $directLinkField->getName(),
                     1
                 )
                 ->addFieldDependence(
                     $sub3Field->getName(),
                     $directLinkField->getName(),
                     1
                 )
                 ->addFieldDependence(
                     $sub4Field->getName(),
                     $directLinkField->getName(),
                     1
                 )
                 ->addFieldDependence(
                     $sub5Field->getName(),
                     $directLinkField->getName(),
                     1
                 )
                 ->addFieldDependence(
                     $uidField->getName(),
                     $directLinkField->getName(),
                     1
                 )
                 ->addFieldDependence(
                     $sourceIdField->getName(),
                     $directLinkField->getName(),
                     1
                 )
                 ->addFieldDependence(
                     $transactionIdField->getName(),
                     $directLinkField->getName(),
                     1
                 )
                 ->addFieldDependence(
                     $couponCodeField->getName(),
                     $directLinkField->getName(),
                     1
                 )
        );

        return $this;
    }
}

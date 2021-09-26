<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab\Template;

use Plumrocket\Affiliate\Model\Config\Source\Affilinet\Tracking\Parameter as TrackingParameter;
use Plumrocket\Affiliate\Model\Config\Source\Affilinet\Tracking\Product\Attribute as ProductAttribute;

class Affilinet extends AbstractNetwork
{
    /**
     * @var \Plumrocket\Affiliate\Model\Config\Source\Affilinet\Domain
     */
    protected $domain;
    /**
     * @var \Plumrocket\Affiliate\Model\Config\Source\Affilinet\Tracking\Event
     */
    protected $trackingEvent;
    /**
     * @var \Plumrocket\Affiliate\Model\Config\Source\Affilinet\Tracking\Parameter
     */
    protected $trackingParameter;
    /**
     * @var \Plumrocket\Affiliate\Model\Config\Source\Affilinet\Tracking\Product\Attribute
     */
    protected $productAttribute;
    /**
     * @var \Plumrocket\Affiliate\Model\Config\Source\Affilinet\Profiling\Event
     */
    protected $profilingEvent;

    /**
     * @param \Magento\Backend\Block\Template\Context                                        $context
     * @param \Plumrocket\Affiliate\Api\PageTypeProviderInterface                            $pageTypeProvider
     * @param \Plumrocket\Affiliate\Model\Config\Source\Affilinet\Domain                     $domain
     * @param \Plumrocket\Affiliate\Model\Config\Source\Affilinet\Tracking\Event             $trackingEvent
     * @param \Plumrocket\Affiliate\Model\Config\Source\Affilinet\Tracking\Parameter         $trackingParameter
     * @param \Plumrocket\Affiliate\Model\Config\Source\Affilinet\Tracking\Product\Attribute $productAttribute
     * @param \Plumrocket\Affiliate\Model\Config\Source\Affilinet\Profiling\Event            $profilingEvent
     * @param array                                                                          $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context                                         $context,
        \Plumrocket\Affiliate\Api\PageTypeProviderInterface                             $pageTypeProvider,
        \Plumrocket\Affiliate\Model\Config\Source\Affilinet\Domain                      $domain,
        \Plumrocket\Affiliate\Model\Config\Source\Affilinet\Tracking\Event              $trackingEvent,
        \Plumrocket\Affiliate\Model\Config\Source\Affilinet\Tracking\Parameter          $trackingParameter,
        \Plumrocket\Affiliate\Model\Config\Source\Affilinet\Tracking\Product\Attribute  $productAttribute,
        \Plumrocket\Affiliate\Model\Config\Source\Affilinet\Profiling\Event             $profilingEvent,
        array $data = []
    ) {
        parent::__construct($context, $pageTypeProvider, $data);
        $this->domain               = $domain;
        $this->trackingEvent        = $trackingEvent;
        $this->trackingParameter    = $trackingParameter;
        $this->productAttribute     = $productAttribute;
        $this->profilingEvent       = $profilingEvent;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareForm($form)
    {
        $affiliate  = $this->getAffiliate();

        // PepperJam.
        $fieldsetBase = $form->addFieldset(
            'section_bodyend',
            ['legend' => __('Affiliate Script'), 'class' => 'fieldset-wide']
        );

        $fieldsetBase->addField(
            'additional_data_program_id',
            'text',
            [
                'name'      => 'additional_data[program_id]',
                'label'     => 'Program ID',
                'required'  => true,
                'class'     => 'validate-digits input-text-short',
                'value'     => $affiliate->getProgramId(),
                'note'      => 'Enter your affilinet advertiser program ID.',
            ]
        );

        $fieldsetBase->addField(
            'additional_data_tag_id',
            'text',
            [
                'name'      => 'additional_data[tag_id]',
                'label'     => 'Tag ID',
                'required'  => true,
                'class'     => 'input-text-short',
                'value'     => $affiliate->getTagId(),
                'note'      => 'Enter a personalized tag ID, if needed.',
            ]
        );

        $fieldsetBase->addField(
            'additional_data_domain',
            'select',
            [
                'name'      => 'additional_data[domain]',
                'label'     => __('Domain'),
                'value'     => $affiliate->getDomain(),
                'values'    => $this->domain->toOptionArray(),
                'note' => 'The domain to be used for the tracking depends on the country where the affilinet program is registered.'
                    . ' Orders with the wrong tracking domain will not be tracked.',
            ]
        );

        $fieldsetBase->addField(
            'section_bodyend_includeon_id',
            'hidden',
            [
                'name'      => 'section_bodyend_includeon_id',
                'value'     => $this->getIncludeonByKey('all')->getId(),
            ]
        )->setAfterElementHtml(
			"<script>
                //< ![CDATA
                    require(['jquery'], function($){
                        window.checkDependencies = function(){
                            var baseField = $('#affiliate_additional_data_tracking_event');
                            var parameterField = $('select[id^=\"affiliate_additional_data_tracking_parameter\"]').parents('.admin__field');
                            var attributeField = $('select[id^=\"affiliate_additional_data_tracking_attribute\"]').parents('.admin__field');

                            switch (baseField.val()) {
                                case '1':
                                    parameterField.slideDown();
                                    attributeField.slideUp();
                                    break;

                                case '2':
                                    parameterField.slideDown();
                                    attributeField.slideUp();
                                    break;

                                case '3':
                                    parameterField.slideDown();
                                    attributeField.slideDown();
                                    break;

                                default:
                                    parameterField.slideUp();
                                    attributeField.slideUp();
                            }
                        };

                        $(document).ready(function(){window.checkDependencies();});
                    });
                //]]>
            </script>"
        );

        $fieldsetTracking = $form->addFieldset(
            'section_bodyend_tracking',
            ['legend' => __('Order Tracking'), 'class' => 'fieldset-wide']
        );

        $fieldsetTracking->addField(
            'additional_data_tracking_event',
            'select',
            [
                'name'      => 'additional_data[tracking_event]',
                'label'     => __('Event'),
                'value'     => $affiliate->getTrackingEvent(),
                'values'    => $this->trackingEvent->toOptionArray(),
                'note' => 'If you want to enable tracking please select tracking event.'
                    . 'The order tracking module provides events for tracking sales, leads and basket orders.',
                'onchange' => 'window.checkDependencies();',
            ]
        );

        /**
         * List of tracking parameters
         */
        for ($i = 1; $i <= TrackingParameter::MAX_COUNT; $i++) {
            $currentValue = $affiliate->getTrackingParameter($i);
            if (! $currentValue) {
                switch ($i) {
                    case 1:
                        $currentValue = 'payment_method';
                        break;
                    case 2:
                        $currentValue = 'shipping_method';
                        break;
                    default:
                }
            }

            $fieldsetTracking->addField(
                'additional_data_tracking_parameter' . $i,
                'select',
                [
                    'name'   => 'additional_data[tracking_parameter' . $i . ']',
                    'label'  => __('Parameter ' . $i),
                    'value'  => $currentValue,
                    'values' => $this->trackingParameter->toOptionArray(),
                    'note'   => 'Select information to enrich your affilinet statistics. ' .
                        'Note: If necessary the information can be made available to publishers.',
                ]
            );
        }

        /**
         * List of tracking attributes
         */
        $fieldsetTracking->addField(
            'additional_data_tracking_attribute_brand',
            'select',
            [
                'name'      => 'additional_data[tracking_attribute_brand]',
                'label'     =>  __('Product Brand Attribute'),
                'value'     => $affiliate->getTrackingAttributeBrand(),
                'values'    => $this->productAttribute->toOptionArray(),
                'note'      => 'Select brand attribute for your products.',
            ]
        );

        for ($i = 1; $i <= ProductAttribute::MAX_COUNT; $i++) {
            $fieldsetTracking->addField(
                'additional_data_tracking_attribute' . $i,
                'select',
                [
                    'name'      => 'additional_data[tracking_attribute'  . $i . ']',
                    'label'     =>  __('Attribute ' . $i),
                    'value'     => $affiliate->getTrackingAttribute($i),
                    'values'    => $this->productAttribute->toOptionArray(),
                    'note'      => '',
                ]
            );
        }

        $fieldsetProfiling = $form->addFieldset(
            'section_bodyend_profiling',
            ['legend' => __('Profiling'), 'class' => 'fieldset-wide']
        );

        $fieldsetProfiling->addField(
            'additional_data_profiling_event',
            'multiselect',
            [
                'name'      => 'additional_data[profiling_event]',
                'label'     => __('Event'),
                'value'     => $affiliate->getProfilingEvent(),
                'values'    => $this->profilingEvent->toOptionArray(),
                'note' => 'The profiling module allows you to provide publishers with customer centric visitor behaviour data for targeting purposes'
                    . ' (e.g. Prospecting, Targeting, Retargeting or Cart Abandonment) before a sale or lead has been completed.',
            ]
        );

        return $this;
    }
}

<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab\Template;

class Chango extends AbstractNetwork
{

    /**
     * {@inheritdoc}
     */
    public function prepareForm($form)
    {
        $affiliate  = $this->getAffiliate();

        $fieldset = $form->addFieldset('section_bodybegin', ['legend' => __('Affiliate Script - Pay Per Sale (PPS) or Cost Per Sale (CPS) Program'), 'class' => 'fieldset-wide']);


        $fieldset->addField(
            'additional_data_chango_id',
            'text',
            [
                'name'      => 'additional_data[chango_id]',
                'label'     => 'Chango ID',
                'required'  => true,
                'value'     => $affiliate->getChangoId(),
                'note' => 'Your Chango ID',
            ]
        );

        $fieldset->addField(
            'additional_data_conversion_id',
            'text',
            [
                'name'      => 'additional_data[conversion_id]',
                'label'     => 'Conversion ID',
                'required'  => true,
                'value'     => $affiliate->getConversionId(),
                'note' => 'Your Chango Conversion ID',
            ]
        );

        $fieldset->addField(
            'additional_data_implementing_method',
            'select',
            [
                'name'      => 'additional_data[implementing_method]',
                'label'     => __('Pixel Type'),
                'value'     => $affiliate->getImplementingMethod(),
                'values'    => [
                    \Plumrocket\Affiliate\Model\Affiliate\Chango::JAVASCRIPT_IMPLEMENTING_METHOD => __('JavaScript (preferred)'),
                    \Plumrocket\Affiliate\Model\Affiliate\Chango::IMAGE_IMPLEMENTING_METHOD => __('Image based'),
                ],
                'onchange' => 'onpixelTypeChange(this);',
                'note' => 'The recommended method of implementing the Chango Pixel is the JavaScript method listed above. If this is not possible, however, Chango provides an image version of the pixel.'
                .'<script type="text/javascript">//<![CDATA[

                    function onpixelTypeChange(pixelType) {
                        var isJs = pixelType.value == "javascript";
                        var jsview = document.getElementsByClassName("jsview");
                        var imgview = document.getElementsByClassName("imgview");
                        for (var i = 0; i < jsview.length; i++) {
                            jsview[i].style.display = isJs ? "block" : "none";
                        }
                        for (var i = 0; i < imgview.length; i++) {
                            imgview[i].style.display = !isJs ? "block" : "none";
                        }
                    }
                //]]></script>',
            ]
        );

        $isJsType = $affiliate->getImplementingMethod() != \Plumrocket\Affiliate\Model\Affiliate\Chango::IMAGE_IMPLEMENTING_METHOD;

        $ion = $this->getIncludeonByKey('checkout_success')->getId();
        $ionV = ($affiliate->getSectionBodybeginIncludeonId() === null) ? $ion : $affiliate->getSectionBodybeginIncludeonId();
        $style = "width: 780px !important; font-size: 11px; font-weight: 500; font-family: Courier New, monospace; padding: 2px; margin: 5px 5px 0px 0px;";
        $fieldset->addField(
            'section_bodybegin_includeon_id',
            'select',
            [
                'name'      => 'section_bodybegin_includeon_id',
                'label'     => __('Enable Conversion Pixel'),
                'value'     => $ionV,
                'values'    => [
                    0 => __('Disabled'),
                    $ion => __('Enabled'),
                ],
                'onchange' => "document.getElementById('conversion_pixel_example').style.display = (this.value != 0 ? 'block' : 'none')" ,
                'note' => 'Executed on Checkout Success Page. Allows Chango to track conversion events including view through and click through attribution, and post conversion impact.'
                    .'<div id="conversion_pixel_example" style="'.($ionV ? '' : 'display:none;').'"><textarea disabled="disabled" class="jsview" style="'.$style.'; height: 135px; '.($isJsType ? '' : 'display:none;').'">'.htmlspecialchars($affiliate->getCodeTemplate('conversion_javascript')).'</textarea>
                    <textarea disabled="disabled" class="imgview" style="'.$style.'; height: 45px; '.(!$isJsType ? '' : 'display:none;').'">'.htmlspecialchars($affiliate->getCodeTemplate('conversion_image')).'</textarea>
    <p class="note"><span>Preview of Conversion Pixel.</span></p></div>',
            ]
        );

        $ion = $this->getIncludeonByKey('all')->getId();
        $ionV = ($affiliate->getSectionBodyendIncludeonId() === null) ? $ion : $affiliate->getSectionBodyendIncludeonId();
        $fieldset->addField(
            'section_bodyend_includeon_id',
            'select',
            [
                'name'      => 'section_bodyend_includeon_id',
                'label'     => __('Enable Optimization Pixel (Smart Pixel)'),
                'value'     => $ionV,
                'values'    => [
                    0 => __('Disabled'),
                    $ion => __('Enabled'),
                ],
                'onchange' => "document.getElementById('optimization_pixel_example').style.display = (this.value != 0 ? 'block' : 'none')" ,
                'note' => 'Executed on the website, inside the "footer". Improves campaign performance optimization, helps with branding, acquisition and retargeting.'
                .'<div id="optimization_pixel_example" style="'.($ionV ? '' : 'display:none;').'"><textarea disabled="disabled" class="jsview" style="'.$style.'; height: 170px; '.($isJsType ? '' : 'display:none;').'">'.htmlspecialchars($affiliate->getCodeTemplate('optimization_javascript')).'</textarea>
                    <textarea disabled="disabled" class="imgview" style="'.$style.'; height: 60px; '.(!$isJsType ? '' : 'display:none;').'">'.htmlspecialchars($affiliate->getCodeTemplate('optimization_image')).'</textarea>
    <p class="note"><span>Preview of Optimization Pixel.</span></p></div>',
            ]
        );

        return $this;
    }
}

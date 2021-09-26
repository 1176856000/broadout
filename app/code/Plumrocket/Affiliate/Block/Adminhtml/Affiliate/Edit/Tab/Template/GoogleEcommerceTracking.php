<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab\Template;

use Magento\GoogleAnalytics\Helper\Data as GoogleAnalyticsData;
use Plumrocket\Base\Api\GetExtensionInformationInterface;

class GoogleEcommerceTracking extends AbstractNetwork
{
    /**
     * @var \Magento\GoogleAnalytics\Helper\Data
     */
    private $googleAnalyticsData;

    /**
     * @var \Plumrocket\Base\Api\GetExtensionInformationInterface
     */
    private $getExtensionInformation;

    /**
     * @param \Magento\Backend\Block\Template\Context               $context
     * @param \Plumrocket\Affiliate\Api\PageTypeProviderInterface   $pageTypeProvider
     * @param \Magento\GoogleAnalytics\Helper\Data                  $googleAnalyticsData
     * @param \Plumrocket\Base\Api\GetExtensionInformationInterface $getExtensionInformation
     * @param array                                                 $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Plumrocket\Affiliate\Api\PageTypeProviderInterface $pageTypeProvider,
        GoogleAnalyticsData $googleAnalyticsData,
        GetExtensionInformationInterface $getExtensionInformation,
        array $data = []
    ) {
        parent::__construct($context, $pageTypeProvider, $data);
        $this->googleAnalyticsData = $googleAnalyticsData;
        $this->getExtensionInformation = $getExtensionInformation;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareForm($form)
    {
        $fieldset = $form->addFieldset(
            'section_bodyend',
            ['legend' => __('Affiliate Script'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField(
            'section_head_includeon_id',
            'hidden',
            [
                'name'      => 'section_head_includeon_id',
                'value'     => $this->getIncludeonByKey('checkout_success')->getId(),
            ]
        );

        if (!$this->googleAnalyticsData->isGoogleAnalyticsAvailable()) {
            $url = $this->getUrl('adminhtml/system_config/edit', ['section' => 'google']);

            $fieldset->addField(
                'note',
                'note',
                [
                    'label'     => __('Google Analytics API'),
                    'required'  => true,
                    'text'      => __('Google Analytics is disabled in Magento Configuration.').'<br/>'.
                        __('Please enable Google Analytics in order for Ecommerce Tracking to work.').'<br/>'.
                        '<a title="Google Analytics API" href="'.$url.'" onclick="window.open(this.href); return false;"><img src="'.$this->getViewFileUrl('Plumrocket_Affiliate::images/google_api.png').'" style="border: 2px solid #d6d6d6; margin-top: 10px; margin-bottom: 10px" /></a><br/>'.
                        __('Go to System -> Configuration -> Google API (or <a href="'.$url.'" target="_blank" >click here</a>). Enter your Google Analytics Account Number, set Enable = "Yes" and press "Save Config".')
                ]
            );
        } else {
            $fieldset->addField(
                'note',
                'note',
                [
                    'label'     => __('Google Analytics API'),
                    'required'  => true,
                    'text'      => __('Good news! Google Analytics is enabled in Magento Configuration.').'<br/>'.
                        __('Your Account Number is <strong>%1</strong>.', $this->_scopeConfig->getValue(GoogleAnalyticsData::XML_PATH_ACCOUNT)).'<br/><br/>'.
                        __('Google Analytics Ecommerce Tracking is ready to report ecommerce activity.').'<br/>'.
                        __('Make sure to enable ecommerce tracking on the view (profile) settings page for your website in <a href="http://www.google.com/analytics/" target="_blank" >Google Analytics account</a>. For manual please refer to our <a href="%1"  target="_blank">online documentation</a>.', $this->getWikiLink())
                ]
            );
        }

        return $this;
    }

    /**
     * Get wiki link
     *
     * @return string
     */
    public function getWikiLink(): string
    {
        return $this->getExtensionInformation->execute('Affiliate')->getWikiLink();
    }
}

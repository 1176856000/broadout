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

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;
use Plumrocket\Datagenerator\Model\Config\Source\GoogleShopping\Country;
use Plumrocket\Datagenerator\Model\Config\Source\GoogleShopping\Language;

class Category extends Generic implements TabInterface
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var Country
     */
    private $googleShoppingCountry;

    /**
     * @var Language
     */
    private $googleShoppingLanguage;

    /**
     * Category constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Country $googleShoppingCountry
     * @param Language $googleShoppingLanguage
     * @param SerializerInterface $serializer
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Country $googleShoppingCountry,
        Language $googleShoppingLanguage,
        SerializerInterface $serializer,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->googleShoppingCountry = $googleShoppingCountry;
        $this->googleShoppingLanguage = $googleShoppingLanguage;
        $this->serializer = $serializer;
    }

    /**
     * @inheritDoc
     */
    public function getTabLabel()
    {
        return __('Category Mapping');
    }

    /**
     * @inheritDoc
     */
    public function getTabTitle()
    {
        return __('Category Mapping');
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
        $model = $this->_coreRegistry->registry('current_model');
        return 0 === (int) $model->getData('show_category_tab');
    }

    /**
     * @inheritDoc
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_model');
        $form = $this->_formFactory->create();
        $googleShopLang = $this->googleShoppingLanguage->toOptionHash();
        $currentCountry = $model->getCountry();

        $languageOptions = $currentCountry
            ? $googleShopLang[$currentCountry]['value']
            : reset($googleShopLang)['value'];

        $form->setHtmlIdPrefix('datagenerator_');

        $fieldset = $form->addFieldset(
            'category_fieldset',
            ['legend' => __('Category Mapping')]
        );

        $fieldset->addField(
            'country',
            'select',
            [
                'name' => 'country',
                'label' => __('Country'),
                'title' => __('Country'),
                'values' => $this->googleShoppingCountry->toOptionArray(),
                'onchange' =>
                    "require(
                        ['languageField'],
                        function (languageField) {
                            languageField.changeOptions(this);
                        }.bind(this)
                    );"
            ]
        );

        $fieldset->addField(
            'language',
            'select',
            [
                'name' => 'language',
                'label' => __('Language'),
                'title' => __('Language'),
                'options' => $languageOptions,
                'after_element_html' =>
                    "<script>require(
                        ['languageField'],
                        function (languageField) {
                            languageField.setCurrentLanguage('" . $model->getLanguage() . "');
                            languageField
                                .initCountryLanguages(" . $this->serializer->serialize($googleShopLang) . ");
                        });
                    </script>",
            ]
        );

        $chooser = $this->getLayout()->createBlock(
            \Plumrocket\Datagenerator\Block\Adminhtml\Datagenerator\Category\Tree::class
        );
        $categories = $chooser->getCategoryCollection()->addAttributeToSelect('google_product_category');
        $categoryMapping = $this->serializer->serialize(
            $this->getColumnValues('google_product_category', $categories)
        );

        $fieldset->addField(
            'category_mapping',
            \Plumrocket\Datagenerator\Block\Adminhtml\Datagenerator\Element\EmptyElement::class,
            [
                'name' => 'category_mapping',
                'label' => __('Mapping'),
                'title' => __('Mapping'),
                'after_element_html' => $chooser->toHtml(),
                'after_element_js' => '<script>require(
                        ["jquery", "Plumrocket_Datagenerator/js/form/ext-tree"],
                        function ($) {
                            Ext.tree.TreeNodeUI.prototype.googleTaxonomyValues = ' . $categoryMapping . ';
                            $(".categories-side-col > .sidebar-actions").remove();
                        });
                    </script>
                    <script type="text/x-magento-init">
                    {
                        ".mapping_input": {
                            "plumSearch": {
                                "url": "' . $this->getUrl('prdatagenerator/datagenerator/googleTaxonomy') . '",
                                "destinationSelector": "div#search_autocomplete_{id}"
                            }
                        }
                    }
                    </script>'
            ]
        );

        $fieldset->addField(
            'show_category_tab',
            'hidden',
            [
                'name' => 'show_category_tab',
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @param string $column
     * @param iterable $collection
     * @return array
     */
    private function getColumnValues(string $column, $collection)
    {
        $items = [];

        foreach ($collection as $item) {
            $items[$item->getId()] = $item->getData($column);
        }

        return $items;
    }
}

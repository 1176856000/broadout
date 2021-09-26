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

namespace Plumrocket\Datagenerator\Controller\Adminhtml\Datagenerator;

use Magento\Backend\App\Action;
use Magento\Store\Model\StoreManagerInterface;
use Plumrocket\Datagenerator\Model\CategoryMapping\Search;

class GoogleTaxonomy extends Action
{
    const TAXONOMY_SOURCE = 'https://www.google.com/basepages/producttype/taxonomy.{{language}}.txt';

    /**
     * @var Search
     */
    private $categorySearch;

    /**
     * GoogleTaxonomy constructor.
     *
     * @param Action\Context $context
     * @param Search $categorySearch
     */
    public function __construct(
        Action\Context $context,
        Search $categorySearch
    ) {
        parent::__construct($context);
        $this->categorySearch = $categorySearch;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $request = $this->getRequest();
        $result = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
        $source = $this->getSource($request->getParam('language', 'en-US'));
        $categories = $this->categorySearch->search($request->getParam('q'), $source);
        return $result->setData($categories);
    }

    /**
     * @param string $language
     * @return string
     */
    private function getSource(string $language)
    {
        return str_replace('{{language}}', $language, self::TAXONOMY_SOURCE);
    }
}

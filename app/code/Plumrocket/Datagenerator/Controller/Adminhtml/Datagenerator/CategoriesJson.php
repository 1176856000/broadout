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
use Magento\Framework\Controller\ResultFactory;

class CategoriesJson extends Action
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        $this->getRequest()
            ->setRouteName('catalog')
            ->setControllerName('category');

        return $this->resultFactory
            ->create(ResultFactory::TYPE_FORWARD)
            ->forward('categoriesJson');
    }
}

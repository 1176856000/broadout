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
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Datagenerator\Controller\Adminhtml\Datagenerator;

class CleanCache extends \Plumrocket\Datagenerator\Controller\Adminhtml\Datagenerator
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $ids = $this->getRequest()->getParam($this->_idKey);

        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $error = false;
        try {
            foreach ($ids as $id) {
                $this->_objectManager->get($this->_modelClass)->setId($id)->cleanCache();
            }
        } catch (\Exception $e) {
            $error = true;
            $this->messageManager->addExceptionMessage($e, __('We can\'t clean cache for '
                . strtolower($this->_objectTitle) . ' right now. ' . $e->getMessage()));
        }

        if (! $error) {
            $this->messageManager->addSuccessMessage(
                (count($ids) > 1) ? __($this->_objectTitles . ' have been cleaned cache.')
                    : __($this->_objectTitle . ' has been cleaned cache.')
            );
        }

        $this->_redirect('*/*');
    }
}

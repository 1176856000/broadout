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

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Plumrocket\Datagenerator\Controller\Adminhtml\Datagenerator;

class TestConnection extends Datagenerator
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $data = $this->getRequest()->getParams();
        $data = $this->prepareData($data);

        try {
            $this->getTransportHandler($data['protocol'])->open($data);
            $message = __('Connected successfully');
        } catch (NotFoundException $e) {
            // TODO merge catches after we stop support magento 2.2
            $message = $e->getMessage();
            $result->setHttpResponseCode(400);
        } catch (LocalizedException $e) {
            $message = $e->getMessage();
            $result->setHttpResponseCode(400);
        } catch (\SodiumException $e) {
            $message = $e->getMessage();
            $result->setHttpResponseCode(400);
        } catch (\Exception $e) {
            $message = __('Something went wrong');
            $result->setHttpResponseCode(400);
        }

        return $result->setData(['message' => $message]);
    }

    /**
     * @param array $data
     * @return array
     */
    private function prepareData(array $data): array
    {
        $data['user'] = $data['username'];

        if (! empty($data['password']) && preg_match('/^\*+$/', $data['password'])) {
            $data['password'] = $this->_getModel()->getOrigData('password');
        }

        return $data;
    }
}

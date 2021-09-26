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

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Plumrocket\Datagenerator\Controller\Adminhtml\Datagenerator;
use Plumrocket\Datagenerator\Model\RenderFactory;
use Plumrocket\Datagenerator\Model\TemplateFactory;

class Rebuild extends Datagenerator
{
    /**
     * @var RenderFactory
     */
    private $renderFactory;

    /**
     * Rebuild constructor.
     *
     * @param Context $context
     * @param TemplateFactory $templateFactory
     * @param RenderFactory $renderFactory
     */
    public function __construct(
        Context $context,
        TemplateFactory $templateFactory,
        RenderFactory $renderFactory
    ) {
        parent::__construct($context, $templateFactory);
        $this->renderFactory = $renderFactory;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $template = $this->_getModel();

            if ($template->getId()) {
                set_time_limit(0); // phpcs:ignore
                $renderer = $this->renderFactory->create();
                $renderer->setTemplate($template);
                $text = $renderer->getText();
                $this->messageManager->addSuccessMessage(__('Rebuild Data Feed Success'));

                if ($template->getFtpEnabled()) {
                    /** @var \Magento\Framework\Filesystem\Io\IoInterface $transportHandler */
                    $transportHandler = $this->getTransportHandler($template->getProtocol());
                    $transportHandler->open($template->getFtpData());
                    $transportHandler->write($template->getUrlKey(), $text);
                }
            } else {
                $this->messageManager->addErrorMessage(__('Feed Template Is Undefined'));
            }
        } catch (NotFoundException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong'));
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setRefererUrl();
    }
}

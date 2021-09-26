<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Extension\Status;

use Magento\Config\Model\Config;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\ObjectManagerInterface;
use Plumrocket\Base\Api\GetExtensionInformationInterface;

/**
 * @since 2.3.7
 */
class Disable
{
    /**
     * @var \Magento\Config\Model\Config
     */
    private $config;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var \Plumrocket\Base\Api\GetExtensionInformationInterface
     */
    private $getExtensionInformation;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param \Magento\Config\Model\Config                          $config
     * @param \Magento\Framework\App\ResourceConnection             $resourceConnection
     * @param \Plumrocket\Base\Api\GetExtensionInformationInterface $getExtensionInformation
     * @param \Magento\Framework\ObjectManagerInterface             $objectManager
     */
    public function __construct(
        Config $config,
        ResourceConnection $resourceConnection,
        GetExtensionInformationInterface $getExtensionInformation,
        ObjectManagerInterface $objectManager
    ) {
        $this->config = $config;
        $this->resourceConnection = $resourceConnection;
        $this->getExtensionInformation = $getExtensionInformation;
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $moduleName
     * @return bool
     */
    public function execute(string $moduleName): bool
    {
        // TODO: remove usage of 'disableExtension' after finished integration with all extensions
        $helper = $this->getHelper($moduleName);
        if ($helper && method_exists($helper, 'disableExtension')) {
            $helper->disableExtension();
            return true;
        }

        $configPath = $this->getExtensionInformation->execute($moduleName)->getIsEnabledFieldConfigPath();
        if (! $configPath) {
            return false;
        }

        $resource = $this->resourceConnection;
        $connection = $resource->getConnection('core_write');
        $connection->delete(
            $resource->getTableName('core_config_data'),
            [
                $connection->quoteInto('path = ?', $configPath)
            ]
        );
        $this->config->setDataByPath($configPath, 0);

        try {
            $this->config->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Receive data helper
     *
     * @param string $moduleName
     * @return \Magento\Framework\App\Helper\AbstractHelper|false
     */
    private function getHelper(string $moduleName)
    {
        $type = "Plumrocket\\{$moduleName}\Helper\Data";
        try {
            return $this->objectManager->get($type);
        } catch (\ReflectionException $reflectionException) {
            return false;
        }
    }
}

<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Extension\Information;

use Magento\Framework\DataObject;
use Plumrocket\Base\Api\Data\ExtensionInformationInterface;
use Plumrocket\Base\Api\GetModuleVersionInterface;

/**
 * Container for information about extension that parsed from "pr_extensions.xml"
 *
 * Can be used for old extension which has not xml file
 * and does not realize \Plumrocket\Base\Api\ModuleInformationInterface
 *
 * @since 2.5.0
 */
class Container extends DataObject implements ExtensionInformationInterface
{
    /**
     * @var \Plumrocket\Base\Api\GetModuleVersionInterface
     */
    private $getModuleVersion;

    /**
     * @param \Plumrocket\Base\Api\GetModuleVersionInterface $getModuleVersion
     * @param array                                          $data
     */
    public function __construct(
        GetModuleVersionInterface $getModuleVersion,
        array $data = []
    ) {
        parent::__construct($data);
        $this->getModuleVersion = $getModuleVersion;
    }

    /**
     * @inheritDoc
     */
    public function isService(): bool
    {
        return (bool) $this->_getData(self::FIELD_IS_SERVICE);
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return (string) $this->_getData(self::FIELD_TITLE);
    }

    /**
     * @inheritDoc
     */
    public function getWikiLink(): string
    {
        return (string) $this->_getData(self::FIELD_WIKI);
    }

    /**
     * @inheritDoc
     */
    public function getConfigSection(): string
    {
        return (string) $this->_getData(self::FIELD_CONFIG_SECTION);
    }

    /**
     * @inheritDoc
     */
    public function getIsEnabledFieldConfigPath(): string
    {
        return (string) $this->_getData(self::FIELD_IS_ENABLED_PATH);
    }

    /**
     * @inheritDoc
     */
    public function getModuleName(): string
    {
        return (string) $this->_getData(self::FIELD_MODULE_NAME);
    }

    /**
     * @inheritDoc
     */
    public function getVendorAndModuleName(): string
    {
        return "Plumrocket_{$this->getModuleName()}";
    }

    /**
     * @inheritDoc
     */
    public function getInstalledVersion(): string
    {
        return $this->getModuleVersion->execute($this->getVendorAndModuleName());
    }

    /**
     * @param bool $isService
     * @return \Plumrocket\Base\Api\Data\ExtensionInformationInterface
     */
    public function setIsService(bool $isService): ExtensionInformationInterface
    {
        return $this->setData(self::FIELD_IS_SERVICE, $isService);
    }

    /**
     * @param string $title
     * @return \Plumrocket\Base\Api\Data\ExtensionInformationInterface
     */
    public function setTitle(string $title): ExtensionInformationInterface
    {
        return $this->setData(self::FIELD_TITLE, $title);
    }

    /**
     * @param string $configSection
     * @return \Plumrocket\Base\Api\Data\ExtensionInformationInterface
     */
    public function setConfigSection(string $configSection): ExtensionInformationInterface
    {
        return $this->setData(self::FIELD_CONFIG_SECTION, $configSection);
    }

    /**
     * @param string $wikiLink
     * @return \Plumrocket\Base\Api\Data\ExtensionInformationInterface
     */
    public function setWikiLink(string $wikiLink): ExtensionInformationInterface
    {
        return $this->setData(self::FIELD_WIKI, $wikiLink);
    }

    /**
     * @param string $moduleName
     * @return \Plumrocket\Base\Api\Data\ExtensionInformationInterface
     */
    public function setModuleName(string $moduleName): ExtensionInformationInterface
    {
        return $this->setData(self::FIELD_MODULE_NAME, $moduleName);
    }

    /**
     * @param string $configPath
     * @return \Plumrocket\Base\Api\Data\ExtensionInformationInterface
     */
    public function setIsEnabledFieldConfigPath(string $configPath): ExtensionInformationInterface
    {
        return $this->setData(self::FIELD_IS_ENABLED_PATH, $configPath);
    }
}

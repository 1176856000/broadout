<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Base\Api\Data;

/**
 * Allows easily retrieve information about plumrocket module
 *
 * @since 2.5.0
 */
interface ExtensionInformationInterface
{
    const FIELD_IS_SERVICE = 'is_service';
    const FIELD_TITLE = 'title';
    const FIELD_WIKI = 'wiki';
    const FIELD_CONFIG_SECTION = 'config_section';
    const FIELD_IS_ENABLED_PATH = 'is_enabled_path';
    const FIELD_MODULE_NAME = 'module_name';

    /**
     * Some examples of services - Token, AmpEmailApi
     *
     * @return bool
     */
    public function isService(): bool;

    /**
     * Retrieve name of module, e.g. Twitter & Facebook Login
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Retrieve section in system settings, e.g. "pr_social_login"
     *
     * @return string
     */
    public function getConfigSection(): string;

    /**
     * Retrieve section in system settings, e.g. "pr_social_login/general/enabled"
     *
     * @return string
     */
    public function getIsEnabledFieldConfigPath(): string;

    /**
     * Link to wiki
     *
     * @return string
     */
    public function getWikiLink(): string;

    /**
     * Retrieve full name of module, e.g SocialLoginFree
     *
     * @return string
     */
    public function getModuleName(): string;

    /**
     * Retrieve full name of module, e.g Plumrocket_SocialLoginFree
     *
     * @return string
     */
    public function getVendorAndModuleName(): string;

    /**
     * Retrieve installed version by composer.json
     *
     * @return string
     */
    public function getInstalledVersion(): string;

    /**
     * @param bool $isService
     * @return \Plumrocket\Base\Api\Data\ExtensionInformationInterface
     */
    public function setIsService(bool $isService): ExtensionInformationInterface;

    /**
     * @param string $title
     * @return \Plumrocket\Base\Api\Data\ExtensionInformationInterface
     */
    public function setTitle(string $title): ExtensionInformationInterface;

    /**
     * @param string $configSection
     * @return \Plumrocket\Base\Api\Data\ExtensionInformationInterface
     */
    public function setConfigSection(string $configSection): ExtensionInformationInterface;

    /**
     * @param string $wikiLink
     * @return \Plumrocket\Base\Api\Data\ExtensionInformationInterface
     */
    public function setWikiLink(string $wikiLink): ExtensionInformationInterface;

    /**
     * @param string $moduleName
     * @return \Plumrocket\Base\Api\Data\ExtensionInformationInterface
     */
    public function setModuleName(string $moduleName): ExtensionInformationInterface;

    /**
     * @param string $configPath
     * @return \Plumrocket\Base\Api\Data\ExtensionInformationInterface
     */
    public function setIsEnabledFieldConfigPath(string $configPath): ExtensionInformationInterface;
}

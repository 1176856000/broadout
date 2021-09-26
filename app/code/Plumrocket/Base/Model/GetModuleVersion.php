<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Base\Model;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Config;
use Magento\Framework\App\Utility\Files;
use Magento\Framework\Component\ComponentRegistrar;
use Plumrocket\Base\Api\GetModuleVersionInterface;
use Plumrocket\Base\Model\Extension\GetModuleName;

/**
 * Reads version from composer.json
 *
 * @since 2.1.6
 */
class GetModuleVersion implements GetModuleVersionInterface
{
    const CACHE_IDENTIFIER = 'PR_EXTENSION_VERSION';

    /**
     * @var \Magento\Framework\App\Utility\Files
     */
    private $files;

    /**
     * For example [ Plumrocket_ModuleName => '2.1.3' ]
     *
     * @var string[]
     */
    private $versionsLocalCache = [];

    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    private $cache;

    /**
     * @var \Plumrocket\Base\Model\Extension\GetModuleName
     */
    private $getModuleName;

    /**
     * GetModuleVersion constructor.
     *
     * @param \Magento\Framework\App\Utility\Files           $files
     * @param \Magento\Framework\App\CacheInterface          $cache
     * @param \Plumrocket\Base\Model\Extension\GetModuleName $getModuleName
     */
    public function __construct(
        Files $files,
        CacheInterface $cache,
        GetModuleName $getModuleName
    ) {
        $this->files = $files;
        $this->cache = $cache;
        $this->getModuleName = $getModuleName;
    }

    /**
     * @param string $moduleName
     * @return string
     */
    public function execute($moduleName)
    {
        $moduleName = $this->getModuleName->execute($moduleName);

        if (! isset($this->versionsLocalCache[$moduleName]) || ! $this->versionsLocalCache[$moduleName]) {
            $moduleVersionsJson = $this->cache->load(self::CACHE_IDENTIFIER);
            if ($moduleVersionsJson) {
                $moduleVersions = (array) json_decode($moduleVersionsJson, true);
            } else {
                $moduleVersions = [];
            }

            if (! isset($moduleVersions[$moduleName])) {
                $modulePathName = "Plumrocket/$moduleName";
                $this->versionsLocalCache[$moduleName] = '';

                $composerFilePaths = $this->files->getComposerFiles(ComponentRegistrar::MODULE);

                $version = $this->getModuleVersionFromAppCode($composerFilePaths, $modulePathName);
                if ($version) {
                    $this->versionsLocalCache[$moduleName] = $version;
                } else {
                    $versions = $this->getModulesVersionFromVendor($composerFilePaths, 'plumrocket');
                    $this->versionsLocalCache = array_merge($this->versionsLocalCache, $versions);
                }

                $moduleVersions = array_merge($moduleVersions, $this->versionsLocalCache);

                $this->cache->save(
                    json_encode($moduleVersions),
                    self::CACHE_IDENTIFIER,
                    [Config::CACHE_TAG]
                );
            }

            $this->versionsLocalCache = $moduleVersions;
        }

        return $this->versionsLocalCache[$moduleName];
    }

    /**
     * @param array  $composerFilePaths
     * @param string $modulePathName
     * @return mixed|string
     */
    private function getModuleVersionFromAppCode(array $composerFilePaths, $modulePathName)
    {
        foreach ($composerFilePaths as $path => $absolutePath) {
            if (false !== strpos($path, "code/$modulePathName/composer.json")) {
                return $this->extractDataFromComposerJson($path)['version'];
            }
        }

        return '';
    }

    /**
     * @param array  $composerFilePaths
     * @param string $vendorName
     * @return mixed|string
     */
    private function getModulesVersionFromVendor(array $composerFilePaths, $vendorName)
    {
        $versions = [];
        foreach ($composerFilePaths as $path => $absolutePath) {
            if (false !== strpos($path, $vendorName)) {
                $data = $this->extractDataFromComposerJson($path);
                if ($data['version']) {
                    $versions[$data['name']] = $data['version'];
                }
            }
        }

        return $versions;
    }

    /**
     * @param string $path
     * @return array
     */
    public function extractDataFromComposerJson($path)
    {
        if (0 === strpos(trim($path, '/'), 'app')
            || 0 === strpos(trim($path, '/'), 'vendor')
        ) {
            $path = BP . DIRECTORY_SEPARATOR . trim($path, '/');
        }

        $content = file_get_contents($path);
        $result = [
            'version' => '',
            'name' => '',
        ];

        if ($content) {
            $jsonContent = json_decode($content, true);
            if (isset($jsonContent['version']) && ! empty($jsonContent['version'])) {
                $result['version'] = $jsonContent['version'];
            }
            if (isset($jsonContent['autoload']['psr-4']) && ! empty($jsonContent['autoload']['psr-4'])) {
                $directoryPath = trim(array_keys($jsonContent['autoload']['psr-4'])[0], '\\');
                $result['name'] = explode('\\', $directoryPath)[1];
            }
        }

        return $result;
    }
}

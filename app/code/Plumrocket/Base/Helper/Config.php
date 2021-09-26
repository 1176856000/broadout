<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Helper;

/**
 * @since 2.3.0
 */
class Config extends AbstractConfig
{
    const XML_PATH_NOTIFICATIONS_ENABLED = 'plumbase/notifications/enabled';
    const XML_PATH_NOTIFICATION_LISTS = 'plumbase/notifications/subscribed_to';
    const XML_PATH_IS_ENABLED_STATISTIC = 'plumbase/system/subscribed_to';

    /**
     * @param null $store
     * @param null $scope
     * @return bool
     */
    public function isModuleEnabled($store = null, $scope = null): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isEnabledNotifications(): bool
    {
        return (bool) $this->getConfig(self::XML_PATH_NOTIFICATIONS_ENABLED);
    }

    /**
     * @return array
     */
    public function getEnabledNotificationLists(): array
    {
        return $this->prepareMultiselectValue(
            (string) $this->getConfig(self::XML_PATH_NOTIFICATION_LISTS)
        );
    }

    /**
     * @return bool
     */
    public function isEnabledStatistic(): bool
    {
        return (bool) $this->getConfig(self::XML_PATH_IS_ENABLED_STATISTIC);
    }

    public function isDebugMode(): bool
    {
        return false;
    }
}

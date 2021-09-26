<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Base\Model\System\Config;

use Magento\Framework\Exception\NotFoundException;

/**
 * @since 2.5.0
 */
class GetCurrentExtensionName
{
    /**
     * @var \Plumrocket\Base\Model\System\Config\CurrentSection
     */
    private $currentSection;

    /**
     * @param \Plumrocket\Base\Model\System\Config\CurrentSection $currentSection
     */
    public function __construct(CurrentSection $currentSection)
    {
        $this->currentSection = $currentSection;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute(): string
    {
        if ($section = $this->currentSection->get()) {
            /** @var \Magento\Config\Model\Config\Structure\Element\Group $group */
            foreach ($section->getChildren() as $group) {
                if ($group->getId() !== 'general') {
                    continue;
                }
                /** @var \Magento\Config\Model\Config\Structure\Element\Field $field */
                foreach ($group->getChildren() as $field) {
                    if ($field->getId() !== 'version') {
                        continue;
                    }

                    // TODO: remove after changing system config in all modules
                    if (! $field->getAttribute('pr_extension_name')) {
                        $versionFiledData = $field->getData();
                        return explode('\\', $versionFiledData['frontend_model'])[1];
                    }

                    return $field->getAttribute('pr_extension_name');
                }
            }

            throw new NotFoundException(__('Not found version field'));
        }

        throw new NotFoundException(__('Not found section'));
    }
}

<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Affiliate\Model\PageType;

use Magento\Framework\Exception\NoSuchEntityException;
use Plumrocket\Affiliate\Api\Data\PageTypeInterface;
use Plumrocket\Affiliate\Api\Data\PageTypeInterfaceFactory;
use Plumrocket\Affiliate\Api\PageTypeProviderInterface;

/**
 * @since 3.0.0
 */
class Provider implements PageTypeProviderInterface
{
    /**
     * @var \Plumrocket\Affiliate\Api\Data\PageTypeInterface[]
     */
    private $pageTypes;

    /**
     * @var \Plumrocket\Affiliate\Api\Data\PageTypeInterfaceFactory
     */
    private $pageTypeFactory;

    /**
     * @param \Plumrocket\Affiliate\Api\Data\PageTypeInterfaceFactory $pageTypeFactory
     */
    public function __construct(PageTypeInterfaceFactory $pageTypeFactory)
    {
        $this->pageTypeFactory = $pageTypeFactory;
    }

    /**
     * @ingeritdoc
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(string $key): PageTypeInterface
    {
        if (! isset($this->getList()[$key])) {
            throw NoSuchEntityException::singleField('key', $key);
        }
        return $this->getList()[$key];
    }

    /**
     * @ingeritdoc
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $pageTypeId): PageTypeInterface
    {
        foreach ($this->getList() as $pageType) {
            if ($pageTypeId === $pageType->getId()) {
                return $pageType;
            }
        }

        throw NoSuchEntityException::singleField('key', $pageTypeId);
    }

    /**
     * @ingeritdoc
     */
    public function getList(): array
    {
        if (null === $this->pageTypes) {
            $this->pageTypes = [];
            foreach ($this->getConfig() as $typeKey => $typeData) {
                $this->pageTypes[$typeKey] = $this->createPageTypeInstance($typeKey, $typeData);
            }
        }
        return $this->pageTypes;
    }

    /**
     * @ingeritdoc
     */
    public function getConfig(): array
    {
        return [
            PageTypeInterface::ANY => [
                'id'    => 1,
                'name'  => 'All Pages',
                'order' => 10,
            ],
            PageTypeInterface::REGISTRATION_SUCCESS_PAGE => [
                'id'    => 2,
                'name'  => 'Registration Success Pages',
                'order' => 20,
            ],
            PageTypeInterface::LOGIN_SUCCESS_PAGE => [
                'id'    => 3,
                'name'  => 'Login Success Pages',
                'order' => 30,
            ],
            PageTypeInterface::HOME_PAGE => [
                'id'    => 4,
                'name'  => 'Home Page',
                'order' => 40,
            ],
            PageTypeInterface::PRODUCT_PAGE => [
                'id'    => 5,
                'name'  => 'Product Page',
                'order' => 50,
            ],
            PageTypeInterface::CATEGORY_PAGE => [
                'id'    => 6,
                'name'  => 'Category Page',
                'order' => 60,
            ],
            PageTypeInterface::CART_PAGE => [
                'id'    => 7,
                'name'  => 'Shopping Cart Page',
                'order' => 70,
            ],
            PageTypeInterface::ONE_PAGE_CHECKOUT => [
                'id'    => 8,
                'name'  => 'One Page Checkout',
                'order' => 80,
            ],
            PageTypeInterface::CHECKOUT_SUCCESS_PAGE => [
                'id'    => 9,
                'name'  => 'Order Success Page',
                'order' => 90,
            ],
            PageTypeInterface::SEARCH_RESULT_PAGE => [
                'id'    => 10,
                'name' => 'Search Result Page',
                'order' => 100
            ],
        ];
    }

    /**
     * @param string $pageTypeKey
     * @param array  $pageTypeData
     * @return \Plumrocket\Affiliate\Api\Data\PageTypeInterface
     */
    private function createPageTypeInstance(string $pageTypeKey, array $pageTypeData): PageTypeInterface
    {
        $pageTypeData['key'] = $pageTypeKey;
        return $this->pageTypeFactory->create(['data' => $pageTypeData]);
    }
}

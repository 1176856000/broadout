<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Affiliate\Api\Data;

/**
 * @since 2.8.0
 */
interface PageTypeInterface
{
    public const ANY = 'all';
    public const REGISTRATION_SUCCESS_PAGE = 'registration_success_pages';
    public const LOGIN_SUCCESS_PAGE = 'login_success_pages';
    public const HOME_PAGE = 'home_page';
    public const PRODUCT_PAGE = 'product_page';
    public const CATEGORY_PAGE = 'category_page';
    public const CART_PAGE = 'cart_page';
    public const ONE_PAGE_CHECKOUT = 'one_page_chackout';
    public const CHECKOUT_SUCCESS_PAGE = 'checkout_success';
    public const SEARCH_RESULT_PAGE = 'catalogsearch_result_page';

    public function getId(): int;

    /**
     * Retrieve page type key
     *
     * @return string
     */
    public function getKey(): string;

    /**
     * @return string
     */
    public function getName(): string;
}

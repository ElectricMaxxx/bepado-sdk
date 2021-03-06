<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * The SDK is licensed under MIT license. (c) Shopware AG and Qafoo GmbH
 */

namespace Bepado\SDK\Struct\SearchResult;

use Bepado\SDK\Struct;

/**
 * Struct class representing a product in a search result
 *
 * The SDK is licensed under MIT license. (c) Shopware AG and Qafoo GmbH
 * @api
 */
class Product extends Struct
{
    /**
     * Product title
     *
     * @var string
     */
    public $title;

    /**
     * Product short description
     *
     * @var string
     */
    public $shortDescription;

    /**
     * Product full description
     *
     * @var string
     */
    public $longDescription;

    /**
     * Product categories
     *
     * @var string[]
     */
    public $categories;

    /**
     * List of product image URLs
     *
     * @var string[]
     */
    public $images = array();

    /**
     * Lowest product price
     *
     * @var float
     */
    public $priceFrom;

    /**
     * Highest product price
     *
     * @var float
     */
    public $priceTo;

    /**
     * Product price currency
     *
     * @var string
     */
    public $currency;

    /**
     * Product availability
     *
     * @var int
     */
    public $availability;

    /**
     * Product language
     *
     * @var string
     */
    public $language;

    /**
     * Product vendor name
     *
     * @var string
     */
    public $vendor;

    /**
     * Product URL
     *
     * @var string
     */
    public $url;

    /**
     * Number of shops who sell this product.
     *
     * @var integer
     */
    public $shopCount;
}

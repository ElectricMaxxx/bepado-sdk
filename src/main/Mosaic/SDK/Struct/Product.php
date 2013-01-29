<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Struct;

use Mosaic\SDK\Struct;

/**
 * Struct class, representing products
 *
 * @version $Revision$
 * @api
 */
class Product extends Struct
{
    /**
     * Local ID of the product in your shop.
     *
     * ID should never change for one product or be reused for another product.
     *
     * @var string
     */
    public $sourceId;

    /**
     * URL to the product in your shop.
     *
     * Used for redirects to the product, or views of the product.
     *
     * @var string
     */
    public $url;

    /**
     * Title / name of the product
     *
     * @var string
     */
    public $title;

    /**
     * A short description of the product
     *
     * May contain simple HTML
     *
     * @var string
     */
    public $shortDescription;

    /**
     * An extensive / full description of the product
     *
     * May contain simple HTML
     *
     * @var string
     */
    public $longDescription;

    /**
     * Name of the product vendor
     *
     * @var string
     */
    public $vendor;

    /**
     * Current price of the product.
     *
     * Provided as a float.
     *
     * @var float
     */
    public $price;

    /**
     * Currency of the price
     *
     * Currently only the default "EUR" is supported.
     *
     * @var string
     */
    public $currency = "EUR";

    /**
     * Availibility of the product
     *
     * Provide an integer with the amount of products currently in stock and
     * ready for delivery.
     *
     * @var integer
     */
    public $availability;

    /**
     * List of product image URLs
     *
     * @var string[][]
     */
    public $images = array();

    /**
     * Product categories.
     *
     * For a full list of currently available product categories call
     * getCategories() on the SDK class.
     *
     * @var string[]
     */
    public $categories = array();

    /**
     * ID of the shop.
     *
     * Will be set by the SDK.
     *
     * @var string
     * @access private
     */
    public $shopId;

    /**
     * Product revision
     *
     * Will be set by the SDK.
     *
     * @var string
     * @access private
     */
    public $revisionId;

    /**
     * Restores a product from a previously stored state array.
     *
     * @param array $state
     * @return \Mosaic\SDK\Struct\Product
     */
    public static function __set_state(array $state)
    {
        return new Product($state);
    }
}

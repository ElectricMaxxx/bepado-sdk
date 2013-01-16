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
     * @var string
     * @access private
     */
    public $shopId;

    /**
     * @var string
     */
    public $sourceId;

    /**
     * @var string
     * @access private
     */
    public $revisionId;

    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $shortDescription;

    /**
     * @var string
     */
    public $longDescription;

    /**
     * @var string
     */
    public $vendor;

    /**
     * @var float
     */
    public $price;

    /**
     * @var string
     */
    public $currency;

    /**
     * @var integer
     */
    public $availability;

    /**
     * @var string[][]
     */
    public $images = array();

    /**
     * @var string[]
     */
    public $categories = array();

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

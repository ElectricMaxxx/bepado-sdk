<?php
namespace Mosaic\SDK\Struct;

use Mosaic\SDK\Struct;

class Product extends Struct
{
    /**
     * @var string
     */
    public $shopId;

    /**
     * @var string
     */
    public $sourceId;

    /**
     * @var string
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
     * @var byte[][]
     */
    public $images = array();

    /**
     * @var string[]
     */
    public $categories = array();
}

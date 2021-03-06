<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * The SDK is licensed under MIT license. (c) Shopware AG and Qafoo GmbH
 */

namespace Bepado\SDK\Struct;

use Bepado\SDK\Struct;

/**
 * Struct class representing a search
 *
 * The SDK is licensed under MIT license. (c) Shopware AG and Qafoo GmbH
 * @api
 */
class Search extends Struct
{
    /**
     * Search query
     *
     * May just be a string to search for. No special search syntax is
     * supported for now.
     *
     * @var string
     */
    public $query;

    /**
     * Result offset (used for paging)
     *
     * @var int
     */
    public $offset = 0;

    /**
     * Count of results to receive
     *
     * @var int
     */
    public $limit = 10;

    /**
     * Limit search to specified vendor
     *
     * @var mixed
     */
    public $vendor;

    /**
     * Minimum price opf search results
     *
     * @var float
     */
    public $priceFrom;

    /**
     * Maximum price of search results
     *
     * @var float
     */
    public $priceTo;
}

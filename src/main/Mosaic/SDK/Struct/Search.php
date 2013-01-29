<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Struct;

use Mosaic\SDK\Struct;

/**
 * Struct class representing a search
 *
 * @version $Revision$
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
     * API key of the shop executing the query;
     *
     * @var string
     */
    public $apiKey;

    /**
     * Result offset (used for paging)
     *
     * @var int
     */
    public $offset;

    /**
     * Count of results to receive
     *
     * @var int
     */
    public $limit;

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
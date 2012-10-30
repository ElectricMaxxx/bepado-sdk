<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Struct;

use Mosaic\Common\Struct;

/**
 * CHnage struct
 *
 * @version $Revision$
 */
class Change extends Struct
{
    /**
     * Product ID in source shop
     *
     * @var string
     */
    public $sourceId;

    /**
     * Revision of change
     *
     * @var float
     */
    public $revision;

    /**
     * Operation type
     *
     * One of "insert", "update", or "delete"
     *
     * @var string
     */
    public $operation;
}

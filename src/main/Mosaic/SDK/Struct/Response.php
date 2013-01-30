<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Struct;

use Mosaic\SDK\Struct;
use Mosaic\SDK\SDK;

/**
 * Struct class representing a message
 *
 * @version $Revision$
 * @api
 */
class Response extends Struct
{
    /**
     * Result of the response
     *
     * Can be about anything
     *
     * @var mixed
     */
    public $result;

    /**
     * Metrics
     *
     * Array of metrics, transmitted with the response. Will be logged by the
     * updater.
     *
     * @var array
     */
    public $metrics = array();

    /**
     * SDK Version
     *
     * @var string
     */
    public $sdkVersion = SDK::VERSION;
}

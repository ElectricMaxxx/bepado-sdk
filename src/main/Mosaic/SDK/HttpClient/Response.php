<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\HttpClient;

use Mosaic\SDK\Struct;

/**
 * Struct class representing a message
 *
 * @version $Revision$
 * @api
 */
class Response extends Struct
{
    /**
     * @var int
     */
    public $status;

    /**
     * @var string
     */
    public $headers;

    /**
     * @var string
     */
    public $body;
}

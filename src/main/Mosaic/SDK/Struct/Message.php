<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Struct;

use Mosaic\SDK\Struct;

/**
 * Struct class representing a message
 *
 * @version $Revision$
 * @api
 */
class Message extends Struct
{
    /**
     * @var string
     */
    public $message;

    /**
     * Message variables
     *
     * @var array
     */
    public $values = array();
}

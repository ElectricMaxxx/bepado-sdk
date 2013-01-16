<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Struct;

use Mosaic\SDK\Struct;

/**
 * Struct class for shop configurations
 *
 * @version $Revision$
 * @api
 */
class ShopConfiguration extends Struct
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $serviceEndpoint;

    /**
     * Restores a shop configuration from a previously stored state array.
     *
     * @param array $state
     * @return \Mosaic\SDK\Struct\ShopConfiguration
     */
    public static function __set_state(array $state)
    {
        return new ShopConfiguration($state);
    }
}

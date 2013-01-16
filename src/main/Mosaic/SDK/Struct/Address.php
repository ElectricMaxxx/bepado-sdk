<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Struct;

use Mosaic\SDK\Struct;

/**
 * Struct class representing an address
 *
 * @version $Revision$
 * @api
 */
class Address extends Struct
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $line1;

    /**
     * @var string
     */
    public $line2;

    /**
     * @var string
     */
    public $zip;

    /**
     * @var string
     */
    public $city;

    /**
     * @var string
     */
    public $state;

    /**
     * @var string
     */
    public $country;

    /**
     * Restores an address from a previously stored state array.
     *
     * @param array $state
     * @return \Mosaic\SDK\Struct\Address
     */
    public static function __set_state(array $state)
    {
        return new Address($state);
    }
}

<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * The SDK is licensed under MIT license. (c) Shopware AG and Qafoo GmbH
 */

namespace Bepado\SDK\Struct;

use Bepado\SDK\Exception\InvalidArgumentException;
use Bepado\SDK\Struct;

/**
 * Definition of Product Shipping rules.
 */
class ShippingRules extends Struct
{
    /**
     * Array of shipping rules
     *
     * @var array<ShippingRule>
     */
    public $rules = array();
}

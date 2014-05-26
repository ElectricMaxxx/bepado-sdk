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

    /**
     * Create a Shipping Rules Definition from a Google Merchant Feed string
     *
     * @param string $shipping
     * @throws \Bepado\SDK\Exception\InvalidArgumentException
     * @return \Bepado\SDK\Struct\ShippingRules
     */
    static function fromString($shipping)
    {
        if (empty($shipping)) {
            throw new InvalidArgumentException("Empty string passed as shipping information.");
        }

        $ruleParts = explode(",", $shipping);
        $rules = array();

        foreach ($ruleParts as $rule) {
            $rules[] = ShippingRule::fromString($rule);
        }

        return new self(array('rules' => $rules));
    }

    /**
     * Convert shipping rules into string representation.
     */
    public function __toString()
    {
        return implode(
            ',',
            array_map(
                function (ShippingRule $rule) {
                    return (string)$rule;
                },
                $this->rules
            )
        );
    }
}

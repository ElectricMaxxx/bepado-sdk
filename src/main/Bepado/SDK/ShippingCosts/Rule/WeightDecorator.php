<?php
/**
 * This file is part of the Bepado Common Component.
 *
 * The SDK is licensed under MIT license. (c) Shopware AG and Qafoo GmbH
 */

namespace Bepado\SDK\ShippingCosts\Rule;

use Bepado\SDK\ShippingCosts\Rule;
use Bepado\SDK\Struct;

/**
 * Only allows the shipping rule to match when its not more heavy than a max weight.
 *
 * Products without a weight are assumed to weigh next to nothing and are added as 0.
 * Weights have to be given to every product to make this rule useful.
 */
class WeightDecorator extends Rule
{
    /**
     * Maximum weight in kilograms (ex. 4.8).
     *
     * @var float
     */
    public $maxWeight;

    /**
     * @var \Bepado\SDK\ShippingCosts\Rule
     */
    public $delegatee;

    /**
     * Check if shipping cost is applicable to given order
     *
     * @param Struct\Order $order
     * @return bool
     */
    public function isApplicable(Struct\Order $order)
    {
        return
            $this->lessOrEqualMaximumWeight($order) &&
            $this->delegatee->isApplicable($order)
        ;
    }

    private function lessOrEqualMaximumWeight(Struct\Order $order)
    {
        $orderWeight = 0;

        foreach ($order->orderItems as $orderItem) {
            $product = $orderItem->product;

            if (!array_key_exists(Struct\Product::ATTRIBUTE_WEIGHT, $product->attributes)) {
                continue;
            }

            $orderWeight += $product->attributes[Struct\Product::ATTRIBUTE_WEIGHT] * $orderItem->count;
        }

        return $orderWeight <= $this->maxWeight;
    }

    /**
     * Get shipping costs for order
     *
     * Returns the net shipping costs.
     *
     * @param Struct\Order $order
     * @return Struct\Shipping
     */
    public function getShippingCosts(Struct\Order $order)
    {
        return $this->delegatee->getShippingCosts($order);
    }

    /**
     * If processing should stop after this rule
     *
     * @param Struct\Order $order
     * @return bool
     */
    public function shouldStopProcessing(Struct\Order $order)
    {
        return $this->delegatee->shouldStopProcessing($order);
    }
}

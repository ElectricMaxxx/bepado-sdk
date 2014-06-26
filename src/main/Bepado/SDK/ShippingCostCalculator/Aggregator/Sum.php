<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * The SDK is licensed under MIT license. (c) Shopware AG and Qafoo GmbH
 */

namespace Bepado\SDK\ShippingCostCalculator\Aggregator;

use Bepado\SDK\ShippingCostCalculator\Aggregator;
use Bepado\SDK\Struct\Order;
use Bepado\SDK\Struct\OrderItem;
use Bepado\SDK\Struct\Shipping;

class Sum extends Aggregator
{
    /**
     * Aggregate shipping costs
     *
     * Aggregate shipping costs of order items and return the sum of all
     * shipping costs.
     *
     * @param Order $order
     * @return Shipping
     */
    public function aggregateShippingCosts(Order $order)
    {
        // @TODO: Handle VAT correctly
        $vat = .19;

        $shipping = array_reduce(
            $order->orderItems,
            function (Shipping $shipping, OrderItem $orderItem) {
                $shipping->isShippable = $shipping->isShippable && $orderItem->shipping->isShippable;
                $shipping->shippingCosts += $orderItem->shipping->shippingCosts;
                $shipping->deliveryWorkDays = max(
                    $shipping->deliveryWorkDays,
                    $orderItem->shipping->deliveryWorkDays
                );

                // @TODO: How do we want to work with mismatching services? Is
                // it even possible to aggregate this?
                $shipping->service = $shipping->service ?: $orderItem->shipping->service;

                return $shipping;
            },
            new Shipping()
        );

        $shipping->grossShippingCosts = $shipping->shippingCosts * (1 + $vat);
        return $shipping;
    }
}

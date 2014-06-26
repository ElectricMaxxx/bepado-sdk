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

        $netShippingCosts = array_sum(
            array_map(
                function (OrderItem $orderItem) {
                    return $orderItem->shippingCosts;
                },
                $order->orderItems
            )
        );

        // @TODO Aggregate service and delivery time

        return new Shipping(array(
            'isShippable' => true,
            'shippingCosts' => $netShippingCosts,
            'grossShippingCosts' => $netShippingCosts * (1 + $vat),
        ));
    }
}

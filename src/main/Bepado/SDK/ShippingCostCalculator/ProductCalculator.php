<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * The SDK is licensed under MIT license. (c) Shopware AG and Qafoo GmbH
 */

namespace Bepado\SDK\ShippingCostCalculator;

use Bepado\SDK\ShippingCostCalculator;
use Bepado\SDK\ShippingRuleParser;
use Bepado\SDK\Struct\Order;
use Bepado\SDK\Struct\OrderItem;
use Bepado\SDK\Struct\ShippingRule;
use Bepado\SDK\Struct\Shipping;

/**
 * Calculate shipping costs based on product rules
 */
class ProductCalculator implements ShippingCostCalculator
{
    /**
     * Aggregate for products without specific rules
     *
     * @var ShippingCostCalculator
     */
    private $aggregate;

    public function __construct(ShippingCostCalculator $aggregate, ShippingRuleParser $parser = null, Aggregator $aggregator = null)
    {
        $this->aggregate = $aggregate;
        $this->parser = $parser ?: new ShippingRuleParser\Google();
        $this->aggregator = $aggregator ?: new Aggregator\Sum();
    }

    /**
     * Get shipping costs for order
     *
     * @param \Bepado\SDK\Struct\Order $order
     * @param string $type
     *
     * @return Struct\Shipping
     */
    public function calculateShippingCosts(Order $order, $type)
    {
        $productOrder = clone $order;
        $commonOrder = clone $order;

        $productOrder->orderItems = array_filter(
            $productOrder->orderItems,
            function (OrderItem $orderItem) {
                return (bool) $orderItem->product->shipping;
            }
        );

        $commonOrder->orderItems = array_filter(
            $commonOrder->orderItems,
            function (OrderItem $orderItem) {
                return !$orderItem->product->shipping;
            }
        );

        $isShippable = true;
        foreach ($productOrder->orderItems as $orderItem) {
            $rules = $this->parser->parseString($orderItem->product->shipping);

            $orderItem->shipping = new Shipping(array('isShippable' => false));
            foreach ($rules->rules as $rule) {
                if ($this->matchRule($rule, $order, $orderItem)) {
                    continue 2;
                }
            }

            $isShippable = false;
        }

        $order->shippingCosts = $this->aggregator->aggregateShippingCosts(
            array_merge(
                array_map(
                    function (OrderItem $orderItem) {
                        return $orderItem->shipping;
                    },
                    $productOrder->orderItems
                ),
                array($this->aggregate->calculateShippingCosts($commonOrder, $type))
            )
        );

        return $order->shippingCosts;
    }

    /**
     * Match rule
     *
     * Returns true, if rule processing shuld be stopped
     *
     * @param ShippingRule $rule
     * @param Order $order
     * @param OrderItem $orderItem
     * @return bool
     */
    protected function matchRule(ShippingRule $rule, Order $order, OrderItem $orderItem)
    {
        if (isset($rule->country) &&
            ($rule->country !== $order->deliveryAddress->country)) {
            return false;
        }

        if (isset($rule->zipRange) &&
            !fnmatch($rule->zipRange, $order->deliveryAddress->zip)) {
            return false;
        }

        if (isset($rule->region) &&
            ($rule->region !== $order->deliveryAddress->state)) {
            return false;
        }

        $orderItem->shipping = new Shipping(
            array(
                'service' => $rule->service,
                'deliveryWorkDays' => $rule->deliveryWorkDays,
                'isShippable' => true,
                'shippingCosts' => $rule->price * $orderItem->count,
            )
        );
        return true;
    }
}

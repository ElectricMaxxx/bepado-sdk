<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * The SDK is licensed under MIT license. (c) Shopware AG and Qafoo GmbH
 */

namespace Bepado\SDK\ShippingCostCalculator;

use Bepado\SDK\Gateway\ShippingCosts;
use Bepado\SDK\ShippingCostCalculator;
use Bepado\SDK\ShippingCosts\Rules;
use Bepado\SDK\Struct;
use Bepado\SDK\Struct\Order;
use Bepado\SDK\Struct\OrderItem;
use Bepado\SDK\Struct\Shipping;

/**
 * Calculate shipping costs based on rules from the gateway.
 */
class RuleCalculator implements ShippingCostCalculator
{
    /**
     * Shipping costs gateway
     *
     * @var Gateway\ShippingCosts
     */
    protected $shippingCosts;

    public function __construct(ShippingCosts $shippingCosts)
    {
        $this->shippingCosts = $shippingCosts;
    }

    /**
     * Get shipping costs for order
     *
     * @param \Bepado\SDK\Struct\Order $order
     * @param string $type
     *
     * @return \Bepado\SDK\Struct\Order
     */
    public function calculateShippingCosts(Order $order, $type)
    {
        $shippingCostRules = $this->getShippingCostRules($order, $type);
        $vat = $this->calculateVat($order, $shippingCostRules);

        $minShippingCosts = null;
        $minShippingCostValue = PHP_INT_MAX;
        foreach ($shippingCostRules as $shippingCostRule) {
            if ($shippingCostRule->isApplicable($order)) {
                $shippingCosts = $shippingCostRule->getShippingCosts($order);
                if ($shippingCosts->shippingCosts < $minShippingCostValue) {
                    $minShippingCosts = $shippingCosts;
                }
            }
        }

        if (!$minShippingCosts) {
            return new Shipping(
                array(
                    'isShippable' => false,
                )
            );
        }

        if ($shippingCostRules->vatConfig->isNet) {
            $minShippingCosts->grossShippingCosts = $minShippingCosts->shippingCosts * (1 + $vat);
        } else {
            $minShippingCosts->grossShippingCosts = $minShippingCosts->shippingCosts;
            $minShippingCosts->shippingCosts = $minShippingCosts->grossShippingCosts / (1 + $vat);
        }

        return $minShippingCosts;
    }

    /**
     * Get shipping cost rules for current order
     *
     * @param \Bepado\SDK\Struct\Order $order
     * @return Rule[]
     */
    protected function getShippingCostRules(Order $order, $type)
    {
        if (empty($order->providerShop) || empty($order->orderShop)) {
            throw new \InvalidArgumentException(
                "Order#providerShop and Order#orderShop must be non-empty ".
                "to calculate the shipping costs."
            );
        }

        foreach ($order->products as $orderItem) {
            if ($orderItem->product->shopId != $order->providerShop) {
                throw new \InvalidArgumentException(
                    "ShippingCostCalculator can only calculate shipping costs for " .
                    "products belonging to exactly one remote shop."
                );
            }
        }

        return $this->shippingCosts->getShippingCosts($order->providerShop, $order->orderShop, $type);
    }

    /**
     * Get maximum VAT of all products
     *
     * This seems to be a safe assumption to apply the maximum VAT of all
     * products to the shipping costs.
     *
     * @param \Bepado\SDK\Struct\Order $order
     * @param array|\Bepado\SDK\ShippingCosts\Rules $rules
     * @return float
     */
    protected function calculateVat(Order $order, $rules)
    {
        $vatMode = is_array($rules) ? Rules::VAT_MAX : $rules->vatMode;

        switch ($vatMode) {
            case Rules::VAT_MAX:
                return max(
                    array_map(
                        function (OrderItem $orderItem) {
                            return $orderItem->product->vat;
                        },
                        $order->orderItems
                    )
                );

            case Rules::VAT_DOMINATING:
                $prices = array();

                foreach ($order->orderItems as $orderItem) {
                    if (!isset($prices[(string)$orderItem->product->vat])) {
                        $prices[(string)$orderItem->product->vat] = 0;
                    }

                    $prices[(string)$orderItem->product->vat] += $orderItem->product->price * $orderItem->count;
                }

                arsort($prices);
                reset($prices);

                return key($prices);

            case Rules::VAT_PROPORTIONATELY:
                $totalPrice = 0;
                $vat = 0;

                if (count($order->orderItems) === 1) {
                    return $order->orderItems[0]->product->vat;
                }

                foreach ($order->orderItems as $orderItem) {
                    $totalPrice += $orderItem->product->purchasePrice * $orderItem->count;
                }

                foreach ($order->orderItems as $orderItem) {
                    $productPrice = $orderItem->product->purchasePrice * $orderItem->count;
                    $vat += ($productPrice / $totalPrice) * $orderItem->product->vat;
                }

                return $vat;

            case Rules::VAT_FIX:
                return $rules->vat;

            default:
                throw new \RuntimeException("Unknown VAT mode specified: " . $vatMode);
        }
    }
}
